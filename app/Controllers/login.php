  <?php if(isset($send_email)): ?>
    <form class='form-signin'>
      <div class="logo"><img src="<?=base_url()?><?php if($core_settings->login_logo == ""){ echo $core_settings->invoice_logo;} else{ echo $core_settings->login_logo; }?>" alt="<?=$core_settings->company;?>">
      </div>

      <?php if($send_email): ?>
          <div class="tile-base no-padding" style="textContent:justify !important;">



                  <h4  style="text-align:center!important;width: 100%;margin-bottom: 30px;">Vous avez demandé à réinitialiser votre mot de passe...</h4>
                  </br>
                  <p style="text-align: center;margin-bottom: 30px;">
                   Un email de réinitialisation de mot de passe vous a été envoyé.
                  </br>
                  Veuillez vérifier votre boîte de réception et votre dossier <b>spam</b> au cas où vous ne le trouveriez pas dans votre boîte de réception principale                
      </br>
                  Suivez les instructions fournies dans l'email pour confirmer la réinitialisation de votre mot de passe   </p>
                  <input type="submit" style="text-align: center; width:100%; " class="btn btn-primary"  value="<?=$this->lang->line('application_go_to_login');?>" />



          </div>
        <?php else: ?>
          <div class="tile-base no-padding">
            <div class="tile-extended-header">
              <div class="grid tile-extended-header">
                <div class="grid__col-6 grid--justify-end" style="text-align:justify !important;">
                  <p>Vous avez demandé à réinitialiser votre mot de passe...</p>
                  </br>
                  <p>
                  Aucun e-mail de réinitialisation n'a été envoyé, un problème innatendu a été rencontré.
                  </br>
                  Veuillez, s'il vous plaît, contacter l'administrateur..
                </p>
                </div>
              </div>
            </div>
          </div>
    <?php endif; ?>
    </form>

  <?php else: ?>
      <?php $attributes = array('class' => 'form-signin', 'role'=> 'form', 'id' => 'login'); ?>
      <?=form_open('login', $attributes)?>
              <div class="logo"><img src="<?=base_url()?><?php if($core_settings->login_logo == ""){ echo $core_settings->invoice_logo;} else{ echo $core_settings->login_logo; }?>" alt="<?=$core_settings->company;?>"></div>
              <?php if($error == "true") { $message = explode(':', $message)?>
                  <div id="error">
                    <?=$message[1]?>
                  </div>
              <?php } ?>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Entrer votre email" />
                </div>
                <div class="form-group">
                  <label for="password"><?=$this->lang->line('application_password');?></label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="<?=$this->lang->line('application_enter_your_password');?>" />
                </div>

                <input type="submit" class="btn btn-primary fadeoutOnClick" value="<?=$this->lang->line('application_login');?>" />
                <div class="forgotpassword"><a href="<?=site_url("forgotpass");?>"><?=$this->lang->line('application_forgot_password');?></a></div>

                <div class="sub">
                 <?php if($core_settings->registration == 1){ ?><div class="small"><small><?=$this->lang->line('application_you_dont_have_an_account');?></small></div><hr/><a href="<?=site_url("register");?>" class="btn btn-success"><?=$this->lang->line('application_create_account');?></a> <?php } ?>
                </div>
      	  <center><div class="form-header">Connexion sécurisée par certificat SSL 256 bits<br><?=$version?></div></center>
      	  <?php if (isset($expiration)): ?>
          <div id="error"><?=$expiration?></div>
          <?php endif; ?>
      <?=form_close()?>



    <?php endif; ?>
