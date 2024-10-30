<page>
    <page_header>
        <table>
            <tr>
                <td>
                    <h1 style="text-align: center"><?=$name_projext[0]->name;?></h1>
                </td>
            </tr>
        </table>
    </page_header>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <table border="1">
            <tr>
                <td width="120">Taches</td>
                <td width="120">Personne</td>
                <td width="200">Date</td>
                <td width="170">Nombres des heures total</td>
            </tr>
            <?php
            foreach($all as $value){
            ?>
            <tr>
                <td width="120">
                    <?=$value->name;?>
                </td>
                <td width="120">
                    <?=$value->name_user;?>
                </td>
                <td width="200">
                    <?php
                        foreach($all_tasks as $values){
                            if($values->id_task==$value->id){
                                echo $values->date_task." : ".gmdate("H:i:s", $values->time_task)."<br>";
                            }
                        }
                    ?>
                </td>
                <td width="120">
                    <?=gmdate("H:i:s", $value->time_spent);?>
                </td>
            </tr>
                <?php
            }
            ?>
        </table>
    <p style="text-align: right">Nombre des heures total : <?=$count_heure;?></p>
    <page_footer>
    </page_footer>

</page>