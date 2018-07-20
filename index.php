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

	<title></title>

	<link rel="stylesheet" type="text/css" href="css/shadowbox.css">
		
	<script src="planet_framework/js/livevalidation_standalone_modified.js"></script>
	<script src="planet_framework/js/planet_formvalidator.js"></script>
	<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
	<script src="js/shadowbox.js"></script>
	
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

</head>

<body>

	<div class="pagewrapper">
		
		<header class="header"></header>

		<div class="container container__middle container__table">
			<div class="column col-6 visu"></div>

			<div class="column col-6 right">

				<form method="post" action="traitement.php" id="<?php echo $form_id; ?>">
				
					<div class="form_row cf">
						<div class="form_column form_column50 required spacing<?php if(in_array("nom", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
							<input placeholder="Nom" type="text" name="nom" id="pfo_nom" value="<?php if (isset($_SESSION[$form_id]["nom"])) {echo $_SESSION[$form_id]["nom"];}elseif(isset($_GET['v1'])){echo $_GET['v1'];} ?>" />
						</div>

						<div class="form_column form_column50 required spacing<?php if(in_array("prenom", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
							<input placeholder="Prénom" type="text" name="prenom" id="pfo_prenom" value="<?php if (isset($_SESSION[$form_id]["prenom"])) {echo $_SESSION[$form_id]["prenom"];}elseif(isset($_GET['v2'])){echo $_GET['v2'];} ?>" />
						</div>
					</div>

					<div class="form_row cf">				
						<div class="form_column form_column50 required spacing<?php if(in_array("email", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
							<input placeholder="Email" type="email" name="email" id="pfco_email" value="<?php echo $_SESSION[$form_id]["email"]; ?>" />
						</div>
						<div class="form_column form_column50 required spacing<?php if(in_array("telephone", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
							<input placeholder="Téléphone" type="tel" name="telephone" id="pfco_telephone" value="<?php echo $_SESSION[$form_id]["telephone"]; ?>" />
						</div>
					</div>
					<input type="hidden" name="opt_in" value="" />
				
					<div class="form_row cf">				
						<div class="form_column form_column65 spacing<?php if(in_array("adresse", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
							<input placeholder="Adresse" type="tel" name="adresse" id="pfco_adresse" value="<?php echo $_SESSION[$form_id]["adresse"]; ?>" />
						</div>

						<div class="form_column form_column35 spacing<?php if(in_array("code_postal", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
							<input placeholder="Code postal" type="text" name="code_postal" id="pfco_code_postal" value="<?php if (isset($_SESSION[$form_id]["code_postal"])) {echo $_SESSION[$form_id]["code_postal"];}elseif(isset($_GET['v3'])){echo $_GET['v3'];} ?>" />
						</div>
					</div>

					<div class="form_row cf">
						<div class="form_column form_column50 spacing<?php if(in_array("ville", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
							<input placeholder="Ville" type="tel" name="ville" id="pfco_ville" value="<?php echo $_SESSION[$form_id]["ville"]; ?>" />
						</div>
						<div class="form_column form_column50 spacing<?php if(in_array("entreprise", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
							<input placeholder="Entreprise" type="tel" name="entreprise" id="pfco_entreprise" value="<?php echo $_SESSION[$form_id]["entreprise"]; ?>" />
						</div>
					</div>

					<div class="spacing<?php if(in_array("opt_in", (array)$_SESSION[$form_id]["error_fields"])) echo " champ_invalide"; ?>">
						<span id="pfo_opt_in">
							<span class="checkbox">
								<input type="checkbox" name="opt_in" id="opt_in" value="1" <?php if($_SESSION[$form_id]["opt_in"] == "1") echo 'checked="checked"'; ?> />
								<label for="opt_in" class="">je souhaite m’abonner à la Newsletter</label>
							</span>
						</span>
					</div>

				</form>


			</div>

		</div>


			

	</div> <!-- // .pagewrapper -->

</body>

</html>