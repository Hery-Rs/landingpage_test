<?php 
session_start();
$form_id = 'pf_std';

if(!isset($_SESSION['provenance']))
{
	// Si on ne connaît pas encore la provenance de l'internaute
	
	// Récupération de la provenance dans l'URL et mise en session
	//$provenances = array('HIM', 'COM', 'CAM', 'MSP');
	
	// Si la variable 'p' n'est pas renseignée on prend toute l'URL pour mémoire (après le slash qui suit le nom de domaine)
	//$provenance = in_array($_GET['p'], $provenances) ? $_GET['p'] : $_SERVER['REQUEST_URI'];
	
	$provenance = empty($_GET['p']) ? $_SERVER['REQUEST_URI'] : filter_var($_GET['p'], FILTER_SANITIZE_STRING);
	
	$_SESSION['provenance'] = $provenance;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" /> 

	<title>Landingpage Test</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap core CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.4/css/mdb.min.css" rel="stylesheet">
    <!-- Shadowbox -->
	<link rel="stylesheet" type="text/css" href="css/shadowbox.css">
    <!-- Styles CSS-->
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>

<body>

    <!-- Header-->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark primary-color">
            <a class="navbar-brand" href="http://localhost:8000">Landingpage Test</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="http://localhost:8000">Landing Page <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost:8000/login.php">CSV</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Header -->

    <!-- Section -->
    <section>
        <div class="container container__middle container__table">
            <form method="post" action="traitement.php" id="<?php echo $form_id; ?>">
                <div class="form_row cf">
                    <div class="form_column form_column50 required spacing<?php if(in_array("civilite", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
                        <select class="form-control" name="civilite" id="pfo_civilite">
                            <option value="" disabled selected>Choisir votre civilité</option>
                            <option value="mme">Mme</option>
                            <option value="m.">M.</option>
                        </select>
                    </div>
                    <div class="md-form form_column form_column50 required spacing<?php if(in_array("nom", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
                        <input placeholder="Nom" type="text" name="nom" id="pfo_nom" value="<?php if (isset($_SESSION[$form_id]["nom"])) {echo $_SESSION[$form_id]["nom"];}elseif(isset($_GET['v1'])){echo $_GET['v1'];} ?>" class="form-control">
                        <label for="pfo_nom">Votre nom</label>
                    </div>
                </div>

                <div class="form_row cf">
                    <div class="md-form form_column form_column50 required spacing<?php if(in_array("prenom", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
                        <input placeholder="Prénom" type="text" name="prenom" id="pfo_prenom" value="<?php if (isset($_SESSION[$form_id]["prenom"])) {echo $_SESSION[$form_id]["prenom"];}elseif(isset($_GET['v2'])){echo $_GET['v2'];} ?>" class="form-control">
                        <label for="pfo_prenom">Votre prénom</label>
                    </div>
                    <div class="md-form form_column form_column50 required spacing<?php if(in_array("email", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
                        <input placeholder="Email" type="email" name="email" id="pfco_email" value="<?php echo $_SESSION[$form_id]["email"]; ?>" class="form-control">
                        <label for="pfco_email">Votre Email</label>
                    </div>
                </div>
                <input type="hidden" name="opt_in" value="" />

                <div class="spacing<?php if(in_array("opt_in", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
                    <div class="custom-control custom-checkbox checkbox">
                        <input type="checkbox" class="custom-control-input" name="opt_in" id="opt_in" value="1" <?php if($_SESSION[$form_id]["opt_in"] == "1") echo 'checked="checked"'; ?> />
                        <label for="opt_in" class="custom-control-label">je souhaite m’abonner à la Newsletter</label>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-primary" type="submit">Valider</button>
                </div>
            </form>
        </div>
    </section>
    <!-- Section -->

    <!-- Footer -->
    <footer class="page-footer font-small blue">

        <!-- Copyright -->
        <div class="footer-copyright text-center py-3">© 2018 Copyright Landing Test</div>
        <!-- Copyright -->

    </footer>
    <!-- Footer -->

    <!-- Livevalidation Standalone -->
    <script src="planet_framework/js/livevalidation_standalone_modified.js"></script>
    <!-- Planet Formvalidator -->
    <script src="planet_framework/js/planet_formvalidator.js"></script>
    <!-- Shadowbox -->
    <script src="js/shadowbox.js"></script>
    <!-- Shadowbox init -->
    <script type="text/javascript">

        Shadowbox.init();

        window.onload = function() {
            //initValidation('<?php echo $form_id; ?>');

            <?php if(isset($_SESSION[$form_id]["validationMessage"]) && !empty($_SESSION[$form_id]["validationMessage"])) : ?>

            Shadowbox.open({
                content:    '<div class="<?php echo $_SESSION[$form_id]["validationMessageClass"]; ?>"><?php echo addslashes($_SESSION[$form_id]["validationMessage"]); ?><br/><br/> <a href="javascript:Shadowbox.close()">Fermer</a></div>',
                player:     "html",
                height:     220,
                width:      450
            });

            <?php endif; ?>
        }

    </script>
    <!-- JQuery -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.13.0/umd/popper.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.4/js/mdb.min.js"></script>

</body>

</html>