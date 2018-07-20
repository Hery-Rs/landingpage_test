<?php
/**
 * Fonctions d'utilité générale 
 */
class PlanetHelpers
{
	/**
	 * Conversion d'une date au format européen en un datetime ISO (MySQL)
	 *
	 * @param	String	Date au format Européen (jj/mm/yyyy)
	 * 
	 * @return	String	Date au format ISO (yyyy-mm-jj)
	 */
	public static function dateEurToIso($dateEur)
	{
		return preg_replace('/([0-9]{2})[- \/.]([0-9]{2})[- \/.]([0-9]{4})/', '$3-$2-$1', $dateEur);
	} 
    
    /**
	 * Conversion d'une date au format ISO (MySQL) en une date au format européen 
	 *
	 * @param	String	Date au format ISO (yyyy-mm-jj)
	 * 
	 * @return	String	Date au format Européen (jj/mm/yyyy)
	 */
	public static function dateIsoToEur($dateIso)
	{
		return preg_replace('~^([0-9]{4})[- \/.]([0-9]{2})[- \/.]([0-9]{2})$~', '$3/$2/$1', $dateIso);
	}
    
	/**
	 * Destruction totale de la session en cours
	 * (nécessite un session_start() au préalable)
	 *
	 * @return void
	 */
	public static function destroySession()
	{
		// Destruction de toutes les variables de session
		$_SESSION = array();

		// Destruction du cookie de session
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}

		// Destruction de la session
		session_destroy();
	}
	
    /**
     * Prend en charge le déplacement d'un fichier envoyé par HTTP POST
     *
     * @param array     $file Informations sur le fichier uploadé (après filtrage, INDISPENSABLE)
     * @param string    $storageFileName [opt.] Nom définitif du fichier uploadé (par défaut 'Fichier_' + uniqid())
     * @param string    $uploadFolder [opt.] Chemin vers le répertoire de stockage définitif des fichiers uploadés (par défaut ce sera le répertoire courant)
     * @param bool      $denyUploadFolder [opt.] Mettre à true pour créer automatiquement un fichier .htaccess empêchant l'accès direct au dossier des fichiers uploadés (désactivé par défaut)
     * @param bool      $overwriteIfExisting [opt.] Mettre à true pour écraser un éventuel fichier existant portant le même nom (activé par défaut)
     *
     * @return mixed Le chemin vers le fichier uploadé si le déplacement a fonctionné, false sinon
     */
    public static function handleFile($file, $storageFileName = null, $uploadFolder = '.', $denyUploadFolder = false, $overwriteIfExisting = true)
    {
        $storageFileName = $storageFileName ? $storageFilename : 'Fichier_' . uniqid();

        if(is_array($file))
        {
            if($file != array())
            {
                // Un fichier a bien été sélectionné dans le champ de type file

                // Traitement du fichier

                $newFilePath = $uploadFolder . '/' . $storageFileName . '.' . $file['extension'];

                if(!$overwriteIfExisting && is_file($newFilePath))
                {
                    // Si un fichier du même nom existe déjà et qu'on ne souhaite pas l'écraser

                    // Le traitement est terminé (avec succès)
                    return true;
                }

                if(!is_dir($uploadFolder))
                {
                    // Si le répertoire d'upload n'existe pas encore on le créé
                    mkdir($uploadFolder, 0755, true);
                }

                if($denyUploadFolder && !is_file($uploadFolder . "/.htaccess"))
                {
                    // Si on souhaite empêcher l'accès direct aux fichiers uploadés
                    // Et si le fichier .htaccess qui empêche l'accès au dossier n'existe pas

                    // Création du fichier .htaccess
                    file_put_contents($uploadFolder . "/.htaccess", "Order Deny,Allow\nDeny from all");
                }

                // Déplacement du fichier depuis le dossier temporaire vers le dossier définitif et modification des droits d'accès au fichier
                move_uploaded_file($file['tmp_name'], $newFilePath);
                chmod($newFilePath, 0644);

                return $newFilePath;
            }
        }

        return false;
    }
    
    /**
     * Redirection vers une URL et mise en session d'un message accompagné de son type
     * 
     * @param string $url Adresse de redirection
     * @param string $message Message (à afficher)
     * @param string $messageType Type du message
     * @param string $namespace Nom du tableau qui contiendra les données de session
     */
    public static function redirect($url, $message = '', $messageType = '', $namespace = '')
    {
        if(!empty($message)) $_SESSION[$namespace]['message'] = $message;
        if(!empty($messageType)) $_SESSION[$namespace]['messageType'] = $messageType;
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Suppression d'un dossier et de son contenu de manière récursive (non à ce jour ça n'existe pas en PHP, it's a shame...)
     * 
     * @param string $dir Chemin absolu vers le dossier à supprimer
     * 
     * @return void
     */
    public static function removeDirectory($dir)
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);

            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (filetype($dir . "/" . $object) == "dir")
                    {
                        rrmdir($dir . "/" . $object);
                    }
                    else 
                    {
                        unlink($dir . "/" . $object);
                    }
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }
    
    /**
     * Mise en session des données valides saisies par l'utilisateur dans un formulaire
     * 
     * @param string $form_id Identifiant du formulaire dont proviennent les données
     * @param Mixed $donnees Filtre utilisé pour traiter les données du formulaire ou tableau associatif contenant les données à sauvegarder
     * 
     * @return void
     */
    function saveFormData($form_id, $donnees)
    {
        if($donnees instanceof PlanetFilter)
        {
            foreach($donnees->donneesFiltrees as $donnee => $valeur)
            {
                $_SESSION[$form_id][$donnee] = $donnees->get($donnee);
            }

            // Champs qui contiennent des données invalides ou qui n'ont pas été remplis
            if(!empty($donnees->donneesInvalides))
                $_SESSION[$form_id]['error_fields'] = $donnees->donneesInvalides;
        }
        else
        {
            foreach($donnees as $donnee => $valeur)
            {
                $_SESSION[$form_id][$donnee] = $valeur;
            }
        }
    }
    
    /**
     * Truncates text. (Fonction géniale issue du framework PHP)
     *
     * Cuts a string to the length of $length and replaces the last characters
     * with the ending if the text is longer than length.
     *
     * @param string  $text String to truncate.
     * @param integer $length Length of returned string, including ellipsis.
     * @param string  $ending Ending to be appended to the trimmed string.
     * @param boolean $exact If false, $text will not be cut mid-word
     * @param boolean $considerHtml If true, HTML tags would be handled correctly
     * @return string Trimmed string.
     */
    public static function truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                    // if tag is a closing tag (f.e. </b>)
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                    // if tag is an opening tag (f.e. <b>)
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length+$content_length> $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1]+1-$entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if($total_length>= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
        return $truncate;
    }
}