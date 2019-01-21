<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'account.php'; $cAccount = new Account();
    include_once $_SESSION['rootDir'] . 'cart.php'; $cCart = new Cart;
    
    $_SESSION['error_message'] = '';
    
    if($_SESSION['valid_user']) {
        $username = filter_input(INPUT_POST, 'AccountID');
        $reason = filter_input(INPUT_POST, 'Reason');
        
        if($username == '' || $reason == '') {
            $_SESSION['error_message'] .= 'Please enter a reason for leaving, so we can make our site better.<br/>';
        }
    } else {
        $_SESSION['error_message'] .= 'You must be logged in to do that.<br/>';
    }

    if($_SESSION['error_message'] == '') {
        $db->SafeExec("UPDATE accounts SET AccountType = 0 WHERE ID = :0",array($username));
        $db->SafeExec("INSERT INTO deletionRequests (AccountID, Reason) VALUES (:0, :1)",array($username,$reason));
        
        //send email to administrator with link to account for deletion...
        $to      = 'anne.young21@yahoo.com';
        $subject = 'Hello';
        $message = 'I Love You';
        $headers = 'From: stalkerguy@efrance.com' . "\r\n" .
            'Reply-To: smartretard247@hotmail.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        //mail($to, $subject, $message, $headers);

        
        $_SESSION['alert'] = 'Your request to delete your account has been received.  Please allow up to 3 business days for processing.  Thank you.';
        
        header("location:../core/logout.php");
        exit();
    } else {
        $_SESSION['error_message'] = substr($_SESSION['error_message'], 0, strlen($_SESSION['error_message'])-5);
    }
    
    header("location:../?action=delete_account");
    exit();
