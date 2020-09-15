<center>
<?php if($_SESSION['valid_user']) : ?>
    <?php $eventVIPs = $db->SafeFetchAll("SELECT AtName, Number, Total FROM reservations WHERE EventID = :0 AND Seat = 'VIP'",array($viewID)); ?>
    <?php $eventGhosts = $db->SafeFetchAll("SELECT AtName, Number, Total FROM reservations WHERE EventID = :0 AND Seat = 'Ghost'",array($viewID)); ?>
    <?php $eventDate = $db->SafeFetch("SELECT EventDate FROM schedule WHERE ID = $viewID"); ?>
    
    <table id="orders" class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th><?php echo $eventDate['EventDate']; ?></th>
        </tr>
        
        <tr>
            <td><b>VIP</b></td>
        </tr>
        
        <?php foreach ($eventVIPs as $nextV) : ?>
            <tr>
                <td>
                    <?php echo $nextV['AtName']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php NoDataRow($nextV, 1, 'No VIPs in this event') ?>    
            
        <tr>
            <td><b>Ghost</b></td>
        </tr>
        
        <?php foreach ($eventGhosts as $nextG) : ?>
            <tr>
                <td>
                    <?php echo $nextG['AtName']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php NoDataRow($nextG, 1, 'No ghosts in this event') ?>
    </table>
<?php else : ?>
    <p class="error">You do not have permission to view this page.
<?php endif; ?>

</center>