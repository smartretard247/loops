<?php $promos = $db->SafeFetchAll("SELECT ID, PromoCode, PercentOff, Expires FROM promos ORDER BY $sortby $desc LIMIT " . ($page*$_SESSION['max_per_page']) . ", " . $_SESSION['max_per_page']);
    $totalpromos = GetTotalPendingReviews();
    $numPages = $totalpromos / $_SESSION['max_per_page'] + 1;
    $columns = 4;
?>

<center>
<?php if($_SESSION['admin_enabled']) : ?>
    <table class="topmargin" style="width: 75%;">
        <tr>
            <th colspan="<?php echo $columns; ?>">Promotion Codes (<?php echo $totalpromos; ?> Total)</th>
        </tr>
        <tr>
            <th><a id="sortby" href="index.php?action=view_promo&s=PromoCode">&#x25B2;</a>Promotion Code<a id="sortby" href="index.php?action=view_promo&s=PromoCode&d=1">&#x25BC;</a></th>
            <th><a id="sortby" href="index.php?action=view_promo&s=PercentOff">&#x25B2;</a>Percent Off<a id="sortby" href="index.php?action=view_promo&s=PercentOff&d=1">&#x25BC;</a></th>
            <th><a id="sortby" href="index.php?action=view_promo&s=Expires">&#x25B2;</a>Expires<a id="sortby" href="index.php?action=view_promo&s=Expires&d=1">&#x25BC;</a></th>
            <th colspan="2">Delete</th>
        </tr>
        <?php if($promos) { foreach ($promos as $tpromo) : ?>
            <tr>
                <td>
                    <?php echo $tpromo['PromoCode']; ?>
                </td>
                <td>
                    <?php echo ($tpromo['PercentOff']*100) . ' %'; ?>
                </td>
                <td>
                    <?php echo $tpromo['Expires']; ?>
                </td>
                <td>
                    <form action="index.php?action=delpromo" method="post">
                        <input name="ID" value="<?php echo $tpromo['ID']; ?>" type="hidden"/>
                        <input type="submit" value="X"/>
                    </form>
                </td>
            </tr>
        <?php endforeach; } ?>
        <?php NoDataRow($tpromo, $columns) ?>
        <tr>
            <form action="index.php?action=addpromo&s=PromoCode" method="post">
                <td style="border-top: 1px solid blue;">New Code: <input type="text" name="PromoCode" size="11" maxlength="10"/></td>
                <td style="border-top: 1px solid blue;">
                    Percent Off:
                    <select name="PercentOff">
                        <?php for($i = 0; $i <= 15; $i++) : ?>
                            <option value="<?php echo ($i * 0.05); ?>"><?php echo ($i * 0.05 * 100); ?> %</option>
                        <?php endfor; ?>
                    </select>
                </td>
                <td style="border-top: 1px solid blue;">Expires On: <input size="10" id="popupDatepicker" name="Expires" type="text" value=""/></td>
                <td style="border-top: 1px solid blue;"><input type="submit" value="Add"/></td>
            </form>
        </tr>
    </table>
    <?php InsertPageNavigation($page, $numPages, 0, true); ?>
<?php else : ?>
    <p class="error">You do not have permission to view this page.</p><a href="../index.php">Go Back</a>
<?php endif; ?>
</center>