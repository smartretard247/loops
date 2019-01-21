<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'cart.php'; $cCart = new Cart;
    
    $id = $_POST['ID']; //get arrays
    $itemID = $_POST['ItemID'];
    $q = $_POST['Quantity'];
    
    if($_SESSION['valid_user']) { //only add to database if logged in...
        //update entries
        for($i = 0; $i < count($id); $i++) {
            if($q[$i]) {
                $cCart->UpdateCart($id[$i], $q[$i]); //update quantity
            } else {
                $cCart->DeleteFromCart($id[$i]); //remove from cart
            }
        }
        
        $_SESSION['alert'] = 'Updated your cart.';
    } else {
        //update session cart
        for($i = 0; $i < count($_SESSION['cart']['ItemID']); $i++) {
            if($q[$i]) {
                $_SESSION['cart']['ItemID'][$i] = $itemID[$i];
                $_SESSION['cart']['Quantity'][$i] = $q[$i];
            } else {
                unset($_SESSION['cart']['ItemID'][$i]);
                unset($_SESSION['cart']['Quantity'][$i]);
                $_SESSION['cart']['ItemID'] = array_values($_SESSION['cart']['ItemID']);
                $_SESSION['cart']['Quantity'] = array_values($_SESSION['cart']['Quantity']);
            }
        }
    }
    
    header("location:../?action=view_my_cart");
    exit();