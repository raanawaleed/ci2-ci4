<div id="tab-saisi-ticket_<?=$index?>" class="tab-saisi-ticket" >
    <div class="row">
        <?php if(isset($xmessage) || count($xsaisieUserTicket)>0) :?>
            <div class="col-xs-12 col-md-12">
                <div class="table-head-2" style="line-height: 20px !important;height: 55px !important;margin-top: 0px;padding-top: 10px;">
                    <div class="" >
                        <div class="alert alert-info" style="padding-bottom: 5px; padding-top: 3px; font-size: 11px!important;">
                            <?php echo   $this->lang->line('application_derniere_maj') . $xsaisieUserTicket->created_by ." - ".  $xsaisieUserTicket->created_at ;
                            ?>
                            <?php if(isset($xmessage)) : ?>
                                <br><?php echo $xmessage; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-xs-12 col-md-12">
            <div class="table-head-2" style="color: #505458; background: #FFFFFF;">
                <div class="tab-saisie col-md-9" style="">
                    <div class="form-group" style="    height: 46px !important;">
                        <select name="ticket[<?=$index;?>]"  readonly class="readonly chosen-select inbox-folder chosen_select_L" title="Inbox" style="width:100%">
                            
                            <?php foreach($xticketsParDefaut as $tkt):?>
                                <option value="<?=$tkt->id.'-'.$type_ticket_defaut->id;?>" <?=(($tkt->id==$xkey_ticket) && ($xtype_ticket === $type_ticket_defaut->id)? "selected":"") ?>>
                                    <?=(($tkt->ordre == 1)? "---":"").$tkt->subject?>
                                </option>
                            <?php  endforeach;?>
                            <?php foreach($xtickets as $tkt):?>
                                <option value="<?=$ticket->id.'-'.$type_ticket_projet->id;?>" <?=(($tkt->id==$xkey_ticket) && ($xtype_ticket === $type_ticket_projet->id)? "selected":"") ?>>
                                    <?php $msg = "";     if(! is_null($tkt->project_id)) $msg .= $tkt->project_id->name ; ?>
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
                        <?php foreach($xtickets as $tkt):?>
                            <?php if($tkt->id==$xkey_ticket && $xtype_ticket === $type_ticket_projet->id): ?>
                                <?=(isset($tkt->text)?$tkt->text:"") ?>
                            <?php endif; ?>
                        <?php endforeach;  ?>
                    </div>
                </div>
                <div class=" col-md-1 col-sm-12" style="">
                    <div class="btn-group pull-right MarginTop" style="margin-right:5px;">
                        <a class="btn btn-danger delete-trigger" title="<?=$this->lang->line('application_delete');?>"  data-href="<?php echo (isset($planification)? site_url('planification/deleteTempsTicket/'.$xkey_ticket.'/'.$mois.'/'.$annee.'/'.$xutilisateurCourant):site_url('saisietemps/deleteTempsTicket/'.$xkey_ticket.'/'.$xmois.'/'.$xannee));?>" data-toggle="modal" data-target="#confirm-delete-db" <?=($validation_mois ==1)? "disabled":"";?>>
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
                        <?php if(isset($xsaisieUserTicket->temps)) :?>
                            <?php foreach($xsaisieUserTicket->temps as $jour) :?>
                                 <th align="center" class="" style="width:5px !important;" >
                                        <?php echo strtoupper(substr($this->lang->line('application_'.$jour->weekdaylib), 0, 2)); ?>
                                        </br>
                                        <?php echo $jour->day; ?>
                                    </th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <tr rel="<?=$index;?>">
                            <?php if(isset($xsaisieUserTicket->temps)) :?>
                                <?php foreach($xsaisieUserTicket->temps as $jour) :;?>
                                    <td align="center" class="" style="width:5px !important;">
                                        <input  type="time" class="row_ch" name="nbHeures[<?=$index;?>][0][<?=$jour->day; ?>]" value="<?=$jour->nbhours; ?>" min="00:00" <?php  echo ($limite_heures === false)? "":"max= '".$limite_heures.":00'"; ?>
                                         <?=($validation_mois ==1)? "disabled":"";?>
                                        >
                                    </td>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                        <tr rel="<?=$index;?>">
                            <?php if(isset($xsaisieUserTicket->temps)) :?>
                                <?php foreach($xsaisieUserTicket->temps as $jour) :;?>
                                    <td align="center" class="row_ch" style="width:5px !important;"  data-id="<?=$index;?>" data-jour-id="<?=$jour->day; ?>"  data-date-id="<?=$jour->day .'-'.$moisCourant; ?>" data-value="<?=$jour->autreSaisie; ?>" data-toggle="modal" data-target="#zoomModal">
                                        <input  class="autre-saisie-to-zoom row_ch" type="text"  name="nbHeures[<?=$index;?>][1][<?=$jour->day; ?>]" value="<?=$jour->autreSaisie; ?>"
                                            <?=($validation_mois ==1)? "disabled":"";?>
                                        >

                                    </td>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>
