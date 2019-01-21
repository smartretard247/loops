<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'cart.php'; $cCart = new Cart;
    
    $itemID = filter_input(INPUT_POST, 'ID');
    $q = filter_input(INPUT_POST, 'Q');
    $qoh = filter_input(INPUT_POST, 'QOH');
    
    if($_SESSION['valid_user']) { //only add to database if logged in...
        $code = $cCart->AddToCart($itemID, $q);
        
        switch($code) {
            case 2: $_SESSION['alert'] = 'Updated quantity in your cart.'; break;
            case 1: $_SESSION['alert'] = 'Added to your cart.'; break;
            case -1: $_SESSION['alert'] = 'Quantity exceeded maximum on hand. Cart amount did not change.'; break;
            default: $_SESSION['alert'] = 'Could not update cart.'; break;
        }
        
        header("location:../?action=view_my_cart");
        exit();
    } else {
        //add to session cart
        array_splice($_SESSION['cart']['ItemID'], 0, 0, $itemID);
        array_splice($_SESSION['cart']['Quantity'], 0, 0, $q);
        
        $_SESSION['alert'] = 'Added to the guest cart.  Please login or create an account to save your cart.';
        
        header("location:../?action=view_my_cart");
        exit();
    }
    
    header("location:../?action=view_item&id=$itemID");
    exit();
