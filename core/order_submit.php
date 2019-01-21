<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'paypal.php';
    
    $billingID = filter_input(INPUT_POST, 'BillingID');
    //$shipID = filter_input(INPUT_POST, 'ShipID');
    $cartID = filter_input(INPUT_POST, 'CartID');
    
    $itemssold = filter_input(INPUT_POST, 'ItemsSold');
    $shipping = filter_input(INPUT_POST, 'Shipping');
    $subtotal = filter_input(INPUT_POST, 'Subtotal');
    $discount = number_format(filter_input(INPUT_POST, 'Discount'),2);
    $promo = filter_input(INPUT_POST, 'PromoCode');
    $ordertotal = filter_input(INPUT_POST, 'OrderTotal');
    $totalpurchaseprice = filter_input(INPUT_POST, 'TotalPurchasePrice');
    $orderyear = date("Y");
    
    $profit = ($ordertotal-$shipping-$totalpurchaseprice);
    
    $_SESSION['order_details'] = array('Total' => $ordertotal, 'CostOfItems' => $subtotal-$discount, 'Shipping' => $shipping, 'Currency' => 'USD', 'Discount' => $discount);
    
    
    if($promo != '') {
        $promo = strtoupper($promo);
    } else {
        $promo = null;
    }
    
    if($_SESSION['valid_user']) { //only process order if logged in...
        //need to add to orders table next
        $order = array('orders',
            'AccountID', $_SESSION['valid_user'],
            //'BillingAddressID', $billingID,
            //'ShippingAddressID', $shipID,
            'CartID', $cartID,
            'OrderDateTime', date('Y-m-d H:i:s'),
            'Discount', $discount,
            'OrderTotal', $ordertotal);
        
        if($db->AddToDB($order)) { //add order then process transaction
            $lastID = $db->Query("SELECT MAX(ID) AS LastID FROM orders")->fetch();
            $_SESSION['order_details']['OrderID'] = $lastID['LastID'];

            $values = array($_SESSION['order_details']['OrderID'], $orderyear, date('m'), $itemssold, $shipping, $discount, $promo, $ordertotal, $profit);
            $db->SafeExec("INSERT INTO orderhistory (ID, OrderYear, OrderMonth, ItemsSold, Shipping, Discount, Promo, OrderTotal, Profit) "
                    . "VALUES (:0, :1, :2, :3, :4, :5, :6, :7, :8)",$values);

            $itemsOrdered = $db->SafeFetchAll("SELECT ItemID, Quantity FROM carts WHERE CartID = 0 AND AccountID = :0",array($_SESSION['valid_user']));
            
            if($itemsOrdered) {
                $itemArray = array();
                $itemPosition = 0;
                
                foreach($itemsOrdered as $titemsOrdered) {
                    $itemInfo = $db->Query("SELECT Description, Price AS Amount, "
                            . "(SELECT Singular FROM item_category WHERE ID = "
                            . "(SELECT CategoryID FROM items WHERE ID = " . $titemsOrdered['ItemID'] . ")) AS Singular "
                            . "FROM items WHERE ID = " . $titemsOrdered['ItemID'] . "")->fetch();

                    $itemArray["L_PAYMENTREQUEST_0_NAME$itemPosition"] = $itemInfo['Singular'];
                    $itemArray["L_PAYMENTREQUEST_0_NUMBER$itemPosition"] = $titemsOrdered['ItemID'];
                    $itemArray["L_PAYMENTREQUEST_0_DESC$itemPosition"] = $itemInfo['Description'];
                    $itemArray["L_PAYMENTREQUEST_0_AMT$itemPosition"] = number_format($itemInfo['Amount'],2);
                    $itemArray["L_PAYMENTREQUEST_0_QTY$itemPosition"] = $titemsOrdered['Quantity'];
                    
                    ++$itemPosition;
                }
                
                if($discount>0) {
                    $itemArray["L_PAYMENTREQUEST_0_NAME$itemPosition"] = 'Discount from Promotion';
                    $itemArray["L_PAYMENTREQUEST_0_AMT$itemPosition"] = number_format((-$discount),2);
                    $itemArray["L_PAYMENTREQUEST_0_QTY$itemPosition"] = 1;
                }
                
                //set success and cancelled site
                $requestParams = array( 'RETURNURL' => Paypal::$returnURL,
                                        'CANCELURL' => Paypal::$cancelURL);
                
                //send user to PayPal checkout
                $taxDollars = ($_SESSION['order_details']['CostOfItems']+$_SESSION['order_details']['Discount'])*(Paypal::$tax);
                SetExpressCheckout( $requestParams['RETURNURL'],
                                    $requestParams['CANCELURL'],
                                    $_SESSION['order_details']['Total']+$taxDollars,
                                    $_SESSION['order_details']['CostOfItems'],
                                    $_SESSION['order_details']['Shipping'],
                                    $_SESSION['order_details']['OrderID'], $itemArray,
                                    $_SESSION['order_details']['Currency'],
                                    $taxDollars
                        );
                
                $_SESSION['alert'] = 'Error, order was not submitted to PayPal.';
                header("location:../core/order_cancelled.php");
                exit();
                /*TESTING
                header("location:../core/order_success.php");
                exit();
                //END TESTING*/
            } else {
                //no items in current users cart
                $_SESSION['alert'] = 'There were no items in the order.';
                header("location:../core/order_cancelled.php");
                exit();
            }
        } else {
            $_SESSION['alert'] = 'Due to an unknown error your order was not processed.  Please try again later.\\n';
        }
    } else {
        $_SESSION['alert'] = 'Please create an account with us before submitting an order.';
        
        header("location:../?action=view_create_account&checkout=1");
        exit();
    }
    
    header("location:../");
    exit();