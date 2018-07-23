<?php
// No direct access
//defined('_JEXEC') or die;

class PlanetFilter
{
	/**
	 * Filtres prédéfinis
	 * 
	 * @var array
	 */
	public static $filtres = array(
		
		/**
		 * Code postal
		 * 
		 * Valeurs acceptées : tous les codes postaux français
		 */
		"code_postal" => array(
			"filter" => FILTER_VALIDATE_REGEXP,
			"options" => array(
				"regexp" => "/^((0[1-9])|([1-8][0-9])|(9[0-8])|(2A)|(2B))[0-9]{3}$/"
				/* 
				Un OU deux caractères :
				 - de 1 à 9
				 - OU de 01 à 09
				 - OU de 10 à 89 
				 - OU de 90 à 98 
				 - OU 2A
				 - OU 2B 
				
				Suivis de trois caractères numériques de 000 à 999  
				*/
			)
		),
		
		/**
		 * Civilité
		 * 
		 * Valeurs acceptées : "M", "Mr", "Mme", "Mlle", "M.", "Mr.", "Mme.", "Mlle.", "Monsieur", "Madame", "Mademoiselle"
		 */
		"civilite" => array(
			"filter" => FILTER_VALIDATE_REGEXP,
			"options" => array(
				"regexp" => "/(Mme|M.)/i"
			)
		),
		
		/**
		 * Filtre par défaut (nettoyage de la chaine de caractères uniquement)
		 */
		"default" => array(
			"filter" => FILTER_SANITIZE_STRING,
			"flags" => FILTER_FLAG_NO_ENCODE_QUOTES
		),
		
		/**
		 * Email 
		 * 
		 * Valeurs acceptées : toutes les adresses email syntaxiquement valides
		 */
		"email" => array(
			"filter" => FILTER_VALIDATE_EMAIL
		),
		
		/**
		 * Confirmation d'email
		 * 
		 * Valeurs acceptées : toutes les adresses email syntaxiquement valides
		 */
        /*
		"email_confirmation" => array(
			"filter" => FILTER_VALIDATE_EMAIL
		),
		*/
        "email_confirmation" => array(
            "filter" => FILTER_CALLBACK,
			"options" => array("self", "validateEmailConfirmation")
        ),
        
		/**
		 * Téléphone
		 * 
		 * Valeurs acceptées :
		 * - Chiffres de 0 à 9
		 * - Parenthèses
		 * - Plus / Moins (tiret)
		 * - Point
		 * - Espace
		 * 
		 * Minimum 10 caractères
		 */
		"fax" => array(
			"filter" => FILTER_VALIDATE_REGEXP,
			"options" => array(
				"regexp" => "/^[0-9()+-. ]{10,}$/"
			)
		),
		
		/**
		 * Fax (ancienne version)
		 * 
		 * Suppression de tous les caractères qui ne sont pas dans la liste suivante :
		 * - Chiffres de 0 à 9
		 * - Parenthèses
		 * - Plus / Moins (tiret)
		 * - Point
		 * - Espace
		 */
		/*
		"fax" => array(
			"filter" => FILTER_CALLBACK,
			"options" => array("self", "sanitizePhoneNumber")
		),
		*/
		
		/**
		 * Numéro SIRET
		 * 
		 * Valeurs acceptées : Trois séries de trois chiffres suivies ou non par un tiret ou un espace puis une série de cinq chiffres
		 */
		"siret" => array(
			"filter" => FILTER_VALIDATE_REGEXP,
			"options" => array(
				"regexp" => "/^([0-9]{3}( |-)?){3}[0-9]{5}$/"
			)
		),
		
		/**
		 * Téléphone
		 * 
		 * Valeurs acceptées :
		 * - Chiffres de 0 à 9
		 * - Parenthèses
		 * - Plus / Moins (tiret)
		 * - Point
		 * - Espace
		 * 
		 * Minimum 10 caractères
		 */
		"telephone" => array(
			"filter" => FILTER_VALIDATE_REGEXP,
			"options" => array(
				"regexp" => "/^[0-9()+-. ]{10,}$/"
			)
		),
		
		/**
		 * Téléphone (ancienne version)
		 * 
		 * Suppression de tous les caractères qui ne sont pas dans la liste suivante :
		 * - Chiffres de 0 à 9
		 * - Parenthèses
		 * - Plus / Moins (tiret)
		 * - Point
		 * - Espace
		 */
		/*
		"telephone" => array(
			"filter" => FILTER_CALLBACK,
			"options" => array("self", "sanitizePhoneNumber")
		)
		*/
	);
	
	/**
	 * Détection automatique des filtres à appliquer à partir des noms des variables reçues (en "post" ou en "get") avec la requête HTTP
	 * 
	 * @var bool
	 */
	public $detectionAuto = true;
	
    /**
	 * Données "unsecure" provenant directement de la requête
	 * 
	 * @var array
	 */
	public $donneesNonFiltrees = array();
    
	/**
	 * Données "secure" obtenues après application de filtres
	 * 
	 * @var array
	 */
	public $donneesFiltrees = array();
	
	/**
	 * Noms des données invalides suite au filtrage
	 * 
	 * @var array
	 */
	public $donneesInvalides = array();
	
	/**
	 * Liste des types MIME associés aux extensions les plus courantes
	 */
	private static $extensionsMimeTypes = array(
		'jpg'   => array('image/jpeg', 'image/pjpeg'),
		'jpeg'  => array('image/jpeg', 'image/pjpeg'),
		'png'   => array('image/png'),
		'rtf'   => array('application/rtf', 'application/x-rtf', 'text/richtext', 'text/rtf'),
        'pdf'   => array('application/pdf'),
        'doc'   => array('application/msword'),
        'docx'  => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'),
        'pps'   => array('application/vnd.ms-powerpoint'),
        'ppsx'  => array('application/vnd.openxmlformats-officedocument.presentationml.slideshow', 'application/zip'),
        'ppt'   => array('application/vnd.ms-powerpoint'),
        'pptx'  => array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'),
        'xls'   => array('application/vnd.ms-excel'),
        'xlsx'  => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip')
	);

	/**
	 * Par défaut on force la validation des données facultatives
	 * 
	 * Concrètement cela veut dire que si une donnée facultative est fournie mais invalide le filtrage échouera.
	 * Dans le cas opposé la donnée invalide sera remplacée par une chaîne de longueur 0 mais le filtrage n'échouera pas.
	 * 
	 * @var bool
	 */
	public $forcerValidation = true;
	
    /**
     * Type de la requête dont proviennent les données à filtrer ('get' ou 'post')
     * 
     * @var string
     */
    public $requestType = "";
    
	/**
	 * Indique si le filtrage des données s'est déroulé avec succès ou non
	 * 
	 * @var bool
	 */
	public $succesFiltrage = false;
	
	
	
	/**
	 * Filtrage de données provenant d'une requête HTTP
	 * 
	 * @param string $requestType Type de requête, soit "get", soit "post" (insensible à la casse)
	 * @param array $optionalData [optionnel] Données facultatives
	 * @param array $dataToFilter [optionnel] Données à filtrer (toutes les données dont le nom ne figure pas dans ce tableau seront ignorées)
	 * @param array $filters [optionnel] Tableau de filtres à appliquer seuls ou en complément des filtres trouvés automatiquement (formattage du tableau identique à celui requis par la fonction filter_input_array())
	 * 
	 * @return bool true si la méthode s'est bien exécutée, false en cas d'erreur (ex: type de requête invalide)
	 */
	public function filtrer($requestType, $optionalData = null, $dataToFilter = null, $filters = null)
	{
		// Mise en place des valeurs par défaut
		$optionalData = $optionalData ? $optionalData : array();
		$dataToFilter = $dataToFilter ? $dataToFilter : array();
		$filters = $filters ? $filters : array();
		
		// Validation du type de la requête
		$requestType = $this->validateRequestType($requestType);
		
		if(empty($requestType))
		{
			// Le type de la requête est invalide on s'arrête ici
			return false;
		}
		
		// Récupération des types de filtres à appliquer à partir de la requête et/ou des filtres passés en paramètre
		$filtersToApply = $this->getFilters($requestType, $filters);
		
		if(!empty($filtersToApply))
		{
			// Des filtres à appliquer on été trouvés
			
            // Stockage des données non filtrées
            $this->donneesNonFiltrees = $GLOBALS["_" . strtoupper($requestType)];
            
			// Application des filtres
			$this->donneesFiltrees = filter_input_array(constant("INPUT_" . strtoupper($requestType)), $filtersToApply);
			
			foreach($this->donneesFiltrees as $data => $value)
			{
				if(empty($dataToFilter) || in_array($data, $dataToFilter))
				{
					// Si les données à filtrer n'ont pas été indiquées explicitement OU si la donnée courante fait partie des données à filtrer
					
					if($value == "")
					{
						// La valeur de la donnée courante était vide ou elle a été vidée (parce qu'elle ne satisfaisait pas aux critères du filtre)
						
						if(!in_array($data, $optionalData))
						{
							// La donnée courante est obligatoire on la mémorise en tant que donnée erronée
							$this->donneesInvalides[] = $data;
						}
						else
						{
							// La donnée est facultative
							
							$requestVars = $GLOBALS["_" . strtoupper($requestType)];
							
							if(trim($requestVars[$data]) != "" && $this->forcerValidation)
							{
								// La valeur non filtrée de la donnée n'est pas vide et la validation des données facultatives est forcée (comportement par défaut)
								
								// On mémorise la donnée courante en tant que donnée erronée
								$this->donneesInvalides[] = $data;
							}
						}
					}
				}
			}
			
			if(!$this->donneesInvalides)
			{
				$this->succesFiltrage = true;
			}
		}
		else
		{
			// Aucun filtre à appliquer n'a été trouvé
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Vérifie si un fichier uploadé est légitime ou non
	 *
	 * Permet de repousser les attaques de hackers débutants ou vraiment mauvais mais difficile de faire mieux (pour ce qui est de l'analyse du fichier)
	 * Le plus gros du travail est à réaliser au niveau de la manière de traiter le fichier uploadé (renommage, empêcher son exécution etc)
	 *
	 * @param string $inputName Nom du champ input de type "file"
	 * @param array $validExtensions Liste des extensions autorisées pour le fichier uploadé (sans le point devant)
	 * @param int $maxFileSize Taille maximale de fichier autorisée pour le fichier uploadé (en octets => pour des Mo multiplier le nombre de Mo souhaité par 1024 deux fois de suite)
	 * @param bool $optional [optionnel] Indique si le fichier est optionnel (s'il l'est, son absence ne déclenchera pas d'erreur)
	 *
	 * @return mixed L'entrée du tableau $_FILES correspondant au fichier s'il semble légitime, un message d'erreur dans le cas contraire
	 */
	public function filtrerFichier($inputName, $validExtensions, $maxFileSize = 2097152, $maxFilenameSize = 255, $optional = false)
	{
		if(!isset($_FILES[$inputName]))
		{
			// Le fichier n'a pas été trouvé dans le tableau $_FILES

			$error_message = "Aucun fichier envoyé";
		}
		else
		{
			$file = $_FILES[$inputName];

			if($file["error"] === 4)
			{
				// Aucun fichier n'a été sélectionné dans l'input de type "file"

                if($optional)
                {
                    // Le fichier est déclaré comme étant optionnel
                    
                    // On retourne un tableau (vide) pour signifier qu'il n'y a pas d'erreur
                    return array();
                }
                else
                {
                    // Le fichier n'est pas optionnel
                    
                    $error_message = "Aucun fichier envoyé";
                }
			}
			else
			{
				$fileExtension = pathinfo($file["name"], PATHINFO_EXTENSION);

				$error_message = "";

				if(empty($validExtensions))
				{
					// Le tableau des extensions autorisées n'a pas fourni (ou il est vide)

					$error_message = "Extensions autorisées non définies.";
				}
				else if($file == "" || $file["error"] != UPLOAD_ERR_OK || $file["tmp_name"] == "none" || !is_uploaded_file($file["tmp_name"]))
				{
					// Erreurs diverses

					$error_message = "Fichier invalide";
				}
                else if(!in_array($fileExtension, $validExtensions))
				{
					// L'extension du fichier ne fait pas partie des extensions tolérées

					$error_message = "Type de fichier non autorisé. Extensions autorisées : " . implode(', ', $validExtensions) . ".";
				}
				else if(!in_array($fileExtension, array_keys(self::$extensionsMimeTypes)))
				{
					// L'extension du fichier n'est pas déclarée dans le tableau qui associe les extensions de fichier aux types MIME

					$error_message = "Type MIME du fichier non reconnu.";
				}
                else if(!in_array(self::getMimeType($file["tmp_name"]), self::$extensionsMimeTypes[$fileExtension]))
				{
					// Le type MIME du fichier ne correspond pas à son extension

					$error_message = "Le type MIME du fichier ne correspond pas à son extension.";
				}
				else if($file["size"] <= 0 || $file["size"] > $maxFileSize)
				{
					// Taille de fichier nulle ou supérieure à la taille maximale autorisée
                    
                   $maxFileSizeMo = ($maxFileSize/1024)/1024;
                   $maxFileSizeMo = round($maxFileSizeMo, 1);
                   
                   $error_message = "Le poids du fichier ne doit pas excéder " . $maxFileSizeMo . " Mo.";
				}
				else if(strlen($file["name"]) > $maxFilenameSize || preg_match("~(\.\.|/|\\\| )~", $file["name"]))
				{
					// Le nom de fichier comprend au moins une fois la chaine "..", "/", "\" ou " "

					$error_message = "Le nom de fichier dépasse " . $maxFilenameSize . " caractères ou contient des caractères non autorisés.";
				}
				else
				{
					$file["extension"] = $fileExtension;
					return $file;
				}
			}
		}

		return $error_message;
	}

	/**
	 * Retourne une donnée filtrée (sécurisée)
	 * 
	 * @param string $data Nom de la donnée
	 * @param mixed $default Donnée à retourner si celle qui est demandée n'existe pas
	 * 
	 * @return mixed donnée demandée
	 */
	public function get($data, $default = "")
	{
		if(isset($this->donneesFiltrees[$data]))
			return $this->donneesFiltrees[$data];
		else
			return $default;
	}
	
	/**
	 * Retourne les filtres à appliquer en tenant compte des différents paramètres passés
	 * 
	 * A noter que les filtres qui sont directement passés à la fonction dans le tableau $filters sont prioritaires sur (et de ce fait écrasent) les filtres détectés
	 * Si la détection des filtres est désactivée ($this->detectionAuto = false), seuls les filtres contenus dans $filters seront utilisés
	 * 
	 * @param string $requestType Type de requête, soit "get", soit "post" (insensible à la casse)
	 * @param array $filters Tableau de filtres à appliquer seuls ou en complément des filtres trouvés automatiquement (formattage du tableau identique à celui requis par la fonction filter_input_array())
	 * 
	 * @return array Filtres à appliquer (tableau directement utilisable par la fonction filter_input_array())
	 */
	private function getFilters($requestType, $filters)
	{
		// Validation du type de la requête
		$requestType = $this->validateRequestType($requestType);
		
		if(empty($requestType))
		{
			// Le type de la requête est invalide on s'arrête ici
			return false;
		}
		
		$requestVars = $GLOBALS["_" . strtoupper($requestType)];
		
		$filtersToApply = array();
		
		// Détection automatique des filtres à partir du nom de chaque variable reçue
		if($this->detectionAuto)
			$filtersToApply = array_intersect_key(self::$filtres, $requestVars);
		
		// Ajout des filtres éventuellement passés en paramètre
		if(!empty($filters))
		{
			foreach($filters as $data => $filter)
			{
				$filtersToApply[$data] = $filter;
			}
		}
		
		// Ajout d'un filtre par défaut pour les champs qui ne sont ni filtrés automatiquement ni manuellement
		$notFilteredYet = array_diff_key($requestVars, $filtersToApply);
		
		foreach($notFilteredYet as $data => $value)
		{
			$filtersToApply[$data] = self::$filtres["default"];
		}
		
		return $filtersToApply;
	}
	
	/**
     * Wrapper qui permet de récupérer le type MIME d'un fichier de différentes manières selon les fonctions disponibles sur le serveur
     *
     * @static
     * @param string $filepath Chemin absolu vers le fichier
     *
     * @return void
     */
    public static function getMimeType($filepath)
    {
        $mimeType = "";
        
        if(function_exists("finfo_open"))
        {
            // Si la fonction finfo_open() existe
            // Cela veut dire qu'on est en PHP 5.3 ou que l'extension PECL a été installée

            // Objet "ressource" qui permet d'accéder à la base de données "magic" qui permet de détecter les types de fichier
			$finfo = finfo_open(FILEINFO_MIME_TYPE);

			$mimeType = finfo_file($finfo, $filepath);
        }
        else if($fileInfo = exec("file -i " . $filepath))
        {
            // La fonction finfo_open() n'existe pas mais la commande shell file -i fonctionne
            
			$output = array();
			$fileInfo = exec("file -i " . $filepath, $output);
			$matches = array();
			preg_match("/: (.*);?/", $output[0], $matches);
			$mimeType = $matches[1];
        }
        
		return $mimeType;
    }

	/**
	 * Nettoie un numéro de téléphone
	 * 
	 * @param string $string Numéro de téléphone à nettoyer
	 * 
	 * @return string Le numéro de téléphone dont on aura supprimé les caractères qui ne sont pas dans la liste suivante :
	 * 	- chiffres de 0 à 9,
	 *  - parenthèse,
	 *  - plus,
	 *  - moins,
	 *  - point,
	 *  - espace
	 */
	public static function sanitizePhoneNumber($string)
	{
		return preg_replace("/[^0-9()+-. ]/", "", $string);
	}
	
    private function validateEmailConfirmation($email_confirmation)
    {
        if(filter_var($email_confirmation, FILTER_VALIDATE_EMAIL) 
           && isset($this->donneesNonFiltrees['email']) 
           && $email_confirmation == $this->donneesNonFiltrees['email'])
        {
            return $email_confirmation;
        }
        
        return false;
    }
    
	/**
	 * Vérifie qu'un type de requête demandé existe bien
	 * 
	 * Types de requête valides : "get", "post" (insensible à la casse)
	 * 
	 * @param string $requestType
	 * 
	 * @return string La variable $requestType si elle est valide, une chaîne de longueur 0 dans le cas contraire
	 */
	private function validateRequestType($requestType)
	{
		$requestType = strtolower($requestType);
		
		$requestTypes = array("get", "post");
		
		if(!in_array($requestType, $requestTypes))
		{
			$requestType = "";
		}
		
		return $requestType;
	}
}