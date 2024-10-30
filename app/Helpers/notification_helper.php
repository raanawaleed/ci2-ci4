<?php

use CodeIgniter\Email\Email;
use App\Models\SettingModel;
use App\Models\ClientModel;
use App\Models\TicketModel;
use App\Models\InvoiceHasPaymentModel;
use CodeIgniter\Files\File;
use CodeIgniter\View\Parser;

if (!function_exists('send_notification')) {
  /**
   * Send a notification email
   *
   * @param string $email
   * @param string $subject
   * @param string $text
   * @param mixed $attachment
   * @return bool
   */
  function send_notification(string $email, string $subject, string $text, $attachment = false): bool
  {
    $emailService = \Config\Services::email();
    $parser = \Config\Services::parser();
    $settingsModel = new SettingModel();
    $settings = $settingsModel->first();

    $emailService->setFrom($settings->email, $settings->company);
    $emailService->setTo($email);
    $emailService->setSubject($subject);

    if ($attachment) {
      if (is_array($attachment)) {
        foreach ($attachment as $value) {
          $emailService->attach(ROOTPATH . 'files/media/' . $value);
        }
      } else {
        $emailService->attach(ROOTPATH . 'files/media/' . $attachment);
      }
    }

    // Parse email template
    $clientModel = new ClientModel();
    $client = $clientModel->where('email', $email)->first();

    $parseData = [
      'company' => $settings->company,
      'link' => base_url(),
      'logo' => '<img src="' . base_url($settings->logo) . '" alt="' . $settings->company . '"/>',
      'invoice_logo' => '<img src="' . base_url($settings->invoice_logo) . '" alt="' . $settings->company . '"/>',
      'message' => $text,
      'client_contact' => $client ? $client->firstname . ' ' . $client->lastname : '',
      'client_company' => $client ? $client->company->name : ''
    ];

    $emailTemplate = file_get_contents(APPPATH . 'Views/' . $settings->template . '/templates/email_notification.html');
    $message = $parser->setData($parseData)->renderString($emailTemplate);

    $emailService->setMessage($message);

    return $emailService->send();
  }
}

if (!function_exists('send_ticket_notification')) {
  /**
   * Send ticket notification email
   *
   * @param string $email
   * @param string $subject
   * @param string $text
   * @param int $ticket_id
   * @param mixed $attachment
   * @return void
   */
  function send_ticket_notification(string $email, string $subject, string $text, int $ticket_id, $attachment = false)
  {
    $emailService = \Config\Services::email();
    $parser = \Config\Services::parser();
    $settingsModel = new SettingModel();
    $settings = $settingsModel->first();

    $ticketModel = new TicketModel();
    $ticket = $ticketModel->find($ticket_id);
    $ticketLink = base_url('tickets/view/' . $ticket->id);

    $emailService->setFrom($settings->email, $settings->company);
    $emailService->setTo($email);
    $emailService->setSubject($subject);
    $emailService->setReplyTo($settings->ticket_config_email);

    if ($attachment) {
      if (is_array($attachment)) {
        foreach ($attachment as $value) {
          $emailService->attach(ROOTPATH . 'files/media/' . $value);
        }
      } else {
        $emailService->attach(ROOTPATH . 'files/media/' . $attachment);
      }
    }

    // Parse email template
    $parseData = [
      'company' => $settings->company,
      'link' => base_url(),
      'ticket_link' => $ticketLink,
      'ticket_number' => $ticket->reference,
      'ticket_created_date' => date($settings->date_format . ' ' . $settings->date_time_format, $ticket->created),
      'ticket_status' => lang('application_ticket_status_' . $ticket->status),
      'logo' => '<img src="' . base_url($settings->logo) . '" alt="' . $settings->company . '"/>',
      'invoice_logo' => '<img src="' . base_url($settings->invoice_logo) . '" alt="' . $settings->company . '"/>',
      'message' => $text,
      'client_contact' => $ticket->client ? $ticket->client->firstname . ' ' . $ticket->client->lastname : '',
      'client_company' => $ticket->client ? $ticket->client->company->name : ''
    ];

    $emailTemplate = file_get_contents(APPPATH . 'Views/' . $settings->template . '/templates/email_ticket_notification.html');
    $message = $parser->setData($parseData)->renderString($emailTemplate);

    $emailService->setMessage($message);
    $emailService->send();
  }
}

if (!function_exists('receipt_notification')) {
  /**
   * Send receipt notification
   *
   * @param int $clientId
   * @param string $subject
   * @param int $paymentId
   * @return void
   */
  function receipt_notification(int $clientId, string $subject = '', int $paymentId = 0)
  {
    $emailService = \Config\Services::email();
    $parser = \Config\Services::parser();
    $settingsModel = new SettingModel();
    $settings = $settingsModel->first();

    $paymentModel = new InvoiceHasPaymentModel();
    $payment = $paymentModel->find($paymentId);
    $paymentDate = date($settings->date_format, strtotime($payment->date));

    $clientModel = new ClientModel();
    $client = $clientModel->find($clientId);

    $emailService->setFrom($settings->email, $settings->company);
    $emailService->setTo($client->email);
    $emailService->setSubject(lang('application_receipt') . " #" . $payment->reference);

    // Parse email template
    $parseData = [
      'company' => $settings->company,
      'link' => base_url(),
      'logo' => '<img src="' . base_url($settings->logo) . '" alt="' . $settings->company . '"/>',
      'invoice_logo' => '<img src="' . base_url($settings->invoice_logo) . '" alt="' . $settings->company . '"/>',
      'payment_date' => $paymentDate,
      'invoice_id' => $settings->invoice_prefix . $payment->invoice->reference,
      'payment_method' => lang('application_' . $payment->type),
      'payment_reference' => $payment->reference,
      'payment_amount' => number_format($payment->amount, 2),
      'client_firstname' => $client->firstname,
      'client_lastname' => $client->lastname,
      'client_company' => $client->company->name
    ];

    $emailTemplate = file_get_contents(APPPATH . 'Views/' . $settings->template . '/templates/email_receipt.html');
    $message = $parser->setData($parseData)->renderString($emailTemplate);

    $emailService->setMessage($message);
    $emailService->send();
  }
}
