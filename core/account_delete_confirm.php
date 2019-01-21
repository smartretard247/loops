<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('ssa');
    
    $_SESSION['error_message'] = '';
    
    $delAction = filter_input(INPUT_POST, 'action');
    $username = filter_input(INPUT_POST, 'AccountID');
    
    $values = array($username);
    
    if($delAction == "Delete") {
        $open_orders = $db->SafeFetchAll("SELECT ID FROM orders WHERE Status > -1 AND AccountID = :0",$values);
        if($open_orders) { $_SESSION['error_message'] = "User still has open orders.<br/>"; }

        if($_SESSION['error_message'] == '') {
            $numOrders = $db->SafeExec("DELETE FROM orders WHERE AccountID = :0",$values);
            $numCarts = $db->SafeExec("DELETE FROM carts WHERE AccountID = :0",$values);
            $numAddresses = $db->SafeExec("DELETE FROM addresses WHERE AccountID = :0",$values);
            $deleteSuccess = $db->SafeExec("DELETE FROM accounts WHERE ID = :0",$values);

            $db->SafeExec("DELETE FROM deletionRequests WHERE AccountID = :0",$values);

            $_SESSION['alert'] = "Account Successfully deleted:\\n\\nAccountID: $username\\n"
                    . "Orders: $numOrders\\nCarts: $numCarts\\nAddresses: $numAddresses";

            header("location:../?action=view_account_dels");
            exit();
        } else {
            $_SESSION['error_message'] = substr($_SESSION['error_message'], 0, strlen($_SESSION['error_message'])-5);
        }
    } elseif($delAction == "Cancel") { 
        $db->SafeExec("UPDATE accounts SET AccountType = 1 WHERE ID = :0",$values);
        $db->SafeExec("DELETE FROM deletionRequests WHERE AccountID = :0",$values);
        
        $_SESSION['alert'] = "Cancelled account deletion for:\\n\\nAccountID: $username";
        
        header("location:../?action=view_account_dels");
        exit();
    }
    
    $_SESSION['alert'] = "Error deleting account: " . $username;
        
    header("location:../?action=view_account_dels");
    exit();