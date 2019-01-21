<?php $discount = 0;
    if($_SESSION['valid_user']) {
        if($orderView) {
            $cartOwner = filter_input(INPUT_POST, 'AccountID');
            $cartID = filter_input(INPUT_POST, 'CartID');
            $orderID = filter_input(INPUT_POST, 'OrderID');
            $status = filter_input(INPUT_POST, 'Status');
            
            $discountrow = $db->SafeFetch("SELECT Discount FROM orders WHERE ID = :0",array($orderID));
            if($discountrow) {
                $discount = $discountrow['Discount'];
            } else {
                $discount = 0;
            }
            
            if($_SESSION['admin_enabled'] && $status == 1) { //if order is paid for and an administrator views the order...
                //change status for order to 2 (processing)
                $db->SafeExec("UPDATE orders SET Status = 2 WHERE ID = :0",array($orderID));
            }
        } else { $cartOwner = $_SESSION['valid_user']; $cartID = 0; }
    
        $values = array($cartOwner,$cartID);
        $query = "SELECT ID, AccountID, ItemID, Quantity, "
                . "(SELECT QOH FROM items WHERE items.`ID` = carts.`ItemID`)-"
                . "(SELECT QOO FROM items WHERE items.`ID` = carts.`ItemID`) AS QFS, CartID "
                . "FROM carts WHERE AccountID = :0 AND CartID = :1 ORDER BY $sortby $desc";
        
        $items_in_cart = $db->SafeFetchAll($query,$values);
        $deletedFromCart = 0;
        
        if(!$orderView) {
            foreach($items_in_cart as $temp) {
                if($temp['QFS'] < 1) {
                    $deletedFromCart += $db->SafeExec("DELETE FROM carts WHERE ID = :0",array($temp['ID']));
                }
            }
            if($deletedFromCart) {
                $items_in_cart = $db->SafeFetchAll($query,$values);
                $_SESSION['alert'] = 'One or more of the items in your cart are now sold out\\nand have been removed, sorry for the inconvenience.';
            }
        }
        
        $totalItems = $db->SafeFetch("SELECT COUNT(ID) AS Total FROM carts WHERE AccountID = :0 AND CartID = :1",array($cartOwner,$cartID));
    } else {
        $totalItems['Total'] = 0;
        
        $items_in_cart = array();
        
        $item_to_add = array(
            'ItemID' => 0,
            'Quantity' => 0
        );
        
        for($i = 0; $i < count($_SESSION['cart']['ItemID']); $i++) {
            $item_to_add['ItemID'] = $_SESSION['cart']['ItemID'][$i];
            $item_to_add['Quantity'] = $_SESSION['cart']['Quantity'][$i];
            array_push($items_in_cart, $item_to_add);
            
            ++$totalItems['Total'];
        }
    }
    
    $columns = 5;
    $shippingcost = 0;
    $subtotal = 0.0;
    $itemssold = 0;
    
    $totalpurchaseprice = 0;
?>

<center>
    <table class="topmargin" id="cart" style="width: 60%;">
        <tr>
            <?php if(!$orderView) : ?>
                <th colspan="<?php echo $columns; ?>">My Shopping Cart (<?php echo $totalItems['Total']; ?> Items)</th>
            <?php else : ?>
                <th colspan="<?php echo $columns; ?>">Order #<?php echo $orderID; ?> (<?php echo $totalItems['Total']; ?> Items)</th>
            <?php endif; ?>
        </tr>
        <tr>
            <th width="15%">Thumb</th>
            <th width="65%">Description</th>
            <th width="10%">Quantity</th>
            <th width="10%">Each</th>
            <th width="10%">Cost</th>
        </tr>
        
        <?php if($totalItems['Total']) : ?>
            <form name="cart" action="core/update_cart.php" method="post">
                
            <?php foreach ($items_in_cart as $tcart_item) :
                    $item_info = $db->SafeFetch("SELECT * FROM items WHERE ID = :0", array($tcart_item['ItemID']));
                    if($item_info['Shipping'] > $shippingcost) { $shippingcost = $item_info['Shipping']; }
            ?>
        
                
            <tr>
                <td>
                    <a href="index.php?action=view_item&id=<?php echo $tcart_item['ItemID']; ?>">
                        <img height="35" width="35" src="<?php echo 'Images/inv/thumbs/' . GetThumbnailFilename($item_info['ImgFile']); ?>"/>
                    </a>
                </td>
                <td>
                    <?php echo $item_info['Description']; ?>
                </td>
                <td>
                    <input  type="hidden" name="ID[]" value="<?php echo $tcart_item['ID']; ?>"/>
                    <input  type="hidden" name="ItemID[]" value="<?php echo $tcart_item['ItemID']; ?>"/>
                    <?php if(!$orderView) : ?>
                        <select name="Quantity[]">
                            <?php $itemssold += $tcart_item['Quantity']; //tally items
                            if($_SESSION['valid_user']) :
                                for($i = 0; $i <= $tcart_item['QFS']; $i++) : ?>
                                    <?php if($tcart_item['Quantity'] == $i) : ?>
                                        <option value="<?php echo $i; ?>" selected="selected"><?php echo $i; ?></option>
                                    <?php else : ?>
                                        <option onclick="document.cart.submit();" value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            <?php else : ?>
                                <option value="1" selected="selected">1</option>
                            <?php endif; ?>
                        </select>
                    <?php else : ?>
                        <input style="text-align: center;" type="text" size="2" name="Quantity[]" value="<?php echo $tcart_item['Quantity']; ?>" disabled="disabled"/>
                    <?php endif; ?>
                </td>
                <td>
                    <?php $subtotal += $item_info['Price'] * $tcart_item['Quantity']; ?>
                    <?php echo '$' . number_format($item_info['Price'],2); ?>
                </td>
                <td>
                    <?php echo '$' . number_format(($item_info['Price'] * $tcart_item['Quantity']),2);
                        $totalpurchaseprice += ($item_info['PurchasePrice'] * $tcart_item['Quantity']);
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
            <tr>
                <td colspan="2" style="text-align: left; border-bottom: 1px dotted silver; border-top: 1px dotted silver;">
                    <?php if($orderView) :
                        $giftMessage = $db->SafeFetch("SELECT GiftMessage, GiftReceipt FROM orders WHERE ID = :0",array($orderID));
                        if($giftMessage['GiftMessage'] != '') {
                            echo '<b>Gift Message:</b> ' . $giftMessage['GiftMessage'];
                        } else {
                            echo '<b>Gift Message:</b> None.';
                        }
                        
                        $giftReceipt = ($giftMessage['GiftReceipt'] ? 'Yes' : 'No');
                        echo '<br/><br/><b>Gift Receipt:</b> ' . $giftReceipt;
                    endif; ?>
                </td>
                <td colspan="<?php echo ($columns-2); ?>" style="text-align: right; border-bottom: 1px dotted silver; border-top: 1px dotted silver;">
                    <?php echo '<b>Subtotal:</b> $' . number_format($subtotal,2); ?>
                    <?php echo '<br/><b>S&H:</b> $' . number_format($shippingcost,2); ?>
                    <?php if($discount) : ?>
                        <?php echo '<br/><b style="color: green;">Discount:</b> $' . number_format($discount,2); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td colspan="<?php echo $columns; ?>" style="text-align: right; border-bottom: 1px dotted silver;">
                    <?php echo '<b>Total:</b> $' . number_format(($subtotal+$shippingcost-$discount),2); ?>
                </td>
            </tr>
            </form>
            <?php if(!$orderView) : ?>
            <tr>
                <td colspan="<?php echo $columns; ?>">
                    <?php if($_SESSION['valid_user']) : ?>
                        <form method="post">
                            <input type="hidden" name="action" value="view_checkout"/>
                            <input type="hidden" name="ItemsSold" value="<?php echo $itemssold; ?>"/>
                            <input type="hidden" name="Shipping" value="<?php echo $shippingcost; ?>"/>
                            <input type="hidden" name="Subtotal" value="<?php echo $subtotal; ?>"/>
                            <input type="hidden" name="OrderTotal" value="<?php echo ($subtotal+$shippingcost); ?>"/>
                            <input type="hidden" name="TotalPurchasePrice" value="<?php echo $totalpurchaseprice; ?>"/>
                            <input type="submit" value="Proceed to Checkout"/>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php else : ?>
                <?php if($_SESSION['admin_enabled'] && $status == 2) : ?>
                    <tr>
                        <td colspan="<?php echo $columns; ?>">
                            <form action="index.php?d=1" method="post">
                                <input type="hidden" name="action" value="order_ship"/>
                                <input type="hidden" name="OrderID" value="<?php echo $orderID; ?>"/>
                                <input type="submit" value="Mark as Shipped"/>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php NoDataRow($tcart_item, $columns, "Your cart is empty.") ?>
    </table>
</center>