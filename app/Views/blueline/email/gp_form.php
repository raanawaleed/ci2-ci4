<?php  echo validation_errors();

      echo "Password :".form_password('password', '');
      echo "Password Confirmation :".form_password('passconf', '');
      echo form_submit('submit', 'Submit');

?> 