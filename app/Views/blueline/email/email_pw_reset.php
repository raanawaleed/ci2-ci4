<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- --------------------------------------Font awesome ------------------------------------------------------------->

    <!--  -------------------------------------From assets folder------------------------------------------------------------ -->

    <link rel="stylesheet" type="text/css"
          href="<?php base_url() ?>assets/blueline/css/bootstrap-4.2.1/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php base_url() ?>assets/blueline/css/fontawesome5.min.css">

    <link rel="stylesheet" type="text/javascript" href="<?php base_url() ?>assets/blueline/js/jquery3.js">
    <link rel="stylesheet" type="text/javascript" href="<?php base_url() ?>assets/blueline/js/fontawesome5.min.js">
    <link rel="stylesheet" type="text/javascript"
          href="<?php base_url() ?>assets/blueline/js/bootstrap-4.2.1/bootstrap.min.js">


    <title>Réinitialisation du mot de passe</title>

</head>
<body style="margin: 0;
        padding: 0;">

<div class="container" style="width: 100% ;display: flex;
        justify-content: center;">

    <div class="outer-panel text-center img-rounded" style="width: 60%;
        padding: 0% 7%;
        background-color: rgba(0, 0, 0, 0.059);">
        <i class="far fa-bookmark p-2"></i>
        <div class="inner-panel img-rounded">

            <div class="col-md-12"
                 style="align-content: center; text-align: center;  margin-top: 20px;">
                <img src="<?php base_url() ?>assets/blueline/img/invoice_logo.png" alt="Vision">
            </div>
            <div>
                <div class="col-md-12">
                    <p style="align-content: center; width: 100%;">
                    <h4  style="position: relative; right: 50%;width: 100%;margin-top: 30px; align-content: center;
        margin-bottom: 20px; "> Bonjour <?php echo ucwords(strtolower($user->firstname . ' ' . $user->lastname)) ?>
                        ,</h4>
                    Vous avez demandé à réinitialiser votre mot de passe. Vous pouvez maintenant valider cette demande
                    en cliquant sur le bouton ci-dessous.
                    </p>
                    
                    <p><button type="button" class="btn btn-primary"
                            style="position: absolute; right: 50%;border-radius: 10px ; padding: 10px 20px; opacity: 0.4;  align-content: center" >
                            <a style="text-decoration: none!important;" style="color: black;"
                               href="<?php echo $url; ?>">Réinitialiser</a>
                    </button></p>
                   
                    <p style="justify-content: center;width: 100%;">
                        Veuillez tenir compte du fait que ce lien n'est valable que pour aujourd'hui !
                    </p>
                    
                </div>
               
            </div>
        </div>
    </div>
</div>

</body>
</html>