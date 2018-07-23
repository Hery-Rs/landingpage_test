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
    <link rel="stylesheet" type="text/css" href="css/styles.css">

</head>

<body>

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

    <section>
        <div class="container container__middle container__table">
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
            </form>
        </div>
    </section>

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