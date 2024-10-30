<div id="template-tab" style="display: none">
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="table-head-2" style="color: #505458; background: #FFFFFF;">
                <div class="tab-saisie col-md-9" style="">
                    <div class="form-group" style="    height: 46px !important;">
                        <select name="ticket[X]"  class="chosen-select inbox-folder chosen_select_L" title="Inbox" style="width:100%">
                            <?php foreach($xticketsToadd[$type_ticket_defaut->alias] as $tkt):?>
                                <option value="<?=$tkt->id.'-'.$type_ticket_defaut->id;?>">
                                    <?=(($tkt->ordre == 1)? "---":"").$tkt->subject?>
                                </option>
                            <?php  endforeach; ?>
                            
                            <?php foreach($xticketsToadd[$type_ticket_projet->alias] as $tkt):?>
                                <option value="<?=$tkt->id.'-'.$type_ticket_projet->id;?>"  >
                                    <?php $msg = $tkt->reference." - ";     if(! is_null($tkt->project_id)) $msg .= $tkt->project_id->name ; ?>
                                    <?php $msg .= " - "; if(! is_null($tkt->sub_project_id)) $msg .= $tkt->sub_project_id->name; ?>
                                    <?php $msg .= " - ". $tkt->subject; ?>
                                    <?=$msg; ?>
                                </option>
                            <?php  endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="tab-saisie col-md-2  col-sm-12" style="">
                    <div class="form-group" style="    height: 46px !important;" readonly="readonly">
                            <input type="hidden" value="<?=isset($ticket->text)? $ticket->text:''?>">
                    </div>
                </div> 
                <div class=" col-md-1 col-sm-12" style="">
                    <div class="btn-group pull-right MarginTop" style="margin-right:5px;">
                        <a class="btn btn-danger delete-trigger" title="<?=$this->lang->line('application_delete');?>" rel="tab-saisi-ticket_<?=$xindex?>"
                           data-id="<?=$xindex?>" data-href="" data-toggle="modal" data-target="#confirm-delete-tab">
                            <span class="menu-icon">  <i class="fa fa-trash" title="<?=$this->lang->line('application_delete');?>"></i> </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-12">
            <div class="tab-div table-responsive ">

                <table class="table table-hover" rel="" cellspacing="0" cellpadding="0">
                    <thead>
                    <?php foreach($xjoursTravailMois as $jour) :?>
                        <th align="center" class="" style="width:5px !important;" >
                            <?php echo strtoupper(substr($this->lang->line('application_'.$jour->weekdaylib), 0, 2)); ?>
                            </br>
                            <?php echo $jour->day; ?>
                        </th>
                    <?php endforeach; ?>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach($xjoursTravailMois as $jour) :?>
                                <td align="center" class="" style="width:5px !important;">
                                    <input  type="time" name="nbHeures[X][0][<?=$jour->day; ?>]" value="00:00"
                                            min="00:00" <?php  echo ($limite_heures === false)? "":"max= '".$limite_heures.":00'"; ?> >
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <?php foreach($xjoursTravailMois as $jour) :?>
                                <td align="center"  style="width:5px !important;" title="" data-id="X" data-jour-id="<?=$jour->day; ?>"  data-date-id="<?=$jour->day .'-'.$moisCourant; ?>"  data-value="<?=$jour->autreSaisie; ?>" data-toggle="modal" data-target="#zoomModal">
                                    <input  class="autre-saisie-to-zoom" type="text"  name="nbHeures[X][1][<?=$jour->day; ?>]" value="<?=$jour->autreSaisie; ?>" >
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
