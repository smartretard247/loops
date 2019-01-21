<?php include_once $_SESSION['rootDir'] . 'core/paypal.php';
    if($_SESSION['valid_user']) {
        $values = array($_SESSION['valid_user']);
        $addresses = $db->SafeFetchAll("SELECT ID, Line1, IsBilling FROM addresses WHERE Historical = 0 AND AccountID = :0",$values);
        $cart = $db->SafeFetchAll("SELECT ItemID, Quantity FROM carts WHERE CartID = 0 AND AccountID = :0",$values);
    } else {
        //no user logged in
    }
    
    if($discount) {
        $discount = number_format(($subtotal-($subtotal*(1-$discount))),2);
        $ordertotal = number_format(($subtotal+$shipping-$discount),2);
    } else {
        $promo = null;
    }
?>

<center>
    <form name="checkout" method="post" action="core/order_submit.php">
        <table class="topmargin">
            <th>
                Order Summary
            </th>
            <tr>
                <input type="hidden" name="ItemsSold" value="<?php echo $itemssold; ?>"/>
                <input type="hidden" name="Shipping" value="<?php echo $shipping; ?>"/>
                <input type="hidden" name="Subtotal" value="<?php echo $subtotal; ?>"/>
                <input type="hidden" name="Discount" value="<?php echo $discount; ?>"/>
                <input type="hidden" name="PromoCode" value="<?php echo $promo; ?>"/>
                <input type="hidden" name="TotalPurchasePrice" value="<?php echo $totalpurchaseprice; ?>"/>
                <input type="hidden" name="OrderTotal" value="<?php echo $ordertotal; ?>"/>
                            
                <td><b>Total Items:</b> <?php echo $itemssold; ?><br/>
                    <b>Cost of Items:</b> $<?php echo number_format($subtotal,2); ?><br/>
                    <b>Shipping:</b> $<?php echo number_format($shipping,2); ?><br/>
                    <?php if($discount) : ?>
                        <b style="color: green;">Discount: $<?php echo $discount; ?></b><br/>
                    <?php else : ?>
                        <b>Discount:</b> $0.00<br/>
                    <?php endif; ?>
                    <b>Tax:</b> $<?php echo number_format($subtotal*Paypal::$tax,2); ?><br/>
                    <b>Total:</b> $<?php echo number_format($ordertotal+($subtotal*Paypal::$tax),2); ?><br/>
                </td>
            </tr>
            <?php /* 
            <tr>
                <td>
                    Billing Address:
                    <select name="BillingID">
                        <?php foreach ($addresses as $address) {
                            if($address['IsBilling'] == 1) : ?>
                                <option value="<?php echo $address['ID']; ?>"><?php echo $address['Line1']; ?></option>
                        <?php break; endif; } ?>
                        <?php foreach ($addresses as $address) {
                            if($address['IsBilling'] == 0) : ?>
                                <option value="<?php echo $address['ID']; ?>"><?php echo $address['Line1']; ?></option>
                        <?php endif; } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    Shipping Address:
                    <select name="ShipID">
                        <?php foreach ($addresses as $address) {
                            if($address['IsBilling'] == 1) : ?>
                                <option value="<?php echo $address['ID']; ?>"><?php echo $address['Line1']; ?></option>
                        <?php break; endif; } ?>
                        <?php foreach ($addresses as $address) {
                            if($address['IsBilling'] == 0) : ?>
                                <option value="<?php echo $address['ID']; ?>"><?php echo $address['Line1']; ?></option>
                        <?php endif; } ?>
                    </select>
                </td>
            </tr> */ ?>
            <tr>
                <td>
                    <input name="CartID" type="hidden" value="0"/>
                    <!--<input type="submit" value="Pay with Paypal"/>-->
                    <img src="Images/paypal-button.png" onclick="checkout.submit();" style="cursor: pointer;"/>
                </td>
            </tr>
        </table>
    </form>
    <form method="post">
        <table class="topmargin">
            <th>
                Promotion Code
            </th>
            <tr>
                <td>
                    <input name="PCode" type="text" maxlength="10"/><br/>
                    
                    <input type="hidden" name="action" value="view_checkout"/>
                    <input type="hidden" name="ItemsSold" value="<?php echo $itemssold; ?>"/>
                    <input type="hidden" name="Shipping" value="<?php echo $shipping; ?>"/>
                    <input type="hidden" name="Subtotal" value="<?php echo $subtotal; ?>"/>
                    <input type="hidden" name="OrderTotal" value="<?php echo $shipping+$subtotal; ?>"/>
                    <input type="hidden" name="TotalPurchasePrice" value="<?php echo $totalpurchaseprice; ?>"/>
                    <input type="submit" value="Apply Promotion"/>
                </td>
            </tr>
        </table>
    </form>
</center>