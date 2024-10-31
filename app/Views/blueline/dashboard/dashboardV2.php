<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?= $this->include('layouts/sidebar') ?>
<?php

use App\Models\EventModel;
use App\Models\InvoiceModel;
use App\Models\ProjectModel;

$projectModel = new ProjectModel();
$invoiceModel = new InvoiceModel();
$eventModel = new EventModel();
$projects_open = $projectModel->getOpenProjectsCount();
$projects_all = $projectModel->getAllProjectsCount();
$invoices_open = $invoiceModel->getOpenInvoicesCount();
$invoices_all = $invoiceModel->getAllInvoicesCount();
$eventcount = $eventModel->getAllEvents();
?>

<style>
  @media (max-width: 767px) {
    .content-area {
      padding: 0;
    }

    .row.mainnavbar {
      margin: 0;
    }
  }
</style>

<div class="grid">
  <div class="grid__col-md-7 dashboard-header">
    <h1><?= sprintf(lang('application_welcome_back'), esc($this->user->firstname ?? '')); ?></h1>
    <small><?= sprintf(lang('application_welcome_subline'), esc($messages_new[0]->amount ?? 0), esc($event_count_for_today ?? 0)); ?></small>
  </div>

  <?php if (isset($this->user->admin) && $this->user->admin): ?>
    <div class="grid__col-12 update-panel">
      <div class="tile-base">
        <div class="panel-heading red">
          <span class="title red"><?= lang('application_update_available'); ?></span>
          <span id="hideUpdate" class="pull-right"><i class="ion-close"></i></span>
        </div>
        <div class="panel-content">
          <h2><a href="<?= base_url('settings/updates'); ?>"><?= lang('application_new_update_is_ready'); ?></a></h2>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="grid__col-md-8">
    <div class="grid">
      <div class="grid__col-6">
        <div class="tile-base tile-with-icon">
          <div class="tile-icon hidden-md hidden-xs"><i class="ion-ios-analytics-outline"></i></div>
          <div class="tile-small-header">
            <?= lang('application_' . $view_data['month']); ?> <?= lang('application_payments'); ?>
          </div>
          <div class="tile-body">
            <div class="number"><?= $view_data['payments'] ?? 0; ?></div>
          </div>
        </div>
      </div>

      <div class="grid__col-6">
        <div class="tile-base">
          <div class="tile-icon hidden-md hidden-xs"><i class="ion-ios-information-outline"></i></div>
          <div class="tile-small-header"><?= lang('application_total_outstanding'); ?></div>
          <div class="tile-body">
            <div class="number"><?= $view_data['paymentsOutstandingMonth'] ?? 0; ?></div>
          </div>
        </div>
      </div>

      <div class="grid__col-6">
        <a href="<?= base_url(); ?>projects/filter/open" class="tile-base">
          <div class="tile-icon hidden-md hidden-xs"><i class="ion-ios-lightbulb-outline"></i></div>
          <div class="tile-small-header"><?= lang('application_open_projects'); ?></div>
          <div class="tile-body">
            <?= $projects_open; ?><small> / <?= $projects_all; ?></small>
          </div>
          <div class="tile-bottom">
            <div class="progress tile-progress tile-progress--green">
              <div class="progress-bar" role="progressbar" style="width: <?= $openProjectsPercent ?? 0 ?>%"></div>
            </div>
          </div>
        </a>
      </div>

      <div class="grid__col-6">
        <a href="<?= base_url(); ?>invoices/filter/open" class="tile-base">
          <div class="tile-icon hidden-md hidden-xs"><i class="ion-ios-paper-outline"></i></div>
          <div class="tile-small-header"><?= lang('application_open_invoices_dashboard'); ?></div>
          <div class="tile-body">
            <?= $invoices_open; ?><small> / <?= $invoices_all; ?></small>
          </div>
          <div class="tile-bottom">
            <div class="progress tile-progress tile-progress--orange">
              <div class="progress-bar" role="progressbar" style="width: <?= $openInvoicePercent ?? 0 ?>%"></div>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <div class="grid__col-md-4">
    <div class="stdpad stdpad--calendar">
      <div class="table-head">
        <?= lang('application_calendar'); ?>
        <span class="pull-right">
          <i class="ion-android-sunny calendar-style-toggle calendar-style-light"></i>
          <i class="ion-android-sunny calendar-style-toggle calendar-style-dark hidden"></i>
        </span>
      </div>
      <div id="dashboardCalendar"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('.calendar-style-toggle').on('click', function() {
      $(".calendar-style-toggle").toggleClass("hidden");
      $(".stdpad--calendar").toggleClass("stdpad--blue");
      localStorage.calendar_style_light = $(".calendar-style-light").hasClass("hidden") ? "1" : "0";
    });

    if (localStorage.calendar_style_light == "1") {
      $(".calendar-style-toggle").toggleClass("hidden");
      $(".stdpad--calendar").toggleClass("stdpad--blue");
    }

    $('#dashboardCalendar').fullCalendar({
      lang: '<?= $langshort ?? 'en' ?>',
      header: {
        left: 'prev',
        center: 'title',
        right: 'next'
      },
      events: [<?= $project_events ?? '' ?>, <?= $events_list ?? '' ?>],
      eventRender: function(event, element) {
        element.attr('title', event.title);
        if (event.modal === 'true') {
          element.attr('data-toggle', "mainmodal");
        }
      },
      eventClick: function(event) {
        if (event.url && event.modal === 'true') {
          NProgress.start();
          $.get(event.url, function(data) {
            $('#mainModal').modal();
            $('#mainModal').html(data);
          }).done(function() {
            NProgress.done();
          });
          return false;
        }
      }
    });
  });
</script>

<?= $this->endSection() ?>