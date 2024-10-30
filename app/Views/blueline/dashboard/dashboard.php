<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php

use App\Models\InvoiceModel;
use App\Models\ProjectModel;

$projectModel = new ProjectModel();
$invoiceModel = new InvoiceModel();
$projects_open = $projectModel->getOpenProjectsCount();
$projects_all = $projectModel->getAllProjectsCount();
$invoices_open = $invoiceModel->getOpenInvoicesCount();
$invoices_all = $invoiceModel->getAllInvoicesCount();

if ($user['admin'] == 1): ?>
  <div class="panel-wrapper update-panel">
    <div class="panel-heading red">
      <span class="title red"><?= lang('application_update_available'); ?></span>
      <span class="pull-right hidden"><i class="ion-close"></i></span>
    </div>
    <div class="panel-content">
      <h2><a href="<?= base_url('settings/updates'); ?>"><?= lang('application_new_update_is_ready'); ?></a></h2>
    </div>
    <div class="panel-footer">Version <span id="versionnumber"></span></div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="stdpad-small red">
        <div class="icon"><i class="ion-ios-lightbulb-outline"></i></div>
        <div class="stats">
          <div class="number"><?= esc($projects_open); ?><small> / <?= esc($projects_all); ?></small></div>
          <div class="text"><?= lang('application_open_projects'); ?></div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="stdpad-small orange">
        <div class="icon"><i class="ion-ios-paper-outline"></i></div>
        <div class="stats">
          <div class="number"><?= esc($invoices_open); ?><small> / <?= esc($invoices_all); ?></small></div>
          <div class="text"><?= lang('application_open_invoices'); ?></div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="stdpad-small blue">
        <div class="icon"><i class="ion-ios-analytics-outline"></i></div>
        <div class="stats two">
          <div class="number" id="number1"><?= esc(display_money($payments[0]->summary ?? 0, $core_settings->currency, 2)); ?></div>
          <div class="text"><?= lang('application_' . $month); ?> <?= lang('application_payments'); ?></div>
          <div class="number" id="number2"><?= esc(display_money($payments_outstanding[0]->summary ?? 0, $core_settings->currency, 2)); ?></div>
          <div class="text"><?= lang('application_outstanding_payments'); ?></div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-md-4">
    <div class="stdpad">
      <div class="table-head"><?= lang('application_events'); ?><small> (<?= esc($eventcount); ?>)</small></div>
      <ul class="eventlist">
        <?php if (!empty($events)): ?>
          <?php foreach ($events as $value): ?>
            <li>
              <p class="truncate"><?= esc($value); ?></p>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li>
            <p class="truncate"><?= lang('application_no_events_yet'); ?></p>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <div class="col-md-4">
    <?php if (isset($tasks)): ?>
      <div class="stdpad">
        <div class="table-head"><?= lang('application_my_open_tasks'); ?></div>
        <div id="main-nano-wrapper" class="nano">
          <div class="nano-content">
            <ul id="jp-container" class="todo jp-container">
              <?php foreach ($tasks as $task): ?>
                <h5><?= esc($task->project->name); ?></h5>
                <li class="<?= esc($task->status); ?>">
                  <span class="lbl-">
                    <p class="truncate">
                      <input type="checkbox" class="checkbox-nolabel task-check" data-link="<?= base_url('projects/tasks/' . $task->project_id . '/check/' . $task->id); ?>" <?= esc($task->status); ?> />
                      <a href="<?= base_url('projects/view/' . $task->project_id); ?>"><?= esc($task->name); ?></a>
                    </p>
                  </span>
                  <span class="pull-right">
                    <img class="img-circle list-profile-img" width="21px" height="21px" src="<?= esc($task->user->userpic ?? get_gravatar($task->user->email)); ?>">
                  </span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="col-md-4">
    <?php if (isset($message)): ?>
      <div class="stdpad">
        <div class="table-head"><?= lang('application_recent_messages'); ?></div>
        <ul class="dash-messages">
          <?php foreach ($message as $msg): ?>
            <li>
              <a href="<?= base_url('messages'); ?>">
                <img class="userpic img-circle" src="<?= esc($msg->userpic_u ?? get_gravatar($msg->email_u)); ?>">
                <h5><?= esc($msg->sender_u ?? $msg->sender_c); ?> <small><?= time_ago($msg->time); ?></small></h5>
                <p class="truncate" style="width:80%">
                  <?= ($msg->status == "New") ? '<span class="new"><i class="fa fa-circle-o"></i></span>' : ''; ?>
                  <?= esc($msg->subject); ?>
                </p>
              </a>
            </li>
          <?php endforeach; ?>
          <?php if (empty($message)): ?>
            <li><?= lang('application_no_messages'); ?></li>
          <?php endif; ?>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Chart.js Section -->
<canvas id="tileChart" style="height: 400px;"></canvas>

<script>
  $(document).ready(function() {
    var ctx = $("#tileChart").get(0).getContext("2d");

    var data = {
      labels: <?= $labels; ?>,
      datasets: [{
        label: "<?= lang('application_received'); ?>",
        backgroundColor: "#11A7DB",
        borderColor: "#11A7DB",
        data: <?= $line1; ?>
      }]
    };

    var options = {
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: "#11A7DB"
          }
        },
        x: {
          ticks: {
            color: "#11A7DB"
          }
        }
      },
      maintainAspectRatio: false
    };

    new Chart(ctx, {
      type: "line",
      data: data,
      options: options
    });
  });
</script>

<?= $this->endSection() ?>