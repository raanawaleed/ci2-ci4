<?= $this->section('content') ?>
<style>
  @media (max-width: 767px) {
    .content-area {
      padding: 0;
    }

    .row.mainnavbar {
      margin-bottom: 0px;
      margin-right: 0px;
    }
  }
</style>

<?= $this->extend('layouts/main') ?>


<div class="grid">
  <div class="grid__col-md-7 dashboard-header">
    <h1><?= sprintf(lang('application_welcome_back'), $this->user->firstname ?? ''); ?></h1>
    <small><?= sprintf(lang('application_welcome_subline'), esc($messages_new[0]->amount ?? 0), esc($event_count_for_today ?? 0)); ?></small>
    <div class="grid__col-md-5 dashboard-header hidden-xs">
      <div class="grid grid--bleed grid--justify-end">
        <div class="grid__col-4 grid__col-lg-3 grid--align-self-center">
          <h6><?= lang('application_tasks'); ?></h6>
          <h2><? // count($tasks); 
              ?></h2>
        </div>
      </div>
    </div>


    <div class="grid__col-sm-12 grid__col-md-8 grid__col-lg-9 grid__col--bleed">
      <div class="grid grid--align-content-start">
        <?php if (isset($this->user->admin) && $this->user->admin) { ?>
          <div class="grid__col-12 update-panel">
            <div class="tile-base">
              <div class="panel-heading red"><span class="title red"><?= lang('application_update_available'); ?></span><span id="hideUpdate" class="pull-right"><i class="ion-close"></i></span></div>
              <div class="panel-content">
                <h2><a href="<?= base_url() ?>settings/updates"><?= lang('application_new_update_is_ready'); ?></a></h2>
              </div>
              <div class="panel-footer">Version <span id="versionnumber"></span></div>
            </div>
          </div>

          <div class="grid__col-6 grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3">
            <div class="tile-base tile-with-icon">
              <div class="tile-icon hidden-md hidden-xs"><i class="ion-ios-analytics-outline"></i></div>
              <div class="tile-small-header">
                <?= lang('application_' . $month); ?> <?= lang('application_payments'); ?>
              </div>
              <div class="tile-body">
                <div class="number" id="number1"><?php if (empty($payments)) {
                                                    echo display_money(0, $core_settings->currency, 0);
                                                  } else {
                                                    echo display_money($payments, $core_settings->currency, 2);
                                                  } ?></div>
              </div>

            </div>
          </div>
          <div class="grid__col-6 grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3">
            <div class="tile-base">
              <div class="tile-icon hidden-md hidden-xs">
                <i class="ion-ios-information-outline"></i>
              </div>
              <div class="tile-small-header">
                <?= lang('application_total_outstanding'); ?>
              </div>
              <div class="tile-body">
                <div class="number" id="number2"><?php if (empty($paymentsoutstanding)) {
                                                    echo display_money(0, $core_settings->currency, 0);
                                                  } else {
                                                    echo display_money($paymentsoutstanding, $core_settings->currency, 2);
                                                  } ?></div>
              </div>

            </div>
          </div>
          <div class="grid__col-6 grid__col-xs-6 grid__col-sm-6  grid__col-md-6 grid__col-lg-3">
            <a href="<?= base_url(); ?>projects/filter/open" class="tile-base">
              <div class="tile-icon hidden-md hidden-xs"><i class="ion-ios-lightbulb-outline"></i></div>
              <div class="tile-small-header"><?= lang('application_open_projects'); ?></div>
              <div class="tile-body">
                <?= $projects_open; ?><small> / <?= $projects_all; ?></small>
              </div>
              <div class="tile-bottom">
                <div class="progress tile-progress tile-progress--green">
                  <div class="progress-bar" role="progressbar" aria-valuenow="<?= $openProjectsPercent ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $openProjectsPercent ?>%"></div>
                </div>
              </div>
            </a>
          </div>
          <div class="grid__col-6 grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3">
            <a href="<?= base_url(); ?>invoices/filter/open" class="tile-base">
              <div class="tile-icon hidden-md hidden-xs"><i class="ion-ios-paper-outline"></i></div>
              <div class="tile-small-header"><?= lang('application_open_invoices_dashboard'); ?></div>
              <div class="tile-body">
                <?= $invoices_open; ?><small> / <?= $invoices_all; ?></small>
              </div>
              <div class="tile-bottom">
                <div class="progress tile-progress tile-progress--orange">
                  <div class="progress-bar" role="progressbar" aria-valuenow="<?= $openInvoicePercent ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $openInvoicePercent ?>%"></div>
                </div>
              </div>
            </a>
          </div>

        <?php } ?>

        <div class="grid__col-12">
          <?php if (isset($tasks)) { ?>
            <div class="stdpad">
              <div class="table-head"><?= $this->lang->line('application_my_open_tasks'); ?></div>
              <div id="main-nano-wrapper" class="nano">
                <div class="nano-content">
                  <ul id="jp-container" class="todo jp-container">
                    <?php
                    $count = 0;
                    $projectname = "";
                    foreach ($tasks as $value):
                      $count = $count + 1;
                      if ($projectname != $value->project->name) {
                        $projectname = $value->project->name;
                        echo "<h5>" . $projectname . "</h5>";
                      }
                    ?>
                      <li class="<?= $value->status; ?> priority<?= $value->priority; ?> list-item">
                        <span class="lbl-">
                          <p class="truncate"><input name="form-field-checkbox" type="checkbox" class="checkbox-nolabel task-check" data-link="<?= base_url() ?>projects/tasks/<?= $value->project_id; ?>/check/<?= $value->id; ?>" <?= $value->status; ?> />
                            <a href="<?= base_url() ?>projects/view/<?= $value->project_id; ?>"><?= $value->name; ?></a>
                          </p>
                        </span>
                        <span class="pull-right hidden-xs" style="margin-top: -43px;">
                          <?php if ($value->public != 0) {  ?><span class="list-button"><i class="fa fa-eye tt" title="" data-original-title="<?= $this->lang->line('application_task_public'); ?>"></i></span><?php } ?>
                          <a href="<?= base_url() ?>projects/tasks/<?= $value->project_id; ?>/update/<?= $value->id; ?>" class="edit-button" data-toggle="mainmodal"><i class="fa fa-edit" title="Modifier"></i></a>
                        </span>
                      </li>
                    <?php
                    endforeach;
                    if ($count == 0) { ?>
                      <div class="empty">
                        <i class="ion-checkmark-round"></i><br>
                        <?= $this->lang->line('application_no_tasks_yet'); ?>
                      </div>
                    <?php } ?>
                  </ul>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>


      </div>
    </div>


    <div class="grid__col-sm-12 grid__col-md-4 grid__col-lg-3 grid__col--bleed">
      <div class="grid grid--align-content-start">
        <div class="grid__col-12">
          <div class="stdpad stdpad--auto-height stdpad--calendar stdpad--blue">
            <div class="table-head"><?= lang('application_calendar'); ?>
              <span class="pull-right">
                <i class="ion-android-sunny calendar-style-toggle calendar-style-light"></i>
                <i class="ion-android-sunny calendar-style-toggle calendar-style-dark hidden"></i>
              </span>
            </div>
            <div id='dashboardCalendar'></div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <script type="text/javascript">
    $(document).ready(function() {
      $(document).on("click", '.calendar-style-toggle', function(e) {
        $(".calendar-style-toggle").toggleClass("hidden");
        $(".stdpad--calendar").toggleClass("stdpad--blue");
        if ($(".calendar-style-light").hasClass("hidden")) {
          localStorage.setItem("calendar_style_light", "1");
        } else {
          localStorage.removeItem("calendar_style_light");
        }
      });
      if (localStorage.calendar_style_light == "1") {
        $(".calendar-style-toggle").toggleClass("hidden");
        $(".stdpad--calendar").toggleClass("stdpad--blue");
      }
      $('#dashboardCalendar').fullCalendar({
        lang: '<? // $langshort; 
                ?>',
        header: {
          left: 'prev',
          center: 'title',
          right: 'next'
        },


        events: [

          <?php if (isset($project_events)) echo $project_events; ?>
          <?php if (isset($events_list)) echo $events_list; ?>
        ],

        eventRender: function(event, element) {
          element.attr('title', event.title);

          if (event.source.className[0] == "google-event") {
            element.attr('target', "_blank");
          }
          if (event.modal == 'true') {
            element.attr('data-toggle', "mainmodal");
          }
          if (event.title != '') {
            element.attr('title', event.title);

            var tooltip = event.title;
            $(element).attr("data-original-title", tooltip)
            $(element).tooltip({
              container: "body",
              trigger: 'hover',
              delay: {
                "show": 100,
                "hide": 50
              }
            })
          }

        },
        eventClick: function(event) {
          if (event.url && event.modal == 'true') {
            NProgress.start();
            var url = event.url;

            if (url.indexOf('#') === 0) {
              $('#mainModal').modal('open');
            } else {
              $.get(url, function(data) {
                $('#mainModal').modal();
                $('#mainModal').html(data);
              }).done(function() {
                NProgress.done();
              });
            }
            return false;
          }
        }

      });

      //chartjs
      <?php if ((isset($this->user->admin) && $this->user->admin)) { ?>
        var ctx = document.getElementById("tileChart");
        var myChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: [<?= $labels ?>],
            datasets: [{
              label: "<?= $this->lang->line("application_owed"); ?>",
              backgroundColor: "rgba(215,112,173,0.3)",
              borderColor: "rgba(215,112,173,1)",
              pointBorderColor: "rgba(0,0,0,0)",
              pointBackgroundColor: "#ffffff",
              pointHoverBackgroundColor: "rgba(237, 85, 101, 0.5)",
              pointHitRadius: 25,
              pointRadius: 1,
              data: [<?= $line2 ?>],
              /* Demo Data => [387, 1108, 987, 2100, 2900, 1023, 1897, 1438, 4100,2861,1000,3872], */
            }, {
              label: "<?= $this->lang->line("application_received"); ?>",
              backgroundColor: "rgba(79,193,233,0.6)",
              borderColor: "rgba(79, 193, 233, 1)",
              pointBorderColor: "rgba(0,0,0,0)",
              pointBackgroundColor: "#ffffff",
              pointHoverBackgroundColor: "rgba(79, 193, 233, 1)",
              pointHitRadius: 25,
              pointRadius: 1,
              data: [<?= $line1 ?>],
              /* Demo Data => [1702, 2310, 1900, 3800, 4123, 3000, 3210, 5439, 3000,4000,3234,5539], */
            }, ]
          },
          options: {
            tooltips: {
              xPadding: 10,
              yPadding: 10,
              cornerRadius: 2,
              mode: 'label',
              multiKeyBackground: 'rgba(0,0,0,0.2)'
            },
            legend: {
              display: false
            },
            scales: {
              yAxes: [{
                display: false,
                ticks: {
                  beginAtZero: true
                }
              }],
              xAxes: [{
                display: false
              }]
            }
          }
        });

        //Count up settings
        var options = {
          useEasing: true,
          useGrouping: true,
          <?= get_money_format(); ?>

        };
        var number1 = new CountUp("number1", 0, <?php if (empty($payments)) {
                                                  echo 0;
                                                } else {
                                                  echo $payments;
                                                } ?>, 2, 1.5, options);
        number1.start();

        var number2 = new CountUp("number2", 0, <?php if (empty($paymentsoutstanding)) {
                                                  echo 0;
                                                } else {
                                                  echo $paymentsoutstanding;
                                                } ?>, 2, 1.5, options);
        number2.start();
      <?php } ?>

      function tick() {
        $('ul.dash-messages li:first').slideUp('slow', function() {
          $(this).appendTo($('ul.dash-messages')).fadeIn('slow');
        });
      }

      function tick2() {
        // $('ul.activity__list li:first').slideUp('slow', function () { $(this).appendTo($('ul.activity__list')).fadeIn('slow'); });
      }
      <?php if (isset($message) && count($message) > 4) { ?>
        setInterval(function() {
          tick()
        }, 5000);
      <?php } ?>
      <?php if (isset($recent_activities) && count($recent_activities) > 4) { ?>
        setInterval(function() {
          tick2()
        }, 8000);
      <?php } ?>
      $('ul.notificationlist li').click(function() {
        $('ul.notificationlist li:first').slideUp('slow', function() {
          $(this).appendTo($('ul.notificationlist')).fadeIn('slow');
        });
      });




    });
  </script>

  <?php if (isset($this->user->admin) && $this->user->admin) { ?>
    <script type="text/javascript">
      $(document).ready(function() {
        var url = "<?= base_url() ?>settings/checkForUpdates";

        $('#hideUpdate').click(function() {
          var versionnumber = $("#versionnumber").html();
          var updatename = 'hideUpdate' + versionnumber;
          localStorage.setItem('hideupdate', updatename);
          $(".update-panel").velocity({
            'opacity': '0',
            'height': '0px',
            'padding-top': '0px'
          }, "easeOutQuint");
        });
        <?php if ($this->user->admin == "1") { ?>
          $.getJSON(url, function(data) {
            console.log(data.newUpdate);
            if (data.newUpdate == true) {
              var updateid = 'hideUpdate' + data.lastupdate;
              if (localStorage.getItem('hideupdate') != updateid) {
                $("#versionnumber").html(data.lastupdate);
                $(".update-panel").velocity({
                  'opacity': '1',
                  'height': '122px',
                  'padding-top': '15px'
                }, "easeOutQuint");
              }
            }
          });

        <?php } ?>
      });
    </script>
  <?php } ?>
  <?= $this->endSection() ?>