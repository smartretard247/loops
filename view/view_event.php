<center>
<?php if($_SESSION['valid_user']) : ?>
    <?php $eventDate = $db->SafeFetch("SELECT EventDate FROM schedule WHERE ID = $viewID");
    
        $eventPackage[0] = $db->SafeFetchAll("SELECT ID, AtName, Number, Total, Seat FROM reservations WHERE EventID = :0 AND Seat = 'VIP'",array($viewID));
        $eventPackage[1] = $db->SafeFetchAll("SELECT ID, AtName, Number, Total, Seat FROM reservations WHERE EventID = :0 AND Seat = 'Ghost'",array($viewID));
        #$eventPackage[2] = $db->SafeFetchAll("SELECT AtName, Number, Total FROM reservations WHERE EventID = :0 AND Seat = 'Feature'",array($viewID));
    ?>
    
    <table id="orders" class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th>
                <?php if($_SESSION['admin_enabled']) : ?>
                    <a href="?action=edit_event&id=<?php echo $viewID; ?>"><?php echo $eventDate['EventDate']; ?></a>
                <?php else : ?>
                    <?php echo $eventDate['EventDate']; ?>
                <?php endif; ?>
                <?php if($_SESSION['editing']) : ?>
                    <form action="core/delete_event.php" method="post" style="display: inline;">
                        <input name="action" type="hidden" value="delete_event"/>
                        <input name="eventID" type="hidden" value="<?php echo $viewID; ?>"/>
                        <input type="submit" value="X" onclick="return confirm('Are you sure you want to delete this event?')"/>
                    </form>
                <?php endif; ?>
            </th>
        </tr>
        
        <?php $i = 0; foreach($eventPackage as $package) : ?>
            <tr>
                <td><b><?php echo $availablePackages[$i]; ?></b></td>
            </tr>

            <?php foreach ($package as $next) : ?>
                <tr>
                    <td>
                        <?php echo $next['AtName']; ?>
                        <?php if($_SESSION['editing']) : ?>
                            <form action="core/delete_seat.php" method="post" style="display: inline;">
                                <input name="action" type="hidden" value="delete_seat"/>
                                <input name="seat" type="hidden" value="<?php echo $next['Seat']; ?>"/>
                                <input name="atName" type="hidden" value="<?php echo $next['AtName']; ?>"/>
                                <input name="reservationID" type="hidden" value="<?php echo $next['ID']; ?>"/>
                                <input name="eventID" type="hidden" value="<?php echo $viewID; ?>"/>
                                <input type="submit" value="X" onclick="return confirm('Are you sure you want to remove the user from this event?')"/>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php NoDataRow($package[0], 1, "No " . $availablePackages[$i++] . " in this event") ?>
        <?php endforeach; ?>
            
    </table>
<?php else : ?>
    <p class="error">You do not have permission to view this page.
<?php endif; ?>

</center>