<center>
<?php if($_SESSION['valid_user']) : ?>
    <?php 
        $orders = $db->SafeFetchAll("SELECT * FROM schedule WHERE EventDate >= NOW()-INTERVAL 1 DAY AND Deleted = 0 ORDER BY $sortby $desc");
        $nextVIP = $db->SafeFetchAll("SELECT * FROM schedule WHERE EventDate >= NOW()-1 AND (VIP > 0) AND Deleted = 0 ORDER BY EventDate LIMIT 1");
        $nextGhost = $db->SafeFetchAll("SELECT * FROM schedule WHERE EventDate >= NOW()-1 AND (Ghost > 0) AND Deleted = 0 ORDER BY EventDate LIMIT 1");
        
        $weekNo = getWeekNum(); #change to function to get % 52 of the ID, convert to start/end dates
        $currentFeature = $db->SafeFetch("SELECT AtName FROM features WHERE ID = :0",array($weekNo));
        $nextFeature = $db->SafeFetchAll("SELECT * FROM features WHERE ID > $weekNo AND AtName IS NULL ORDER BY ID LIMIT 1");
        if(!$nextFeature) { $nextFeature = $db->SafeFetchAll("SELECT * FROM features WHERE ID > 0 AND AtName IS NULL ORDER BY ID LIMIT 1"); }
        
        $columns = 4;
        
        $pending[0] = $db->SafeFetchAll("SELECT * FROM pending WHERE Seat = 'VIP' ORDER BY ID");
        $pending[1] = $db->SafeFetchAll("SELECT * FROM pending WHERE Seat = 'Ghost' ORDER BY ID");
        #$pending[2] = $db->SafeFetchAll("SELECT * FROM pending WHERE Seat = 'Feature' ORDER BY ID");
        
    ?>
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th>Featured This Week</th>
        </tr>
        <tr>
            <td>
                <?php echo ($currentFeature['AtName']) ? $currentFeature['AtName'] : "No feature this week."; ?>
            </td>
        </tr>
    </table>
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th colspan="5">Next Open VIP Slot</th>
        </tr>
        <tr>
            <td rowspan="4">Date: <?php echo ($nextVIP[0]) ? $nextVIP[0]['EventDate'] : "TBD"; ?></td>
            <td rowspan="4">Seats: <?php echo ($nextVIP[0]) ? $nextVIP[0]['VIP'] : 5; ?></td>
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
            <td rowspan="4">Date: <?php echo ($nextGhost[0]) ? $nextGhost[0]['EventDate'] : "TBD"; ?></td>
            <td rowspan="4">Seats: <?php echo ($nextGhost[0]) ? $nextGhost[0]['Ghost'] : 8; ?></td>
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
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
            <tr>
                <th colspan="5">Next Open Feature Slot</th>
            </tr>
            <tr>
                <?php $week = getStartAndEndDate($nextFeature[0]['ID']); ?>
                <td rowspan="4" colspan="2">Date: <?php echo ($nextFeature[0]) ? $week['week_start'] . " - " . $week['week_end'] : "TBD"; ?></td>
                
                <td>
                    <form action="core/submit_reservation.php" method="post">
                        <input name="action" type="hidden" value="reserve_seat"/>
                        <input name="seat" type="hidden" value="Feature"/>
                        <input name="week" type="hidden" value="<?php echo $nextFeature[0]['ID']; ?>"/>
                        <input name="year" type="hidden" value="<?php echo $week['week_year']; ?>"/>
                        @Name: <input name="atName" type="text" title="Enter @name of customer"/>
                </td>
                <td>
                    Pack: <select name="number" title="Enter package size">
                        <option value="1">1</option>
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
            <th>Date</th>
            <th>VIPs</th>
            <th>Ghosts</th>
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
            <th colspan="3">
                Waitlist
            </th>
        </tr>
        
        <tr>
            <?php $i = 0; foreach($pending as $package) : ?>
                <td>
                    <?php if($package) : ?>
                        <table class="topmargin" style="width: 100%; border-bottom: solid 1px black">
                            <tr>
                                <th colspan="1"><?php echo $availablePackages[$i]; ?>s</th>
                            </tr>
                            <?php foreach ($package as $row) : ?>
                                <tr>
                                    <td>
                                        <?php echo $row['AtName'] . " (" . $row['Number'] . " of " . $row['Total'] . ")"; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else : ?>
                        No pending <?php echo $availablePackages[$i++]; ?>s.
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>
        </tr>
    </table>
<?php else : ?>
    <p>You do not have permission to view this page.</p>
    <?php include 'view/home.php'; ?>
<?php endif;
    
if($_SESSION['admin_enabled']) : ?>
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