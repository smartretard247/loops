<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'cart.php'; $cCart = new Cart;
    include_once $_SESSION['rootDir'] . 'paypal.php';
    
    $orderDetails = array(); //store the order details from paypal in this array
    
    //add PayPal confirmation here...
    $taxDollars = ($_SESSION['order_details']['CostOfItems']+$_SESSION['order_details']['Discount'])*(Paypal::$tax);
    $transactionID = DoExpressCheckoutPayment(  $_SESSION['order_details']['Total']+$taxDollars,
                                                $_SESSION['order_details']['CostOfItems'],
                                                $_SESSION['order_details']['Shipping'],
                                                $_SESSION['order_details']['OrderID'],
                                                $_SESSION['order_details']['Currency'],
                                                $taxDollars,
                                                $orderDetails);
    
    if($transactionID>0) {
        $db->SafeExec("UPDATE orderhistory SET Valid = 1 WHERE ID = :0",array($_SESSION['order_details']['OrderID']));

        $giftReceipt = ($orderDetails['GIFTRECEIPTENABLE']==true ? 1:0) ;
                
        if($db->SafeExec("UPDATE orders SET Status = 1, TransactionID = :0, GiftMessage = :1, GiftReceipt = :2 WHERE ID = :3",array($transactionID,$orderDetails['GIFTMESSAGE'],$giftReceipt,$_SESSION['order_details']['OrderID']))) {
            $address = array(   stripslashes($orderDetails['PAYMENTREQUEST_0_SHIPTONAME']),
                                $orderDetails['PAYMENTREQUEST_0_SHIPTOSTREET'],
                                $orderDetails['PAYMENTREQUEST_0_SHIPTOSTREET2'],
                                $orderDetails['PAYMENTREQUEST_0_SHIPTOCITY'],
                                $orderDetails['PAYMENTREQUEST_0_SHIPTOSTATE'],
                                $orderDetails['PAYMENTREQUEST_0_SHIPTOZIP'],
                                $orderDetails['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE']);
            
            $addressID = $db->SafeFetch("SELECT ID FROM addresses WHERE Historical = 0 AND AccountID = :0",array($_SESSION['valid_user']));
            
            if($addressID) {
                $address[7] = $addressID['ID'];
                $added = $db->SafeExec("UPDATE addresses SET ShipToName = :0, Line1 = :1, Line2 = :2, City = :3, State = :4, Zip = :5, Country = :6 WHERE ID = :7",$address);
            } else {
                $address[7] = $_SESSION['valid_user'];
                $added = $db->SafeExec("INSERT INTO addresses (ShipToName, Line1, Line2, City, State, Zip, Country, AccountID) VALUES (:0, :1, :2, :3, :4, :5, :6, :7)",$address);
                $addressID['ID'] = $db->GetDB()->lastInsertId('ID');
            }
            
            if(!$db->SafeExec("UPDATE orders SET ShippingAddressID = :0 WHERE ID = :1",array($addressID['ID'],$_SESSION['order_details']['OrderID']))) {
                //error adding address ID to order
                //send an email to the admin
            }
            
            $cCart->PurchaseCartFor($_SESSION['valid_user']);
            
            if($added) {
                $_SESSION['alert'] = 'Your order was submitted successfully.  Thank you for shopping with Simply Silver AKY!';
            } else {
                $_SESSION['alert'] = 'We are sorry, there was an error saving the shipping address for your order.  Please contact us to verify the shipping address.  Thank you.';
            }
        } else {
            $_SESSION['alert'] = 'We received your payment but there was an error completing your order.  Please contact us to verify your order.';
        }
    } else {
        //no transaction id
        $_SESSION['alert'] = 'There was an error processing your payment and you will not be charged.  Please try again later.';
        header("location:../core/order_cancelled.php");
        exit();
    }
    
    $_SESSION['order_details'] = null;
    $_SESSION['cart'] = null;

    header("location:../?action=view_my_orders&s=OrderDateTime&d=1");
    exit();