<?php
require_once "swiftmailer/swift_required.php";
require_once "planet_config.php";

/**
 * Classe "wrapper" pour SwiftMailer qui permet de limiter le nombre de lignes de code nécessaires à l'envoi d'un email
 * Cette classe est dépendante du fichier de configuration "configuration.php"
 */
class PlanetMail
{
	/**
	 * Objet Swift_MailTransport (méthode de transport de l'email)
	 * 
	 * @var Swift_MailTransport
	 */
	private $transport;
	
	/**
	 * Objet Swift_Mailer (permet de déclencher l'envoi de l'email)
	 * 
	 * @var Swift_Mailer
	 */
	private $mailer;
	
	/**
	 * Objet Swift_Message (caractéristiques du message qui va être envoyé par email)
	 * 
	 * @var Swift_Message
	 */
	private $message;
	
	public $default_template_path = "../../templates_emails";
	
	/**
	 * Constructeur
	 * 
	 * @param string $from Adresse email de l'expéditeur
	 * @param string $to Adresses email des destinataires séparées par des points virgule
	 * @param string $template_filename Nom de fichier du template de l'email
	 * @param array $template_variables Tableau associatif qui contient les noms de variables présentes dans le template ainsi que leurs valeurs (ex : array("{nom}" => "ROUVE", "{prenom}" => "Jean-Paul"))
	 * @param string $subject Sujet de l'email
     * @param string $reply_to Adresse de réponse de l'email
     * @param array $attachments [optionnel] Chemins vers des fichiers à joindre
	 * 
	 * @return void
	 */
	function __construct($from, $to, $template_filename, $template_variables, $subject, $reply_to = "", $attachments = array())
	{
        $conf = new PlanetConfig();
        
		$this->_initTransport(
							 $conf->useSmtp,
							 $conf->smtpHost,
							 $conf->smtpPort,
							 $conf->smtpAuth,
							 $conf->smtpLogin,
							 $conf->smtpPass
		);
		
		$this->mailer = Swift_Mailer::newInstance($this->transport);
		
		$this->message = Swift_Message::newInstance();
		
		// Récupération du contenu du template de l'email et remplacement des variables
		$mailBody = file_get_contents(dirname(__FILE__) . "/" . $this->default_template_path . "/" . $template_filename);
		$mailBody = str_replace(array_keys($template_variables), array_values($template_variables), $mailBody);
        
        // Insertion des pièces jointes
        if($nb_pj = count($attachments))
        {
            for($i = 0; $i < $nb_pj; $i++)
            {
                $this->message->attach(Swift_Attachment::fromPath($attachments[$i]));
            }
        }
		
		$this->message->setFrom($from);
		$this->message->setTo(explode(";", $to));
        
        if($reply_to != '')
        {
            $this->message->setReplyTo($reply_to);
        }
        
		$this->message->setSubject(utf8_decode($subject));
		$this->message->setBody(utf8_decode($mailBody), "text/html", "iso-8859-15");
	}
	
	/**
	 * Envoi immédiat de l'email
	 * 
	 * @return bool true en cas de succès, false sinon
	 */
	public function send()
	{
		return $this->mailer->send($this->message);	
	}
	
	/**
	 * Initialisation de l'objet Swift_MailTransport qui définit la méthode de transport des emails utilisée (SMTP / Fonction mail() etc)
	 * 
	 * @param bool $use_smtp [optionnel] Utiliser ou non un serveur SMTP comme méthode de transport
	 * @param string $smtp_host [optionnel] Hôte SMTP
	 * @param int $smtp_port [optionnel] Port utilisé pour le serveur SMTP
	 * @param bool $smtp_auth [optionnel] Le serveur SMTP nécessite une authentification
	 * @param string $smtp_login [optionnel] Login pour l'authentification auprès du serveur SMTP
	 * @param string $smtp_pass [optionnel] Mot de passe pour l'authentification auprès du serveur SMTP
	 * 
	 * @return void
	 */
	private function _initTransport($use_smtp = false, $smtp_host = null, $smtp_port = 25, $smtp_auth = false, $smtp_login = null, $smtp_pass = null)
	{
		$this->transport = Swift_MailTransport::newInstance();
		
		if($use_smtp)
		{
			// L'envoi de l'email passe par un serveur SMTP
			
			$this->transport = Swift_SmtpTransport::newInstance($smtp_host, $smtp_port,'tls');
			
			if($smtp_auth)
			{
				// Le serveur SMTP nécessite une authentification
				
				$this->transport->setUsername($smtp_login);
				$this->transport->setPassword($smtp_pass);
			}
		}
	}
}