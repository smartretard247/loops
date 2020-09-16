<center>
<?php if($_SESSION['valid_user']) : ?>
    
    <table class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <th><?php echo $atName; ?></th>
        </tr>
        
        <?php foreach($availablePackages as $package) : ?>
            <tr>
                <td><b><?php echo $package; ?> Dates</b></td>
            </tr>
            <?php $count = 0; foreach ($userSchedule as $next) : ?>
                <?php if($next['Seat'] == $package) : ?>
                    <tr>
                        <td><?php ++$count; echo $next['EventDate'] . " (" . $next['Number'] . " of " . $next['Total'] . ")"; ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if(!($count > 0)) : ?>
                    <tr><td>No <?php echo $package; ?> reservations</td></tr>
            <?php endif; ?>
            <tr><td></td></tr>
        <?php endforeach; ?>
        
    </table>
<?php else : ?>
    <p class="error">You do not have permission to view this page.
<?php endif; ?>

</center>