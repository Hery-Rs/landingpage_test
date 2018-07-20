<?php
/**
 * Classe de connexion à une base de données via PDO
 * Cette classe est dépendante du fichier de configuration "configuration.php"
 */

require_once("planet_config.php");	// récupération des infos de connexion à la base de données

class PlanetDb
{
	// stockage de la connexion à la base de données
	 public $connexion;
	 
	 // Equivalence jeux de caractère HTML <=> MySQL
	 public $mysql_character_sets = array(
                                    "UTF-8" => "UTF8",
                                    "ISO-8859-1" => "LATIN1",
                                    "ISO-8859-2" => "LATIN2",
                                    "ISO-8859-15" => "LATIN9"
	);
   
	 /**
	  * Effectue la connexion à la base de données puis la stocke dans l'attribut connexion
	  *
	  * @return void
	  */
	 public function __construct($collation = "utf-8")
	 {	
        $conf = new PlanetConfig();
         
	 	switch($conf->driver)
	 	{
	 		case "db2" :
	 			// Au cas où on voudrait utiliser db2 par exemple
				break;
	 		case "mysql" :
	 		default :
				try
				{
					$this->connexion = new PDO(
											"mysql:dbname=" . $conf->mysqlDbName . ";host=" . $conf->mysqlHost,
											$conf->mysqlUser,
											$conf->mysqlPass
					);
					
					$this->connexion->query("SET NAMES " . $this->mysql_character_sets[strtoupper($collation)]);
				}
	 			catch(PDOException $e)
				{
					die("Erreur de connexion à la base de données."); //.$e->getMessage());
				}
	 			break;
	 	}
	 }
	 
	 /**
	  * Suppression d'un enregistrement
	  * 
	  * @param string $table Nom de la table concernée
	  * @param string $primary_key Nom de la clé primaire
	  * @param mixed $primary_value Valeur de la clé primaire correspondant à l'enregistrement que l'on veut supprimer
	  */
	 public function delete($table, $primary_key, $primary_value)
	 {
	 	$query = " DELETE FROM `" . $table . "`"
	 		   . " WHERE `" . $primary_key . "` = :primary_value";
	 		   
	 	$stmt = $this->connexion->prepare($query);
	 	$res = $stmt->execute(array(":primary_value" => $primary_value));
		
	 	return ($res && $stmt->rowCount());
	 }
	 
     /**
	 * Génération d'un export d'une table au format .csv
     *
     * @param string $table Nom de la table dont on veut exporter les enregistrements
     * @param array  [opt.] $ignoreFields Tableau de noms de champs qu'on ne veut pas exporter
     * @param string [opt.] $filename Nom du fichier d'export (default = '[nom-site]_[nom-table]_[date].csv')
     *  
     * @return void
	 */
    public function exportTable($table, $ignoreFields = array(), $filename = '')
    {
        $conf = new PlanetConfig();
        
        if(empty($filename))
            $filename = strtolower(str_replace(' ', '-', $conf->siteName)) . '_' . $table . '_' . date("d/m/Y") . ".csv";
        
	 	$res = self::fetchAll($table, 'array');
        
        header("Content-Type: application/csv-tab-delimited-table");
        header("Content-disposition: : attachment; filename=" . $filename);

        if(count($res) > 0)
        {
            // S'il y a au moins un enregistrement

            // Titres des colonnes
            foreach(array_keys($res[0]) as $fieldname)
            {
                if(!in_array($fieldname, $ignoreFields))
                    echo "\"" . $fieldname ."\";";
            }
            echo "\r\n";

            // Données de la table
            foreach($res as $index => $row)
            {
                foreach($row as $fieldname => $value)
                {
                    if(!in_array($fieldname, $ignoreFields))
                        echo "\"" . utf8_decode(str_replace(array("\r\n", ";"), array(" ", ","), $value)) . "\";";
                }
                echo "\r\n";
            }
        }
    }
     
    /**
    * Récupération d'un enregistrement dans une table grâce à sa clé primaire
    * 
    * @param string $table Table concernée
    * @param string $primary_key Nom de la clé primaire de la table
    * @param mixed $primary_value Valeur de la clé primaire pour l'enregistrement recherché
    * 
    * @return object Enregistrement recherché
    */
    public function fetch($table, $primary_key, $primary_value, $all=false)
    {
        $query = " SELECT * FROM `" . $table . "`"
                ." WHERE `" . $primary_key . "` = :primary_value";

        $stmt = $this->connexion->prepare($query);
        $stmt->execute(array(":primary_value" => $primary_value));

        return $all ? $stmt->fetchAll() : $stmt->fetchObject();
    }

    /**
    * Récupération de tous les enregistrements d'une table
    * 
    * @param string $table Table dont on veut récupérer les enregistrements
    * @param string $fetchMode Type de variable pour le jeu de résultats retourné :
    *   - PDO::FETCH_ASSOC : Tableau associatif
    *   - PDO::FETCH_OBJ : Objet (stdClass)
    * 
    * @return array Tableau d'objets
    */
    public function fetchAll($table, $fetchMode = 'object')
    {
        $fetchModes = array(
            'object' => PDO::FETCH_OBJ,
            'array' => PDO::FETCH_ASSOC
        );
        
        $query = "SELECT * FROM `" . $table . "`";

        $stmt = $this->connexion->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll($fetchModes[$fetchMode]);
    }

    public function getErrorMessage($stmt = null)
    {
        if(!$stmt)
        {
        $error = $this->connexion->errorInfo();
        }
        else
        {
        $error = $stmt->errorInfo();
        }

        return $error[2];
    }

    /**
    * Construction d'une requête SQL d'insertion en fonction des champs donnés
    * 
    * @param string $table Nom de la table dans laquelle seront insérées les données
    * @param array $data Tableau associatif qui contient les données sous la forme "nom_champ" => "valeur_champ"
    * 
    * @return string La requête créée
    */
    public function getInsertQuery($table, $data)
    {
        $sql  = "INSERT INTO " . $table . " (";
        $sql .= implode(",", array_keys($data));
        $sql .= ") VALUES(";
        $sql .= "'" . implode("','", array_values($data)) . "')";

        return $sql;
    }

    /**
    * Construction d'une requête SQL d'insertion *préparée*, en fonction des champs donnés
    * 
    * @param string Nom de la table dans laquelle seront insérées les données
    * @param array Tableau associatif qui contient les données sous la forme "nom_champ" => "valeur_champ"
    * @param bool $dropEmptyData  [optionnel] Mettre à true pour ne pas faire figurer dans la requête les champs pour lesquels la valeur fournie est vide
    *
    * @return array Tableau associatif qui contient la requête créée ("query") et ses paramètres ("params")
    */
    public function getPreparedInsertQuery($table, $data, $dropEmptyData = false)
    {
        if(empty($data))
        {
            // Si aucune donnée n'est fournie on ne renvoie pas de requête

            return "";
        }

        if($dropEmptyData)
        {
            // Si on ne veut pas tenir compte des champs pour lesquels la valeur fournie est vide

            // On se débarrasse des champs en question en amont
            $new_data = array();

            foreach($data as $field => $value)
            {
                if($value != "")
                    $new_data[$field] = $value;
            }

            $data = $new_data;
        }

        $sql  = " INSERT INTO " . $table . " (" . implode(", ", array_keys($data)) . ")"
                . " VALUES(:" . implode(", :", array_keys($data)) . ")";

        $params = array();
        foreach($data as $champ => $valeur)
        {
            $params[":" . $champ] = $valeur;	
        }

        return array("query" => $sql, "params" => $params);
    }

    /**
    * Construction d'une requête SQL de mise à jour *préparée*, en fonction des champs donnés
    * 
    * @param string $table Nom de la table dans laquelle se trouve l'enregistrement à modifier
    * @param array $data Tableau associatif qui contient les données sous la forme "nom_champ" => "valeur_champ"
    * @param string $primary_key Clé primaire de la table
    * @param string $primary_key Valeur de la clé primaire pour l'enregistrement que l'on veut modifier
    * 
    * @return array Tableau associatif qui contient la requête créée ("query") et ses paramètres ("params")
    */
    public function getPreparedUpdateQuery($table, $data, $primary_key)
    {
        $sql_field_list = array();
        $params = array();

        foreach($data as $field => $value)
        {
            if($field != $primary_key)
                $sql_field_list[] = $field . " = :" . $field;

            $params[":" . $field] = $value;
        }

        $sql  = " UPDATE " . $table
                . " SET " . implode(", ", $sql_field_list)
                . " WHERE " . $primary_key . " = :" . $primary_key;

        return array("query" => $sql, "params" => $params);
    }
	
	
    /**
    * Libère la connexion à la base de données
    */
    public function __destruct()
    {
        $this->connexion = NULL;	// invite PDO à libérer la connexion à la base de données
    }
}
?>