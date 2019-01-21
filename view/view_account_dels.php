<?php $delRequests = $db->SafeFetchAll("SELECT * FROM deletionRequests WHERE ID > 0 ORDER BY $sortby $desc LIMIT " . ($page*$_SESSION['max_per_page']) . ', ' . $_SESSION['max_per_page']);
    $totalItems = GetTotalAccountDeletions();
    $numPages = $totalItems / $_SESSION['max_per_page'] + 1;
    $columns = 4;
?>

<center>
<?php if($_SESSION['admin_enabled']) : ?>
    <table class="topmargin" style="width: 95%;">
        <tr>
            <th colspan="<?php echo $columns; ?>">Account Deletion Requests (<?php echo $totalItems; ?> Pending Deletion)</th>
        </tr>
        <tr>
            <th width="20%"><a id="sortby" href="index.php?action=view_account_dels&s=AccountID">&#x25B2;</a>Account ID<a id="sortby" href="index.php?action=view_account_dels&s=AccountID&d=1">&#x25BC;</a></th>
            <th width="70%"><a id="sortby" href="index.php?action=view_account_dels&s=Reason">&#x25B2;</a>Reason<a id="sortby" href="index.php?action=view_account_dels&s=Reason&d=1">&#x25BC;</a></th>
            <th width="10%" colspan="2">Delete</th>
        </tr>
        <?php if($delRequests) { foreach ($delRequests as $tdelRequest) : ?>
            <tr>
                <td>
                    <?php echo $tdelRequest['AccountID']; ?>
                </td>
                <td>
                    <?php echo $tdelRequest['Reason']; ?>
                </td>
                <td>
                    <form action="core/account_delete_confirm.php" method="post">
                        <input name="action" type="submit" value="Delete"/>
                        <input name="AccountID" value="<?php echo $tdelRequest['AccountID']; ?>" type="hidden"/>
                    </form>
                </td>
                <td>
                    <form action="core/account_delete_confirm.php" method="post">
                        <input name="action" type="submit" value="Cancel"/>
                        <input name="AccountID" value="<?php echo $tdelRequest['AccountID']; ?>" type="hidden"/>
                    </form>
                </td>
            </tr>
        <?php endforeach; } ?>
        <?php NoDataRow($tdelRequest, $columns) ?>
    </table>
    <?php InsertPageNavigation($page, $numPages, 0, true); ?>
<?php else : ?>
    <p class="error">You do not have permission to view this page.</p><a href="../index.php">Go Back</a>
<?php endif; ?>
</center>