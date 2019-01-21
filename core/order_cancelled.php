<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'cart.php'; $cCart = new Cart;
    include_once $_SESSION['rootDir'] . 'paypal.php';
    
    $values = array($_SESSION['order_details']['OrderID']);
    $db->SafeExec("DELETE FROM orderhistory WHERE ID = :0",$values);
    $db->SafeExec("DELETE FROM orders WHERE ID = :0",$values);
    
    //add PayPal error handling here...
    if($_SESSION['alert'] == '') {
        $_SESSION['alert'] = 'Your order has been successfully cancelled.  Thank you.';
    }
    
    $_SESSION['order_details'] = null;
    header("location:../");
    exit();