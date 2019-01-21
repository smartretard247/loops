<?php $reviews = $db->SafeFetchAll("SELECT ID, ItemID, (SELECT Description FROM items WHERE ItemID = items.`ID`) AS Description, Rating, Review FROM reviews WHERE Approved = 0 ORDER BY $sortby $desc LIMIT " . ($page*$_SESSION['max_per_page']) . ", " . $_SESSION['max_per_page']);
    $totalItems = GetTotalPendingReviews();
    $numPages = $totalItems / $_SESSION['max_per_page'] + 1;
    $columns = 6;
?>

<center>
<?php if($_SESSION['admin_enabled']) : ?>
    <table class="topmargin" style="width: 95%;">
        <tr>
            <th colspan="<?php echo $columns; ?>">Pending Reviews (<?php echo $totalItems; ?> Pending Approval)</th>
        </tr>
        <tr>
            <th><a id="sortby" href="index.php?action=view_pending_reviews&s=ItemID">&#x25B2;</a>Item ID<a id="sortby" href="index.php?action=view_pending_reviews&s=ItemID&d=1">&#x25BC;</a></th>
            <th><a id="sortby" href="index.php?action=view_pending_reviews&s=Description">&#x25B2;</a>Description<a id="sortby" href="index.php?action=view_pending_reviews&s=Description&d=1">&#x25BC;</a></th>
            <th><a id="sortby" href="index.php?action=view_pending_reviews&s=Rating">&#x25B2;</a>Rating<a id="sortby" href="index.php?action=view_pending_reviews&s=Rating&d=1">&#x25BC;</a></th>
            <th><a id="sortby" href="index.php?action=view_pending_reviews&s=Review">&#x25B2;</a>Review<a id="sortby" href="index.php?action=view_pending_reviews&s=Review&d=1">&#x25BC;</a></th>
            <th colspan="2">Approve</th>
        </tr>
        <?php if($reviews) { foreach ($reviews as $treview) : ?>
            <tr>
                <td width="10%">
                    <?php echo $treview['ItemID']; ?>
                </td>
                <td width="10%">
                    <?php echo $treview['Description']; ?>
                </td>
                <td width="10%">
                    <?php ShowStars($treview['Rating']); ?>
                </td>
                <td width="60%">
                    <?php echo $treview['Review']; ?>
                </td>
                <td width="5%">
                    <form action="core/review_verify.php" method="post">
                        <input name="Approved" type="hidden" value="1"/>
                        <input name="ID" value="<?php echo $treview['ID']; ?>" type="hidden"/>
                        <input name="ItemID" value="<?php echo $treview['ItemID']; ?>" type="hidden"/>
                        <input name="Rating" value="<?php echo $treview['Rating']; ?>" type="hidden"/>
                        <input name="Review" value="<?php echo $treview['Review']; ?>" type="hidden"/>
                        <input type="submit" value="Yes"/>
                    </form>
                </td>
                <td width="5%">
                    <form action="core/review_verify.php" method="post">
                        <input name="Approved" type="hidden" value="3"/>
                        <input name="ID" value="<?php echo $treview['ID']; ?>" type="hidden"/>
                        <input name="ItemID" value="<?php echo $treview['ItemID']; ?>" type="hidden"/>
                        <input name="Rating" value="<?php echo $treview['Rating']; ?>" type="hidden"/>
                        <input name="Review" value="<?php echo $treview['Review']; ?>" type="hidden"/>
                        <input type="submit" value="No"/>
                    </form>
                </td>
            </tr>
        <?php endforeach; } ?>
        <?php NoDataRow($treview, $columns) ?>
    </table>
    <?php InsertPageNavigation($page, $numPages, 0, true); ?>
<?php else : ?>
    <p class="error">You do not have permission to view this page.</p><a href="../index.php">Go Back</a>
<?php endif; ?>
</center>