<?php
require_once "planet_framework/php/planet_db.php";
require_once "planet_framework/php/planet_helpers.php";

if(!isset($_GET['login']) || !isset($_GET['pass'])){
    // Pas de login et/ou de pass => fin de parcours
    PlanetHelpers::redirect("http://" . $_SERVER["HTTP_HOST"]);
}

$givenLogin = filter_var($_GET['login']);
$givenPass = filter_var($_GET['pass']);

$conf = new PlanetConfig();

if($givenLogin != $conf->exportLogin || $givenPass != $conf->exportPass){
    // Login et/ou pass incorrect(s)
    PlanetHelpers::redirect("http://" . $_SERVER["HTTP_HOST"]);
}

// Login et pass OK

// Export de la table
$db = new PlanetDb();

$db->exportTable('');

exit;

?>