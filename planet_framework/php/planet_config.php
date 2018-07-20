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
	public $mysqlHost        = '';
	public $mysqlDbName      = '';
	public $mysqlUser        = '';
	public $mysqlPass        = '';
	
	// Login et mot de passe pour l'export de données d'une table au format .csv
	// /!\ NE PAS METTRE LES MEMES IDENTIFIANTS QUE CEUX DE LA BASE DE DONNEES CI-DESSUS
	public $exportLogin      = '';
	public $exportPass       = ''; 
	
	/**
	* Emails emis par l'application
	*/
	
	public $useSmtp          = 0;			// Utilisation (1) ou non (0) d'un serveur SMTP pour l'envoi des emails
	public $smtpHost         = '';			// Adresse du serveur SMTP
	public $smtpPort         = '';			// Adresse du serveur SMTP
	public $smtpAuth         = 0;			// Le serveur SMTP nÚcessite (1) ou non (0) une authentification
	public $smtpLogin        = '';			// Nom d'utilisateur pour la connexion au serveur SMTP
	public $smtpPass         = '';			// Mot de passe pour la connexion au serveur SMTP
	
	public $siteName         = "";                       		// Nom du site, ex: "Swiss Confort"
	public $siteReplyToEmail = "";                          // Adresse qui sera pré-remplie comme expéditeur lorsqu'un utilisateur répondre à un email émis par le site
	public $siteExpEmail     = '';   // Adresse apparaissant comme expéditeur des emails émis par le site 
	public $siteExpName      = "";                      	// Nom apparaissant comme expéditeur des emails émis par le site
	public $formAdminEmails  = '';     						// Adresse de la ou les personne(s) en charge de traiter les demandes issues du formulaire de contact (séparer les adresses avec des points-virgule)
	//public $formAdminEmails  = 'y.valentin@maetvaplanet.com';     						// Adresse de la ou les personne(s) en charge de traiter les demandes issues du formulaire de contact (séparer les adresses avec des points-virgule)
}
?>
