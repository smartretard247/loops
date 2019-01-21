<?php #$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    $lifetime = 60 * 60 * 3; //3 hours
    ini_set('session.use_only_cookies', true);
    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);
    session_set_cookie_params($lifetime, '/'); //all paths, must be called before session_start()
    session_save_path(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/sessions'); session_start();
    
    date_default_timezone_set('America/New_York');

    #$_SESSION['rootDir'] = "/";
    $_SESSION['rootDir'] = "";
    include $_SESSION['rootDir'] . 'core/include.php';

    $topOfNewItems = -620;
    
    if(empty($_SESSION['valid_user'])) { $_SESSION['valid_user'] = false; $topOfLogin = -400; $topOfNewItems -= 150; }
    if(empty($_SESSION['admin_enabled'])) { $_SESSION['admin_enabled'] = false; }
    if(empty($_SESSION['debug'])) { $_SESSION['debug'] = false; }
    if(empty($_SESSION['error_message'])) { $_SESSION['error_message'] = ''; }
    if(empty($_SESSION['message'])) { $_SESSION['message'] = ''; }
    if(empty($_SESSION['alert'])) { $_SESSION['alert'] = ''; }
    if(empty($_SESSION['edit_mode'])) { $_SESSION['edit_mode'] = false; }
    if(empty($_SESSION['max_per_page'])) { $_SESSION['max_per_page'] = 10; }
    if(empty($_SESSION['cart'])) {
        $_SESSION['cart'] = array(
            'ItemID'=> array(),
            'Quantity'=> array()
        );
        
    }

    $_SESSION['thumb_lw'] = 150;
    $_SESSION['image_lw'] = 400;

    include_once 'view/header.php';

    $action = filter_input(INPUT_POST, 'action');
    if(!$action) { $action = filter_input(INPUT_GET, 'action'); }
    if(!$action) { $action = 'view_home'; }
    $select_all = filter_input(INPUT_POST, 'select_all');
    if(!$select_all) { $select_all = filter_input(INPUT_GET, 'select_all'); }
    $sortby = filter_input(INPUT_GET, 's');
    if(!$sortby) { $sortby = 'ID'; }
    $isDescending = filter_input(INPUT_GET, 'd'); 
    if($isDescending) { $desc = ' DESC'; } else { $desc = ''; $isDescending = 0; }
    $page = filter_input(INPUT_GET, 'p');
    if(!$page) { $page = 0; }

    $cat = filter_input(INPUT_GET, 'cat');
    switch($action) {
        case 'view_new_items': $currPage = 'NewItems';
            break;
        case 'view_my_account': $currPage = 'MyAccount'; //both set to myaccount highlighted
            break;
        case 'view_my_orders': $currPage = 'MyAccount'; //both set to myaccount highlighted
            break;
        case 'view_my_cart': $currPage = 'MyAccount'; //both set to myaccount highlighted
            break;
        case 'view_all': $currPage = 'All';
            break;
        case 'view_item': $currPage = 'All';
            break;
        case 'view_category': $currPage = 'Category';
            break;
        case 'view_inventory': $currPage = 'Admin';
            break;
        case 'view_about_us': $currPage = 'AboutUs';
            break;
        case 'view_home': $currPage = 'Home';
            break;
        default: $currPage = '';
            break;
    }
    InsertNavigationBar($currPage, $cat);

    ShowError();
    ShowMessage();

    //perform necessary action, sent by forms
    switch($action) {
        case 'addpromo': 
            $promocode = filter_input(INPUT_POST, 'PromoCode');
            if($promocode != '') {
                $promocode = strtoupper($promocode);
                
                if($promocode == 'ILOVEYOU') {
                    include_once 'view/view_specialpromo.php';
                    $_SESSION['alert'] = $specialpromo; //hidden promo!
                } else {
                    $duplicate = $db->SafeFetch("SELECT ID FROM promos WHERE PromoCode = :0",array($promocode));
                    
                    if(!$duplicate) {
                        $percentoff = filter_input(INPUT_POST, 'PercentOff');
                        if($percentoff) {
                            $expires = filter_input(INPUT_POST, 'Expires');
                            if($expires != '') {
                                //add promo code
                                $values = array($promocode, number_format($percentoff,2), $expires);
                                if($db->SafeExec("INSERT INTO promos (PromoCode, PercentOff, Expires) VALUES (:0, :1, :2)",$values)) {
                                    $_SESSION['alert'] = "Added promotion code.";
                                } else {
                                    $_SESSION['error_message'] = "ERROR, ASK JESSE!"; ShowError();
                                }
                            } else {
                                //no expiration date
                                $_SESSION['error_message'] = "Must enter an expiration date"; ShowError();
                            }
                        } else {
                            //zero percent off
                            $_SESSION['error_message'] = "Zero percent off ain't a discount!"; ShowError();
                        }
                    } else {
                        $_SESSION['error_message'] = "That promotion code already exists."; ShowError();
                    }
                }
            } else {
                //blank promo code
                $_SESSION['error_message'] = "Did not enter a promotion code."; ShowError();
            }
            include 'view/view_promo.php';
            break;
        case 'delpromo': $id = filter_input(INPUT_POST, 'ID');
            if($db->SafeExec("DELETE FROM promos WHERE ID = :0",array($id))) {
                $_SESSION['alert'] = 'Promotion code deleted.';
            } else {
                $_SESSION['error_message'] = 'Could not delete promotion code.';
                ShowError();
            }
            include 'view/view_promo.php';
            break;
        case 'view_promo': include 'view/view_promo.php';
            break;
        case 'view_tax_info': $thisyear = date("Y");
            $taxyear = filter_input(INPUT_GET, 'taxyear');
            if(!$taxyear) { $taxyear = date("Y"); }
            include 'view/view_tax_info.php';
            break;
        case 'order_ship': $orderID = filter_input(INPUT_POST, 'OrderID');
            if($orderID) {
                if($db->SafeExec("UPDATE orders SET Status = 3 WHERE ID = :0",array($orderID))) {
                    $_SESSION['alert'] = 'Order marked as shipped.';
                } else {
                    $_SESSION['alert'] = 'Error marking order for shipment.';
                }
            } else {
                $_SESSION['alert'] = 'Incorrect order number.';
            }
            include 'view/view_orders.php';
            break;
        case 'view_checkout': $discount = 0; $promo = filter_input(INPUT_POST, 'PCode');
            if($promo) {
                $promorow = $db->SafeFetch("SELECT PercentOff, Expires FROM promos WHERE PromoCode = :0",array($promo));
                if($promorow) {
                    $today = strtotime("now");
                    $expiration = strtotime($promorow['Expires']);

                    if($today <= $expiration) {
                        $discount = $promorow['PercentOff'];
                    } else {
                        $_SESSION['alert'] = "That promotion code is not valid or has expired, sorry.";
                    }
                } else {
                    $_SESSION['alert'] = "That promotion code is not valid or has expired, sorry.";
                }
            }
            
            $itemssold = filter_input(INPUT_POST, 'ItemsSold');
            $shipping = filter_input(INPUT_POST, 'Shipping');
            $subtotal = filter_input(INPUT_POST, 'Subtotal');
            $ordertotal = filter_input(INPUT_POST, 'OrderTotal');
            $totalpurchaseprice = filter_input(INPUT_POST, 'TotalPurchasePrice');
            include 'view/view_checkout.php';
            break;
        //group viewing items actions
        case 'view_rate': $id = filter_input(INPUT_GET, 'id');
            include 'view/view_item_rate.php';
            break;
        case 'view_category':
        case 'view_all': include 'view/view_all.php';
            break;
        case 'view_item': $id = filter_input(INPUT_GET, 'id');
            include 'view/view_item.php';
            break;
        case 'view_new_items': $id = filter_input(INPUT_GET, 'id');
            include 'view/view_new_items.php';
            break;
        //end viewing items actions
        //group address actions
        case 'address_delete': $id = filter_input(INPUT_GET, 'id');
            $values = array($id);
            //$userThatOwnsAddress = $db->SafeFetch("SELECT AccountID FROM addresses WHERE ID = :0",$values);
            //if($userThatOwnsAddress['AccountID'] == $_SESSION['valid_user']) {
            if($_SESSION['admin_enabled']) {
                $linkedOrders = $db->SafeFetch("SELECT ID FROM orders WHERE ShippingAddressID = :0",$values);
                
                if(!$linkedOrders) {
                    $numRowsAffected = $db->SafeExec("DELETE FROM addresses WHERE ID = :0",$values);
                    if($numRowsAffected) { echo '<p class=success>'; } else { $numRowsAffected = 0; echo '<p class=error>'; }
                    echo $numRowsAffected . ' address was deleted.</p>';
                } else {
                    $_SESSION['error_message'] = 'That address is linked to an order, cannot delete.';
                    ShowError();
                }
            } else {
                $_SESSION['error_message'] = 'You are not authorized to delete that address.';
                ShowError();
            }
            include 'view/address_list.php';
            break;
        case 'view_address_list': $_SESSION['edit_mode'] = false;
            include 'view/address_list.php';
            break;
        case 'view_address_edit': $_SESSION['edit_mode'] = true;
            include 'view/address_add.php';
            break;
        case 'view_address_add': $_SESSION['edit_mode'] = false;
            include 'view/address_add.php';
            break;
        //end address actions
        //group item
        case 'view_inv_edit': $_SESSION['edit_mode'] = true;
            $cItem->SetFromDB(end($_POST));
            $_SESSION['item_obj'] = serialize($cItem);
        case 'view_item_edit': $_SESSION['edit_mode'] = true;
            include 'view/item_add.php';
            break;
        case 'view_upload_file': $_SESSION['edit_mode'] = false;
            include 'view/view_upload_file.php';
            break;
        case 'view_item_add': $_SESSION['edit_mode'] = false;
            include 'view/item_add.php';
            break;
        //end group item
        //group inventory actions
        case 'item_restore': $hidden = true;
            UpdateDBAndOutputText('items', end($_POST), 'Hidden', 0, 'item(s) restored to inventory.');
            include 'view/view_inventory.php';
            break;
        case 'item_remove': if(!isset($hidden)) { $hidden = 0; }
            UpdateDBAndOutputText('items', end($_POST), 'Hidden', 1, 'item(s) removed from inventory.');
            include 'view/view_inventory.php';
            break;
        case 'item_delete': $hidden = true;
            $idOfItemToDelete = end($_POST);
            RemoveFromDBByIDAndOutputText('items', $idOfItemToDelete, 'item(s) DELETED from inventory.');
            RemoveFromDBByIDAndOutputText('reviews', $idOfItemToDelete, 'reivews DELETED from database.', 'ItemID');
            include 'view/view_inventory.php';
            break;
        case 'view_pending_reviews': include 'view/view_pending_reviews.php';
            break;
        case 'view_account_dels': include 'view/view_account_dels.php';
            break;
        case 'view_removed': $hidden = true;
        case 'view_inventory': if(!isset($hidden)) { $hidden = 0; }
            include 'view/view_inventory.php';
            break;
        case 'view_my_orders': $myOrders = true;
            include 'view/view_orders.php';
            break;
        case 'view_orders': include 'view/view_orders.php';
            break;
        //end grouping of inventory
        //group account actions
        case 'view_order_cart': $orderView = true;
        case 'view_my_cart': include 'view/view_my_cart.php';
            break;
        case 'view_my_account': $_SESSION['edit_mode'] = true; 
            //if SetFromDB then you must serialize
            $cAccount->SetFromDB($_SESSION['valid_user']);
            $_SESSION['account_obj'] = serialize($cAccount);
            include 'view/account_add.php';
            break;
        case 'view_edit_account': $_SESSION['edit_mode'] = true;
            $goto_checkout = filter_input(INPUT_GET, 'checkout');
            include 'view/account_add.php';
            break;
        case 'view_create_account': $_SESSION['edit_mode'] = false;
            include 'view/account_add.php';
            break;
        case 'delete_account': include 'view/view_account_delete.php';
            break;
        case 'view_about_us': include 'view/about_us.php';
            break;
        //end account actions
        default: //do default action, load home page
            ShowAlert();
            include 'view/home.php';
            break;
    } //end of switch statement
    
    ShowAlert();

    include 'view/footer.php';