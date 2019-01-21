<?php if($_SESSION['debug']) { $minID = 0; } else { $minID = 1000; }
    $items = $db->SafeFetchAll("SELECT * FROM items WHERE ID > :0 AND Hidden = :1 ORDER BY $sortby $desc LIMIT " . ($page*$_SESSION['max_per_page']) . ", " . $_SESSION['max_per_page'],array($minID,$hidden));
    $totalItems = $db->SafeFetch("SELECT COUNT(ID) AS Total FROM items WHERE ID > :0 AND Hidden = :1",array($minID,$hidden));
    $numPages = $totalItems['Total'] / $_SESSION['max_per_page'] + 1;
    $columns = 13;
?>

<center>
<?php if($_SESSION['admin_enabled']) : ?>
    <table class="topmargin" style="width: 98%;">
        <tr>
            <?php if($hidden) : ?>
                <th colspan="<?php echo $columns; ?>">Removed Items (<?php echo $totalItems['Total']; ?> Items)</th>
            <?php else : ?>
                <th colspan="<?php echo $columns; ?>">Current Inventory (<?php echo $totalItems['Total']; ?> Items)</th>
            <?php endif; ?>
        </tr>
        <tr>
            <th><a href="index.php?action=view_inventory&s=ID">&#x25B2;</a>ID<a href="index.php?action=view_inventory&s=ID&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_inventory&s=ImgFile">&#x25B2;</a>Thumb<a href="index.php?action=view_inventory&s=ImgFile&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_inventory&s=Description">&#x25B2;</a>Description<a href="index.php?action=view_inventory&s=Description&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_inventory&s=CategoryID">&#x25B2;</a>Category<a href="index.php?action=view_inventory&s=CategoryID&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_inventory&s=PurchasePrice">&#x25B2;</a>Purchase Price<a href="index.php?action=view_inventory&s=PurchasePrice&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_inventory&s=Price">&#x25B2;</a>Selling Price<a href="index.php?action=view_inventory&s=Price&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_inventory&s=Shipping">&#x25B2;</a>Shipping<a href="index.php?action=view_inventory&s=Shipping&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_inventory&s=PurchaseDate">&#x25B2;</a>Purchase Date<a href="index.php?action=view_inventory&s=PurchaseDate&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_inventory&s=QOO">&#x25B2;</a>QOO<a href="index.php?action=view_inventory&s=QOO&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_inventory&s=QOH">&#x25B2;</a>QOH<a href="index.php?action=view_inventory&s=QOH&d=1">&#x25BC;</a></th>
            <th>Link</th>
            <?php if($hidden) : ?>
                <th>DELETE</th>
                <th>Restore</th>
            <?php else : ?>
                <th>Remove</th>
                <th>Edit</th>
            <?php endif; ?>
        </tr>
        <?php if($items) { foreach ($items as $titem) : ?>
            <tr>
                <td>
                    <?php echo $titem['ID']; ?>
                </td>
                <td>
                    <a href="index.php?action=view_item&id=<?php echo $titem['ID']; ?>">
                        <img height="25" width="25" src="<?php echo 'Images/inv/thumbs/' . GetThumbnailFilename($titem['ImgFile']); ?>" onmouseover="document.<?php echo 'Img' . $titem['ID']; ?>.hidden = false;" onmouseout="document.<?php echo 'Img' . $titem['ID']; ?>.hidden = true;"/>
                    </a>
                </td>
                <td>
                    <?php echo $titem['Description']; ?>
                </td>
                <td>
                    <?php foreach($item_category as $tCategory) {
                        if($tCategory['ID'] == $titem['CategoryID']) {
                            echo $tCategory['Name'];
                            break;
                        }
                    } ?>
                </td>
                <td>
                    <?php echo '$' . number_format($titem['PurchasePrice'],2); ?>
                </td>
                <td>
                    <?php echo '$' . number_format($titem['Price'],2); ?>
                </td>
                <td>
                    <?php echo '$' . number_format($titem['Shipping'],2); ?>
                </td>
                <td>
                    <?php echo $titem['PurchaseDate']; ?>
                </td>
                <td>
                    <?php echo $titem['QOO']; ?>
                </td>
                <td>
                    <?php echo $titem['QOH']; ?>
                </td>
                <td>
                    <?php $link = 'http://www.strawberryfountain.net/SimplySilverAKY/index.php?action=view_item&id=' . $titem['ID']; ?>
                    <textarea wrap="hard" rows="1" cols="6" style="resize: none;"><?php echo $link; ?></textarea>
                </td>
                <td>
                    <form action="index.php?s=<?php echo $sortby; ?>&d=<?php echo $isDescending; ?>&p=<?php echo $page; ?>" method="post">
                        <?php if($hidden) : ?>
                            <input name="action" type="hidden" value="item_delete"/>
                        <?php else : ?>
                            <input name="action" type="hidden" value="item_remove"/>
                        <?php endif; ?>
                        <input type="submit" value="X"/>
                        <input name="<?php echo $titem['ID']; ?>" value="<?php echo $titem['ID']; ?>" type="hidden"/>
                    </form>
                </td>
                <?php if($hidden) : ?>
                    <td>
                        <form action="index.php?s=<?php echo $sortby; ?>&d=<?php echo $isDescending; ?>&p=<?php echo $page; ?>" method="post">
                                <input name="action" type="hidden" value="item_restore"/>
                                <input type="submit" value="Restore"/>
                                <input name="<?php echo $titem['ID']; ?>" value="<?php echo $titem['ID']; ?>" type="hidden"/>
                        </form>
                    </td>
                <?php else : ?>
                    <td>
                        <form action="index.php?s=<?php echo $sortby; ?>&d=<?php echo $isDescending; ?>&p=<?php echo $page; ?>" method="post">
                            <input name="action" type="hidden" value="view_inv_edit"/>
                            <input type="submit" value="Edit"/>
                            <input name="<?php echo $titem['ID']; ?>" value="<?php echo $titem['ID']; ?>" type="hidden"/>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; } ?>
        <?php NoDataRow($titem, $columns) ?>
    </table>
    <table class="topmargin">
        <tr>
            <td>Add an item to inventory:</td>
            <td><form method="get"><input type="hidden" name="action" value="view_item_add"/><input value="Go" type="submit"/></form></td>
        </tr>
        <tr>
            <?php if($hidden) : ?>
                <td>Show current inventory:</td>
                <td><form method="get"><input type="hidden" name="action" value="view_inventory"/><input value="Go" type="submit"/></form></td>
            <?php else : ?>
                <td>View removed items:</td>
                <td><form method="get"><input type="hidden" name="action" value="view_removed"/><input value="Go" type="submit"/></form></td>
            <?php endif; ?>
        </tr>
        <tr>
            <td>View orders:</td>
            <td><form method="get"><input type="hidden" name="action" value="view_orders"/><input value="Go" type="submit"/></form></td>
        </tr>
        <tr>
            <td>View reviews pending approval:</td>
            <td><form method="get"><input type="hidden" name="action" value="view_pending_reviews"/><input value="Go" type="submit"/></form></td>
        </tr>
    </table>
    <?php InsertPageNavigation($page, $numPages, 0, true, ''); ?>
<?php else : ?>
    <p class="error">You do not have permission to view this page.</p><a href="index.php">Go Back</a>
<?php endif; ?>
</center>