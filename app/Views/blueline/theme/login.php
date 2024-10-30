<?php

/**
 * @file        Login View
 * @author      Luxsys <support@freelancecockpit.com>
 * @version     2.5.0
 */

// Ensure you load the URL helper in your controller or autoload it.
helper('url');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <meta name="robots" content="none" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="refresh" content="18000">

  <title><?= esc($core_settings->company); ?></title>

  <!-- CSS Files -->
  <link href="<?= base_url('assets/blueline/css/bootstrap.min.css') ?>?ver=<?= esc($core_settings->version); ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/blueline/css/plugins/animate.css') ?>?ver=<?= esc($core_settings->version); ?>" />
  <link rel="stylesheet" href="<?= base_url('assets/blueline/css/plugins/nprogress.css') ?>" />
  <link href="<?= base_url('assets/blueline/css/blueline.css') ?>?ver=<?= esc($core_settings->version); ?>" rel="stylesheet">
  <link href="<?= base_url('assets/blueline/css/user.css') ?>?ver=<?= esc($core_settings->version); ?>" rel="stylesheet" />

  <!-- Dynamically Inject Theme Colors -->
  <?= get_theme_colors($core_settings); ?>

  <!-- Web Font Loader -->
  <script type="text/javascript">
    WebFontConfig = {
      google: {
        families: ['Open+Sans:400italic,400,300,600,700:latin']
      }
    };
    (function() {
      var wf = document.createElement('script');
      wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
        '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
      wf.type = 'text/javascript';
      wf.async = 'true';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(wf, s);
    })();
  </script>

  <!-- Favicon -->
  <link rel="SHORTCUT ICON" href="<?= base_url('assets/blueline/img/favicon.ico') ?>" />

  <!-- IE9 Support for HTML5 -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login" style="background-image:url('<?= base_url('assets/blueline/images/backgrounds/' . esc($core_settings->login_background)) ?>')">

  <div class="container-fluid">
    <div class="row" style="margin-bottom:0px">
      <?= $this->renderSection('content'); ?>
    </div>
  </div>

  <!-- Notification System -->
  <?php if (session()->getFlashdata('message')): ?>
    <?php $exp = explode(':', session()->getFlashdata('message')); ?>
    <div class="notify <?= esc($exp[0]) ?>"><?= esc($exp[1]) ?></div>
  <?php endif; ?>

  <!-- JS Files -->
  <script src="<?= base_url('assets/blueline/js/plugins/jquery-1.12.4.min.js') ?>"></script>
  <script src="<?= base_url('assets/blueline/js/bootstrap.min.js') ?>"></script>
  <script type="text/javascript" src="<?= base_url('assets/blueline/js/plugins/velocity.min.js') ?>"></script>
  <script type="text/javascript" src="<?= base_url('assets/blueline/js/plugins/velocity.ui.min.js') ?>"></script>
  <script type="text/javascript" src="<?= base_url('assets/blueline/js/plugins/validator.min.js') ?>"></script>
  <script type="text/javascript" src="<?= base_url('assets/blueline/js/plugins/nprogress.js') ?>"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      var fade = "Left";
      <?php if ($core_settings->login_style == "center"): ?>
        fade = "Up";
      <?php endif; ?>

      $("form").validator();
      $(".form-signin").addClass("animated fadeIn" + fade);

      $(".fadeoutOnClick").on("click", function() {
        NProgress.start();
        $(".form-signin").addClass("animated fadeOut" + fade);
        NProgress.done();
      });

      <?php if (isset($error) && $error == "true"): ?>
        $("#error").addClass("animated shake");
      <?php endif; ?>

      // Notify behavior
      $('.notify').velocity({
        opacity: 1,
        right: "10px",
      }, 900, function() {
        $('.notify').delay(4000).fadeOut();
      });

      // Form styling (2.5.0 style)
      $(".form-control").each(function() {
        if ($(this).val().length > 0) {
          $(this).closest('.form-group').addClass('filled');
        }
      });

      $(".chosen-select").each(function() {
        if ($(this).val().length > 0) {
          $(this).closest('.form-group').addClass('filled');
        }
      });

      $(".form-control").on("focusin", function() {
        $(this).closest('.form-group').addClass("focus");
      });

      $(".chosen-select").on("chosen:showing_dropdown", function() {
        $(this).closest('.form-group').addClass("focus");
      });

      $(".chosen-select").on("chosen:hiding_dropdown", function() {
        $(this).closest('.form-group').removeClass("focus");
      });

      $(".form-control").on("focusout", function() {
        $(this).closest('.form-group').removeClass("focus");
        if ($(this).val().length > 0) {
          $(this).closest('.form-group').addClass('filled');
        } else {
          $(this).closest('.form-group').removeClass('filled');
        }
      });
    });
  </script>

</body>

</html>