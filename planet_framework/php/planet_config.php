<?php
/**
 * Classe de stockage des variables de configuration
 */
class PlanetConfig
{
	/**
	 * Base de données
	 */
	
	public $driver           = 'mysql';		// modifier cette constante si le SGBD n'est pas MySQL (PDO)
	
	// Paramètres de connexion MySQL
	public $mysqlHost        = '127.0.0.1';
	public $mysqlDbName      = 'mydb';
	public $mysqlUser        = 'root';
	public $mysqlPass        = 'root';
	
	// Login et mot de passe pour l'export de données d'une table au format .csv
	// /!\ NE PAS METTRE LES MEMES IDENTIFIANTS QUE CEUX DE LA BASE DE DONNEES CI-DESSUS
	public $exportLogin      = 'admin';
	public $exportPass       = 'admin';
	
	/**
	* Emails emis par l'application
	*/
	
	public $useSmtp          = 1;			// Utilisation (1) ou non (0) d'un serveur SMTP pour l'envoi des emails
	public $smtpHost         = 'smtp-mail.outlook.com';			// Adresse du serveur SMTP
	public $smtpPort         = '587';			// Adresse du serveur SMTP
	public $smtpAuth         = 1;			// Le serveur SMTP nÚcessite (1) ou non (0) une authentification
	public $smtpLogin        = 'maetva.test@outlook.fr';			// Nom d'utilisateur pour la connexion au serveur SMTP
	public $smtpPass         = 'maetvatest1234';			// Mot de passe pour la connexion au serveur SMTP
	
	public $siteName         = "Maetva";                       		// Nom du site, ex: "Swiss Confort"
	public $siteReplyToEmail = "maetva.test@outlook.fr";                          // Adresse qui sera pré-remplie comme expéditeur lorsqu'un utilisateur répondre à un email émis par le site
	public $siteExpEmail     = 'maetva.test@outlook.fr';   // Adresse apparaissant comme expéditeur des emails émis par le site
	public $siteExpName      = "Maetva";                      	// Nom apparaissant comme expéditeur des emails émis par le site
	public $formAdminEmails  = 'maetva.test@outlook.fr';     						// Adresse de la ou les personne(s) en charge de traiter les demandes issues du formulaire de contact (séparer les adresses avec des points-virgule)
	//public $formAdminEmails  = 'y.valentin@maetvaplanet.com';     						// Adresse de la ou les personne(s) en charge de traiter les demandes issues du formulaire de contact (séparer les adresses avec des points-virgule)
}
?>
