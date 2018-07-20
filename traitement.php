<?php
session_start();

require_once "planet_framework/php/planet_filter.php";
require_once "planet_framework/php/planet_db.php";
require_once "planet_framework/php/planet_mail.php";


$form_id = 'pf_std';

$fail = true;
$validationMessage  = 'Nous avons bien reçu votre demande et vous recontacterons sous peu';
$validationMessageClass = "confirmation_message";

$redirectOnFailure = "http://" . $_SERVER["HTTP_HOST"] . "/";
$redirectOnSuccess = $redirectOnFailure;

$facultatifs = array('adresse', 'code_postal', 'ville', 'entreprise', 'opt_in');
$f = new PlanetFilter();
$f->filtrer("post", $facultatifs);

if($f->succesFiltrage)
{
	// Pas d'erreur au niveau du filtrage
	
	// Insertion d'un enregistrement dans la base de données
	
    $conf = new PlanetConfig();
	$db = new PlanetDb();
	
	$data = array(
		"id"                  => "''",
		"nom"                 => $f->get("nom"),
		"prenom"              => $f->get("prenom"),
		"email"               => $f->get("email"),
		"adresse"             => $f->get("adresse"),
		"telephone"           => $f->get("telephone"),
		"code_postal"         => $f->get("code_postal"),
		"ville"               => $f->get("ville"),
		"entreprise"          => $f->get("entreprise"),
		"opt_in"              => $f->get("opt_in"),
		"date_enregistrement" => date("Y-m-d H:i:s"),
		"provenance"          => $_SESSION['provenance']
	);
	
	$query = $db->getPreparedInsertQuery("", $data);
	$stmt = $db->connexion->prepare($query["query"]);
	$res = $stmt->execute($query["params"]);
    $id = $db->connexion->lastInsertId();
	
	if($res)
	{
		// Insertion réussie
		
        
		// Envoi des emails

        // Génération du tableau qui contient les variables à remplacer dans le template de l'email (avec leurs valeurs respectives)
		$template_variables = array('{site}' => $conf->siteName);
		foreach($data as $field => $value)
		{
            if(trim($value) == "")
                $value = "<em>(non renseigné)</em>";

			$template_variables["{" . $field . "}"] = $value;
		}

		//$mail_user = new PlanetMail(array($conf->siteExpEmail => utf8_decode($conf->siteExpName)), $f->get('email'), 'email_form_user.html', $template_variables, 'Votre coupon de réduction', "", array($coupon_filepath));
        $mail_admin = new PlanetMail(array($conf->siteExpEmail => utf8_decode($conf->siteExpName)), $conf->formAdminEmails, 'email_form_admin.html', $template_variables, 'Nouvelle demande de renseignement');

		//if($mail_user->send() && $mail_admin->send())
		if($mail_admin->send())
		{
			$fail = false;
		}
	}
	else
	{
		// Insertion échouée
		
		$validationMessage = "Erreur au niveau de la base de données.";
        
        if(strstr($db->getErrorMessage($stmt), 'email_UNIQUE'))
        {
            $fail = false;  // On considère que le formulaire a été envoyé avec succès, même si en définitive il n'y a pas eu d'inscription en base
            $validationMessage = "Vous avez déjà envoyé vos coordonnées.";
        }
        
        echo $db->getErrorMessage($stmt);exit;
	}
}
else
{
	// Erreur au niveau du filtrage

	$validationMessage = "Merci de renseigner tous les champs du formulaire.";
}

if($fail)
{
	// Une erreur s'est produite
	
	$validationMessageClass = "error_message";
    $redirect = $redirectOnFailure;
    
	// Mise en session des données saisies

	foreach($f->donneesFiltrees as $donnee => $valeur)
	{
		$_SESSION[$form_id][$donnee] = $f->get($donnee);
	}

	// Champs qui contiennent des données invalides ou qui n'ont pas été remplis
	if(!empty($f->donneesInvalides))
		$_SESSION[$form_id]["error_fields"] = $f->donneesInvalides;
}
else
{
    // Pas d'erreur, c'est Lesieur...
    
    $redirect = $redirectOnSuccess;
	
    // Mise en session de l'id de l'enregistrement et de l'email pour les passer à l'iframe qui apparaît dans le popup de confirmation
    $_SESSION[$form_id]['email_tracking'] = $f->get('email');
    $_SESSION[$form_id]['id'] = $id;
	
}

$_SESSION[$form_id]['success'] = !$fail;    // Attention : cette variable indique uniquement qu'il n'y a pas eu d'erreur de traitement
$_SESSION[$form_id]["validationMessage"] = $validationMessage;
$_SESSION[$form_id]["validationMessageClass"] = $validationMessageClass;

// Redirection vers la page du formulaire
header("Location: " . $redirect);
exit;
?>