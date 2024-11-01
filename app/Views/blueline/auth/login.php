<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="login-page">

  <div class="login-form">
    <form class="form-signin" method="post" action="<?= site_url('loginpost') ?>" id="login">
      <div class="logo">
        <!-- Add logo image here if needed -->
      </div>

      <?php if (isset($send_email)): ?>
        <?php if ($send_email): ?>
          <div class="tile-base no-padding" style="text-align: justify !important;">
            <h4 style="text-align: center!important; width: 100%; margin-bottom: 30px;">
              <?= lang('reset_password_request') ?>
            </h4>
            <p style="text-align: center; margin-bottom: 30px;">
              <?= lang('password_reset_email_sent') ?>
            </p>
            <input type="submit" style="text-align: center; width: 100%;" class="btn btn-primary" value="<?= lang('go_to_login') ?>" />
          </div>
        <?php else: ?>
          <div class="tile-base no-padding">
            <div class="tile-extended-header">
              <div class="grid tile-extended-header">
                <div class="grid__col-6 grid--justify-end" style="text-align: justify !important;">
                  <p><?= lang('reset_password_request') ?></p>
                  <p>
                    <?= lang('no_email_sent') ?><br>
                    <?= lang('contact_admin') ?>
                  </p>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="form-group">
          <label for="email"><?= lang('email') ?></label>
          <input type="email" class="form-control" id="email" name="email" placeholder="<?= lang('enter_your_email') ?>" required />
        </div>
        <div class="form-group">
          <label for="password"><?= lang('password') ?></label>
          <input type="password" class="form-control" id="password" name="password" placeholder="<?= lang('enter_your_password') ?>" required />
        </div>

        <input type="submit" class="btn btn-primary fadeoutOnClick" value="<?= lang('login') ?>" />
        <div class="forgotpassword">
          <a href="<?= site_url("forgotpass") ?>"><?= lang('forgot_password') ?></a>
        </div>

        <center>
          <div class="form-header"><?= lang('secure_connection') ?><br><?= esc($version ?? 'Version not defined') ?></div>
        </center>
        <?php if (isset($expiration)): ?>
          <div id="error"><?= esc($expiration) ?></div>
        <?php endif; ?>
      <?php endif; ?>
    </form>
  </div>
  <div class="login-bg">
    <div class="login-background">
      <img src="https://vision.bimmapping.com/assets/blueline/images/backgrounds/field.jpg" alt="">
    </div>
  </div>
</div>
<?= $this->endSection() ?>