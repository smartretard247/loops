<?php if($myOrders && !$_SESSION['admin_enabled']) {
        $orders = $db->SafeFetchAll("SELECT * FROM orders WHERE Status > -2 AND AccountID = :0 ORDER BY $sortby $desc LIMIT " . ($page*$_SESSION['max_per_page']) . ", " . $_SESSION['max_per_page'],array($_SESSION['valid_user']));
        $totalOrders = GetTotalOrders('userorderpage');
    } else {
        if($_SESSION['admin_enabled']) {
            //remove completed orders
            $oldorders = $db->SafeFetchAll("SELECT ID FROM orders WHERE `Status` = 3 AND (SELECT DATEDIFF((SELECT CURDATE() AS TodaysDate),OrderDateTime) > 14)");
            if($oldorders) {
                foreach($oldorders as $updateorder) {
                    $db->SafeExec("UPDATE orders SET `Status` = -1 WHERE ID = :0",array($updateorder['ID']));
                }
            }
            
            $view_complete = filter_input(INPUT_GET, "vc");
            if($view_complete) { $view_complete = "="; } else { $view_complete = ">"; } //=shows just complete orders, > shows all except complete
            
            $orders = $db->SafeFetchAll("SELECT * FROM orders WHERE Status "  . $view_complete .  " -1 ORDER BY $sortby $desc LIMIT " . ($page*$_SESSION['max_per_page']) . ", " . $_SESSION['max_per_page']);
            $totalOrders = GetTotalOrders('adminorderpage');
        } else {
            $orders = null;
            $totalOrders = 0;
        }
    }
    
    $numPages = $totalOrders / $_SESSION['max_per_page'] + 1;
    if($myOrders) { $columns = 6; } else { $columns = 7; }
?>

<center>
<?php if($_SESSION['valid_user']) : ?>
    <table id="orders" class="topmargin" style="width: 95%; border-bottom: solid 1px black">
        <tr>
            <?php if($myOrders) : ?>
                <th colspan="<?php echo $columns; ?>">My Orders (<?php echo $totalOrders; ?> Total)</th>
            <?php elseif($view_complete == "=") : ?>
                <th colspan="<?php echo $columns; ?>">Completed Orders (<?php echo $totalOrders; ?> Orders)</th>
            <?php else : ?>
                <th colspan="<?php echo $columns; ?>">Current Orders (<?php echo $totalOrders; ?> Orders)</th>
            <?php endif; ?>
        </tr>
        <tr>
            <th><a href="index.php?action=view_orders&s=ID">&#x25B2;</a>Order ID<a href="index.php?action=view_orders&s=ID&d=1">&#x25BC;</a></th>
            <?php if(!$myOrders) : ?>
            <th><a href="index.php?action=view_orders&s=AccountID">&#x25B2;</a>Account<a href="index.php?action=view_orders&s=AccountID&d=1">&#x25BC;</a></th>
            <?php endif; ?>
            <!--<th><a href="index.php?action=view_orders&s=BillingAddressID">&#x25B2;</a>Billing Address<a href="index.php?action=view_orders&s=BillingAddressID&d=1">&#x25BC;</a></th>-->
            <th><a href="index.php?action=view_orders&s=ShippingAddressID">&#x25B2;</a>Shipping Address<a href="index.php?action=view_orders&s=ShippingAddressID&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_orders&s=OrderDateTime">&#x25B2;</a>Date Ordered<a href="index.php?action=view_orders&s=OrderDateTime&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_orders&s=OrderTotal">&#x25B2;</a>Order Total<a href="index.php?action=view_orders&s=OrderTotal&d=1">&#x25BC;</a></th>
            <th><a href="index.php?action=view_orders&s=Status">&#x25B2;</a>Status<a href="index.php?action=view_orders&s=Status&d=1">&#x25BC;</a></th>
            <th>View</th>
        </tr>
        <?php if($orders) { foreach ($orders as $torders) : ?>
            <tr>
                <td>
                    <?php echo $torders['ID']; ?>
                </td>
                <?php if(!$myOrders) : ?>
                <td>
                    <?php echo $torders['AccountID']; ?>
                </td>
                <?php endif; ?>
                <?php /*<td>
                    <?php $billingAddress = $db->SafeFetch("SELECT Line1, Line2, City, State, Zip, ZipPlusFour"
                            . " FROM addresses WHERE ID = :0",array($torders['BillingAddressID']));
                    
                        if($billingAddress) :
                            $formatted = $billingAddress['Line1'] . "<br/>";
                            if($billingAddress['Line2'] != "") { $formatted .= $billingAddress['Line2'] . "<br/>"; }
                            $formatted .= $billingAddress['City'] . ", ";
                            $formatted .= $billingAddress['State'] . " ";
                            $formatted .= $billingAddress['Zip'];
                            if($billingAddress['ZipPlusFour'] != "") { $formatted .= "-" . $billingAddress['ZipPlusFour']; }
                            echo $formatted; 
                        endif; ?>
                </td>
                 */ ?>
                <td>
                    <?php $shippingAddress = $db->SafeFetch("SELECT ShipToName, Line1, Line2, City, State, Zip, ZipPlusFour"
                            . " FROM addresses WHERE ID = :0",array($torders['ShippingAddressID']));
                    
                        if($shippingAddress) :
                            $formatted = $shippingAddress['ShipToName'] . "<br/>";
                            $formatted .= $shippingAddress['Line1'] . "<br/>";
                            if($shippingAddress['Line2'] != "") { $formatted .= $shippingAddress['Line2'] . "<br/>"; }
                            $formatted .= $shippingAddress['City'] . ", ";
                            $formatted .= $shippingAddress['State'] . " ";
                            $formatted .= $shippingAddress['Zip'];
                            if($shippingAddress['ZipPlusFour'] != "") { $formatted .= "-" . $shippingAddress['ZipPlusFour']; }
                            echo $formatted;
                        endif; ?>
                </td>
                <td>
                    <?php echo $torders['OrderDateTime']; ?>
                </td>
                <td>
                    <?php echo '$ ' . number_format($torders['OrderTotal'],2); ?>
                </td>
                <td>
                    <?php echo GetStatusNoun($torders['Status']); ?>
                </td>
                <td>
                    <form method="post">
                        <input name="action" type="hidden" value="view_order_cart"/>
                        <?php if($torders['Status'] || !$_SESSION['admin_enabled']) : ?>
                            <input type="submit" value="View"/>
                        <?php else : ?>
                            <input type="submit" value="View" disabled="disabled"/>
                        <?php endif; ?>
                        <input name="AccountID" value="<?php echo $torders['AccountID']; ?>" type="hidden"/>
                        <input name="CartID" value="<?php echo $torders['CartID']; ?>" type="hidden"/>
                        <input name="OrderID" value="<?php echo $torders['ID']; ?>" type="hidden"/>
                        <input name="Status" value="<?php echo $torders['Status']; ?>" type="hidden"/>
                    </form>
                </td>
            </tr>
        <?php endforeach; } ?>
        <?php NoDataRow($torders, $columns, 'No orders to display.') ?>
    </table>
    
    <?php if($myOrders) : ?>
        <?php InsertPageNavigation($page, $numPages, 0, false, 'view_my_orders'); ?>
    <?php else : ?>
        <?php InsertPageNavigation($page, $numPages, 0, true, 'view_orders'); ?>
    <?php endif; ?>
<?php else : ?>
    <p class="error">You do not have permission to view this page.</p><a href="index.php">Go Back</a>
<?php endif; ?>
</center>