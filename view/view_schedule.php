<center>
<?php if($_SESSION['valid_user']) : ?>
    <?php 
        $orders = $db->SafeFetchAll("SELECT * FROM schedule WHERE EventDate >= NOW()-1 AND Deleted = 0 ORDER BY $sortby $desc");
        $nextGhost = $db->SafeFetchAll("SELECT * FROM schedule WHERE EventDate >= NOW()-1 AND (Ghost > 0) AND Deleted = 0 ORDER BY EventDate LIMIT 1");
        $nextVIP = $db->SafeFetchAll("SELECT * FROM schedule WHERE EventDate >= NOW()-1 AND (VIP > 0) AND Deleted = 0 ORDER BY EventDate LIMIT 1");
        $columns = 4;
    ?>
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th colspan="5">Next Open VIP Slot</th>
        </tr>
        <tr>
            <td rowspan="4">Date: <?php echo $nextVIP[0]['EventDate']; ?></td>
            <td rowspan="4">Seats: <?php echo $nextVIP[0]['VIP']; ?></td>
            <td>
                <form action="core/submit_reservation.php" method="post">
                    <input name="action" type="hidden" value="reserve_seat"/>
                    <input name="seat" type="hidden" value="VIP"/>
                    <input name="eventID" type="hidden" value="<?php echo $nextVIP[0]['ID']; ?>"/>
                    @Name: <input name="atName" type="text" title="Enter @name of customer"/>
                    
                    </td>
                    <td>
                    Pack: <select name="number" title="Enter package size">
                        <option value="1">1</option>
                        <option value="3">3</option>
                        <option value="8">8</option>
                    </select>
                    </td>
                    
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Reserve"/>
                </form>
            </td>
        </tr>
    </table>
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th colspan="5">Next Open Ghost Slot</th>
        </tr>
        <tr>
            <td rowspan="4">Date: <?php echo $nextGhost[0]['EventDate']; ?></td>
            <td rowspan="4">Seats: <?php echo $nextGhost[0]['Ghost']; ?></td>
            <td>
                <form action="core/submit_reservation.php" method="post">
                    <input name="action" type="hidden" value="reserve_seat"/>
                    <input name="seat" type="hidden" value="Ghost"/>
                    <input name="eventID" type="hidden" value="<?php echo $nextGhost[0]['ID']; ?>"/>
                    @Name: <input name="atName" type="text" title="Enter @name of customer"/>
                    
                    </td>
                    <td>
                    Pack: <select name="number" title="Enter package size">
                        <option value="1">1</option>
                        <option value="3">3</option>
                        <option value="8">8</option>
                    </select>
                    </td>
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Reserve"/>
                </form>
            </td>
        </tr>
    </table>
    
    <table id="orders" class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th colspan="<?php echo $columns; ?>">All Upcoming Events</th>
        </tr>
        <tr>
            <th><a href="index.php?action=view_schedule&s=EventDate">&#x25B2;</a>Date<a href="index.php?action=view_schedule&s=EventDate&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_schedule&s=VIP">&#x25B2;</a>VIPs<a href="index.php?action=view_schedule&s=VIP&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_schedule&s=Ghost">&#x25B2;</a>Ghosts<a href="index.php?action=view_schedule&s=Ghost&d=1">&#x25BC;</a></th>
            <th>Options</th>
        </tr>
        <?php if($orders) { foreach ($orders as $torders) : ?>
            <tr>
                <td>
                    <a href="?action=view_event&id=<?php echo $torders['ID'] ?>"><?php echo $torders['EventDate']; ?></a>
                </td>
                <td>
                    <?php echo $torders['VIP']; ?>
                </td>
                <td>
                    <?php echo $torders['Ghost']; ?>
                </td>
                <td>
                    <?php if($_SESSION['admin_enabled']) : ?>
                        <form action="core/delete_event.php" method="post">
                            <input name="action" type="hidden" value="delete_event"/>
                            <input name="eventID" type="hidden" value="<?php echo $torders['ID']; ?>"/>
                            <input type="submit" value="X" onclick="return confirm('Are you sure you want to delete this event?')"/>
                        </form>
                    <?php else : ?>
                        None
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; } ?>
        <?php NoDataRow($torders, $columns, 'No events scheduled.') ?>
    </table>
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <td>
                <form action="index.php" method="post">
                    <input name="action" type="hidden" value="view_user"/>
                    @Name: <input name="atName" type="text" title="Enter @name of customer"/>
            </td>
            <td>
                <input type="submit" value="Search"/>
                </form>
            </td>
        </tr>
    </table>
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th colspan="2">
                Waitlist
            </th>
        </tr>
        
        <tr>
            <td>
                <?php if($pendingVIPs) : ?>
                    <table class="topmargin" style="width: 100%; border-bottom: solid 1px black">
                        <tr>
                            <th colspan="1">Pending VIPs</th>
                        </tr>
                        <?php foreach ($pendingVIPs as $pendingV) : ?>
                            <tr>
                                <td>
                                    <?php echo $pendingV['AtName'] . " (" . $pendingV['Number'] . " of " . $pendingV['Total'] . ")"; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else : ?>
                    No pending VIPs.
                <?php endif; ?>
            </td>
            <td>
                <?php if($pendingGhosts) : ?>
                    <table class="topmargin" style="width: 100%; border-bottom: solid 1px black">
                        <tr>
                            <th colspan="1">Pending Ghosts</th>
                        </tr>
                        <?php foreach ($pendingGhosts as $pendingG) : ?>
                            <tr>
                                <td>
                                    <?php echo $pendingG['AtName'] . " (" . $pendingG['Number'] . " of " . $pendingG['Total'] . ")"; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else : ?>
                    No pending ghosts.
                <?php endif; ?>
            </td>
        </tr>
    </table>
<?php else : ?>
    <p>You do not have permission to view this page.</p>
    <?php include 'view/home.php'; ?>
<?php endif;
    
if($_SESSION['admin_enabled']) : ?>
    <?php 
        $pendingGhosts = $db->SafeFetchAll("SELECT * FROM pending WHERE Seat = 'Ghost' ORDER BY ID");
        $pendingVIPs = $db->SafeFetchAll("SELECT * FROM pending WHERE Seat = 'VIP' ORDER BY ID");
    ?>
    
    <br/>--ADMIN ONLY--
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th colspan="3">Schedule Event</th>
        </tr>
        <tr>
            <td rowspan="4">
                <form action="core/schedule_event.php" method="post">
                    <input name="action" type="hidden" value="schedule_event"/>
                    VIP Slots: <input name="vips" type="text" value="5" size="1"/>
                </td>
                <td rowspan="4">
                    Ghost Slots: <input name="ghosts" type="text" value="8" size="1"/>
                </td>
                <td>
                    Date: <input name="eventDate" type="date"/>
                </td>
            </tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr>
                <td colspan="1">
                    <input type="submit" value="Schedule"/>
                </form>
            </td>
        </tr>
    </table>
    
    
<?php endif; ?>

</center>