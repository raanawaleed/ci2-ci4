<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td width = "10%">ID</td>
        <td width = "20%">NAME</td>
    </tr>

    <?php foreach($csvData as $field){?>
        <tr>
            <td><?php echo $field['User']?></td>
            <td><?php echo $field['Clocking']?></td>
        </tr>
    <?php }?>
</table>