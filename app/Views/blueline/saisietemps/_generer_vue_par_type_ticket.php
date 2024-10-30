<?php if(isset($planification)) :?>
    <?php if(isset($saisieParTicket[$config_type_ticket->alias][$ticket->id])):?>
        <!--S'il y'a une planification saisie alors l'afficher -->
        <?php   $this->load->view('blueline/saisietemps/_tab_saisie', array(
                'xsaisieUserTicket'=>$saisieParTicket[$config_type_ticket->alias][$ticket->id],
                'xkey_ticket'=>$ticket->id,
                'xticketsParDefaut'=>$ticketsParDefaut,
                'xtickets'=>$tickets,
                'index'=>$i,
                'xmois'=>$mois,
                'xannee'=>$annee,
                'xtype_ticket' => $config_type_ticket->id,
                'xutilisateurCourant'=>$utilisateurCourant->id
                ,'xmessage'=>"Vous avez planifiez les horaires de ce ticket."));
                ?>
    <?php else: ?>
          <!-- Si pas de planification saisie alors voir si l'utilisateur a fait une saisie -->
         <?php if(isset($tempsUserParTiket[$config_type_ticket->alias][$ticket->id])):?>
            <?php   $this->load->view('blueline/saisietemps/_tab_saisie', array(
                    'xsaisieUserTicket'=>$tempsUserParTiket[$config_type_ticket->alias][$ticket->id],
                    'xkey_ticket'=>$ticket->id,
                    'xticketsParDefaut'=>$ticketsParDefaut,
                    'xtickets'=>$tickets,
                    'index'=>$i,
                    'xmois'=>$mois,
                    'xannee'=>$annee,
                    'xtype_ticket' => $config_type_ticket->id,
                    'xutilisateurCourant'=>$utilisateurCourant->id,
                    'xmessage'=>"Vous n'avez pas  planifié auparavant  les horaires de ce ticket. Cependant l'utilisateur a saisi ses temps."));?>


        <?php endif; ?>
    <?php endif; ?>
<?php else:?>
    <!-- Ecran de la saisie -->
    <?php if(isset($saisieParTicket[$config_type_ticket->alias][$ticket->id])):?>
        <!-- S'il y'a une saisie effectuer alors l'afficher  -->
        <?php   $this->load->view('blueline/saisietemps/_tab_saisie', array(
            'xsaisieUserTicket'=>$saisieParTicket[$config_type_ticket->alias][$ticket->id],
            'xkey_ticket'=>$ticket->id,
            'xticketsParDefaut'=>$ticketsParDefaut,
            'xtickets'=>$tickets,
            'index'=>$i,
            'xmois'=>$mois,
            'xannee'=>$annee,
            'xtype_ticket' => $config_type_ticket->id,
            'xutilisateurCourant'=>$utilisateurCourant->id));?>
        <!-- //else: 
            Si pas de saisie alors voir si l'administrateur a fait une planification 
          Ce cas n'existe pas
          'xmessage'=>"L'administrateur vous a planifié des horaires sur ce ticket. Vous pouvez les modifiés.")); -->

    <?php endif; ?>
<?php endif; ?>