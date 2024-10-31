<?php

namespace App\Helpers;

use CodeIgniter\Email\Email;
use Config\Services;

class EmailHelper
{
	public static function sendMail($id, $object, $path, $dest, $cc, $message, $redirectError, $redirectSent)
	{
		$session = Services::session();
		$settings = new \App\Models\SettingModel();
		$settings->find(['id_vcompanies' => $_SESSION['current_company']]);
		$from = $settings->email;
		$obj = explode(' ', $object);
		$object = $obj[0] . ' ' . $obj[1];

		// Load email library and configure it
		$email = new Email();
		$config = [
			'protocol' => 'smtp',
			'SMTPHost' => getenv('SMTP_HOST'),
			'SMTPUser' => getenv('SMTP_USER'),
			'SMTPPass' => getenv('SMTP_PASS'),
			'SMTPPort' => getenv('SMTP_PORT'),
			'SMTPCrypto' => getenv('SMTP_CRYPTO'),
			'mailType' => 'html',
			'charset'  => 'utf-8',
			'wordWrap' => true,
		];

		$email->initialize($config);
		$email->setFrom($from, $object);
		$email->setTo($dest);
		$email->setCC($cc);
		$email->setSubject($object . ' comme demandé');
		$email->setMessage($message);

		// Attach the uploaded file if the path is provided
		if ($path) {
			$email->attach($path);
		}

		if (!$email->send()) {
			$session->setFlashdata('message', 'error:' . lang('Email not sent. Check your email settings'));
			return redirect()->to($redirectError); // Adjust redirection as needed
		} else {
			$session->setFlashdata('message', 'success:' . lang('Test email has been sent. Check your inbox'));
			return redirect()->to($redirectSent); // Adjust redirection as needed
		}
	}

	public static function forgetPwdMail($id, $dest)
	{
		$session = Services::session();
		$email = new Email();

		$config = [
			'protocol' => 'smtp',
			'SMTPHost' => getenv('SMTP_HOST'),
			'SMTPUser' => getenv('SMTP_USER'),
			'SMTPPass' => getenv('SMTP_PASS'),
			'SMTPPort' => getenv('SMTP_PORT'),
			'SMTPCrypto' => getenv('SMTP_CRYPTO'),
			'mailType' => 'html',
			'charset'  => 'utf-8',
			'wordWrap' => true,
		];

		$email->initialize($config);
		$email->setFrom('erpvisionmail@gmail.com', ''); // Add a name if needed
		$email->setTo($dest);
		$email->setSubject('Réinitialiser votre mot de passe');

		$data = ['id' => $id];
		$email->setMessage(view('blueline/email/email_forgot', $data));

		if (!$email->send()) {
			$session->setFlashdata('message', 'error:' . lang('Email not sent. Check your email settings'));
			return redirect()->to('login');
		} else {
			$session->setFlashdata('message', 'success:' . lang('Email has been sent. Check your inbox'));
			return redirect()->to('login');
		}
	}
}
