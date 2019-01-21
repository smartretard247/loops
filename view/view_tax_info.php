<?php $purchasehistory = $db->SafeFetchAll("SELECT ItemID, PurchasePrice, Quantity, PurchaseMonth FROM purchasehistory WHERE PurchaseYear = :0 AND Quantity > 0 ORDER BY ID",array($taxyear));

    $orderhistory = $db->SafeFetchAll("SELECT ItemsSold, Discount, Promo, Shipping, OrderTotal, Profit, OrderMonth FROM orderhistory WHERE Valid = 1 AND OrderYear = :0 ORDER BY $sortby $desc",array($taxyear));
    $totalItems = GetTotalOrders('fortaxinfo', $taxyear);
    $totalPurchases = GetTotalOrders('purchases', $taxyear);
    $numPages = $totalItems / $_SESSION['max_per_page'] + 1;
    $columns = array(5,13,4);
    
    $itemssold = 0;
    $discount = 0;
    $shipping = 0;
    $ordertotal = 0;
    $profit = 0;
    $purchases = 0;
    
    $purchaseByMonth = array(
        1 => 0.0,
        2 => 0.0,
        3 => 0.0,
        4 => 0.0,
        5 => 0.0,
        6 => 0.0,
        7 => 0.0,
        8 => 0.0,
        9 => 0.0,
        10 => 0.0,
        11 => 0.0,
        12 => 0.0
    );
    $ordersByMonth = array(
        1 => 0.0,
        2 => 0.0,
        3 => 0.0,
        4 => 0.0,
        5 => 0.0,
        6 => 0.0,
        7 => 0.0,
        8 => 0.0,
        9 => 0.0,
        10 => 0.0,
        11 => 0.0,
        12 => 0.0
    );
?>

<center>
<?php if($_SESSION['admin_enabled']) : ?>
    <br/><table style="width: 95%;">
        <tr>
            <td>
                <p style="font-weight: bold;">For Tax Year: 
                    <select>
                        <?php for($i = $thisyear; $i > ($thisyear-7); $i--) : ?>
                            <?php if($taxyear == $i) : ?>
                                <option selected="selected" onclick="window.location='index.php?action=view_tax_info&taxyear=<?php echo $i; ?>';"><?php echo $i; ?></option>
                            <?php else : ?>
                                <option onclick="window.location='index.php?action=view_tax_info&taxyear=<?php echo $i; ?>';"><?php echo $i; ?></option>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </select>
                </p>
            </td>
        </tr>
    </table>
    <table class="topmargin" style="width: 35%;">
        <tr>
            <th colspan="<?php echo $columns[2]; ?>">Purchase History (<?php echo $totalPurchases; ?> Total)</th>
        </tr>
        <tr>
            <th width="20%">Item ID</th>
            <th>Purchase Price</th>
            <th>Qty</th>
            <th>Total</th>
        </tr>
        <?php if($purchasehistory) { foreach ($purchasehistory as $tpurchasehistory) : ?>
            <?php if($tpurchasehistory['Quantity']) : ?>
                <tr>
                    <td>
                        <?php echo $tpurchasehistory['ItemID']; ?>
                    </td>
                    <td>
                        <?php echo "$ " . number_format($tpurchasehistory['PurchasePrice'],2); ?>
                    </td>
                    <td>
                        <?php echo $tpurchasehistory['Quantity']; ?>
                    </td>
                    <td>
                        <?php $subpurchase = ($tpurchasehistory['PurchasePrice'] * $tpurchasehistory['Quantity']);
                            $purchaseByMonth[$tpurchasehistory['PurchaseMonth']] += $subpurchase;
                            $purchases += $subpurchase; echo "$ " . number_format($subpurchase,2); ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; } ?>
        <?php NoDataRow($tpurchasehistory, $columns[2], "No purchases from the selected year.") ?>
        <tr>
            <th colspan="<?php echo $columns[2]; ?>" style="border-top: 1px dotted silver;"><b>Total Spent In Purchases:</b> <font style="color: black;">$ <?php echo number_format($purchases,2); ?></font></th>
        </tr>
    </table>
    <table class="topmargin" style="width: 95%;">
        <tr>
            <th colspan="<?php echo $columns[0]; ?>">Order History (<?php echo $totalItems; ?> Total)
            </th>
        </tr>
        <tr>
            <th width="10%"><a id="sortby" href="index.php?action=view_tax_info&s=ItemsSold">&#x25B2;</a>Items Sold<a id="sortby" href="index.php?action=view_tax_info&s=ItemsSold&d=1">&#x25BC;</a></th>
            <th><a id="sortby" href="index.php?action=view_tax_info&s=Shipping">&#x25B2;</a>Shipping<a id="sortby" href="index.php?action=view_tax_info&s=Shipping&d=1">&#x25BC;</a></th>
            <th><a id="sortby" href="index.php?action=view_tax_info&s=Discount">&#x25B2;</a>Discount/Promo<a id="sortby" href="index.php?action=view_tax_info&s=Discount&d=1">&#x25BC;</a></th>
            <th><a id="sortby" href="index.php?action=view_tax_info&s=OrderTotal">&#x25B2;</a>Order Total<a id="sortby" href="index.php?action=view_tax_info&s=OrderTotal&d=1">&#x25BC;</a></th>
            <th><a id="sortby" href="index.php?action=view_tax_info&s=Profit">&#x25B2;</a>Profit<a id="sortby" href="index.php?action=view_tax_info&s=Profit&d=1">&#x25BC;</a></th>
        </tr>
        <?php if($orderhistory) { foreach ($orderhistory as $torderhistory) : ?>
            <tr>
                <td>
                    <?php $itemssold += $torderhistory['ItemsSold']; echo $torderhistory['ItemsSold']; ?>
                </td>
                <td>
                    <?php $shipping += $torderhistory['Shipping']; echo "$ " . number_format($torderhistory['Shipping'],2); ?>
                </td>
                <td>
                    <?php $discount += $torderhistory['Discount'];
                    if($torderhistory['Discount']) {
                        echo "$ " . number_format($torderhistory['Discount'],2) . " / " . $torderhistory['Promo'];
                    } else {
                        echo "$ " . number_format($torderhistory['Discount'],2);
                    } ?>
                </td>
                <td>
                    <?php $ordertotal += $torderhistory['OrderTotal']; echo "$ " . number_format($torderhistory['OrderTotal'],2); ?>
                </td>
                <td>
                    <?php
                        $ordersByMonth[$torderhistory['OrderMonth']] += $torderhistory['Profit'];
                        $profit += $torderhistory['Profit']; echo "$ " . number_format($torderhistory['Profit'],2);
                    ?>
                </td>
            </tr>
        <?php endforeach; } ?>
        <?php NoDataRow($torderhistory, $columns[0], "No orders from the selected year.") ?>
        <tr>
            <td style="border-top: 1px dotted silver;"><b>Total Items Sold:</b> <?php echo $itemssold; ?></td>
            <td style="border-top: 1px dotted silver;"><b>Total Shipping:</b> $ <?php echo number_format($shipping,2); ?></td>
            <td style="border-top: 1px dotted silver;"><b>Total In Discounts:</b> $ <?php echo number_format($discount,2); ?></td>
            <td style="border-top: 1px dotted silver;"><b>Gross Income:</b> $ <?php echo number_format($ordertotal,2); ?></td>
            <td style="border-top: 1px dotted silver;"><b>Net Profit:</b> $ <?php echo number_format($profit,2); ?></td>
        </tr>
        <tr>
            <th style="border-top: 1px dotted silver;" colspan="<?php echo $columns[0]; ?>"><b>Taxes Owed To Social Security:</b> <font style="color: black;">$ <?php echo number_format($profit*0.124,2); ?></font></th>
        </tr>
    </table>
    <table class="topmargin" style="width: 95%;">
        <tr>
            <th colspan="<?php echo $columns[1]; ?>">Monthly Breakdown</th>
        </tr>
        <tr>
            <th></th>
            <th>January</th><th>February</th><th>March</th><th>April</th><th>May</th><th>June</th>
            <th>July</th><th>August</th><th>September</th><th>October</th><th>November</th><th>December</th>
        </tr>
        <tr>
            <th>Spent:</th>
            <?php for($i = 1; $i <= 12; $i++) : ?>
                <td><?php echo "$ " . number_format($purchaseByMonth[$i],2); ?></td>
            <?php endfor; ?>
        </tr>
        <tr>
            <th>Profit:</th>
            <?php for($i = 1; $i <= 12; $i++) : ?>
                <td><?php echo "$ " . number_format($ordersByMonth[$i],2); ?></td>
            <?php endfor; ?>
        </tr>
        <?php NoDataRow($tpurchasehistory, $columns[1], "No purchases from the selected year.") ?>
    </table>
<?php else : ?>
    <p class="error">You do not have permission to view this page.</p><a href="../index.php">Go Back</a>
<?php endif; ?>
</center>