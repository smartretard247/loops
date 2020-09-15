<center>
<?php if($_SESSION['valid_user']) : ?>
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th><?php echo $atName; ?></th>
        </tr>
        
        <tr>
            <td><b>Feature Dates</b></td>
        </tr>
        <?php foreach ($userSchedule as $next) : ?>
            <?php if($next['Seat'] == 'Feature') : ?>
                <tr>
                    <td><?php ++$userFeatureCount; echo $next['EventDate'] . " (" . $next['Number'] . " of " . $next['Total'] . ")"; ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if(!($userFeatureCount > 0)) : ?>
                <tr><td>No Feature reservations</td></tr>
        <?php endif; ?>
        <tr><td></td></tr>
        
        <tr>
            <td><b>VIP Dates</b></td>
        </tr>
        <?php foreach ($userSchedule as $next) : ?>
            <?php if($next['Seat'] == 'VIP') : ?>
                <tr>
                    <td><?php ++$userVIPCount; echo $next['EventDate'] . " (" . $next['Number'] . " of " . $next['Total'] . ")"; ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if(!($userVIPCount > 0)) : ?>
                <tr><td>No VIP reservations</td></tr>
        <?php endif; ?>
        <tr><td></td></tr>
        
        <tr>
            <td><b>Ghost Dates</b></td>
        </tr>
        <?php foreach ($userSchedule as $next) : ?>
            <?php if($next['Seat'] == 'Ghost') : ?>
                <tr>
                    <td><?php ++$userGhostCount; echo $next['EventDate'] . " (" . $next['Number'] . " of " . $next['Total'] . ")"; ?></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if(!($userGhostCount > 0)) : ?>
                <tr><td>No ghost reservations</td></tr>
        <?php endif; ?>
    </table>
<?php else : ?>
    <p class="error">You do not have permission to view this page.
<?php endif; ?>

</center>