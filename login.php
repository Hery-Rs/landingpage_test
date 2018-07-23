<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Login</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap core CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.5.4/css/mdb.min.css" rel="stylesheet">
    <!-- Styles CSS-->
    <link rel="stylesheet" type="text/css" href="css/login.css">

</head>

<body>

    <!-- Header-->
        <?php include('templates_layout/header.php'); ?>
    <!-- Header-->

    <!-- Section -->
    <section>
        <div class="login-csv container container__middle container__table">
            <h3>ADMIN <span>Récupération des données format CSV</span</h3>
            <form method="post" action="export.php">
                <div class="form_row cf">
                    <div class="md-form form_column form_column50 required spacing">
                        <i class="fa fa-user prefix grey-text"></i>
                        <input placeholder="identifiant" type="text" name="login" id="pfo_login" class="form-control">
                        <label for="pfo_login">Votre identifiant</label>
                    </div>
                    <div class="md-form form_column form_column50 required spacing">
                        <i class="fa fa-lock prefix grey-text"></i>
                        <input placeholder="mot de passe" type="password" name="pass" id="pfo_pass" class="form-control">
                        <label for="pfo_pass">Votre mot de passe</label>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-primary" type="submit">Valider</button>
                </div>
                <div class="form-error"></div>
            </form>
        </div>
    </section>
    <!-- Section -->

    <!-- Footer -->
        <?php include('templates_layout/footer.php'); ?>
    <!-- Footer -->

    <!-- Livevalidation Standalone -->
    <script src="planet_framework/js/livevalidation_standalone_modified.js"></script>
    <!-- Planet Formvalidator -->
    <script src="planet_framework/js/planet_formvalidator.js"></script>
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