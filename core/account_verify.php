<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'account.php'; $cAccount = new Account();
    include_once $_SESSION['rootDir'] . 'cart.php'; $cCart = new Cart;
    
    $_SESSION['error_message'] = '';
    $_SESSION['account_obj'] = '';
    
    $goto_checkout = filter_input(INPUT_POST, 'checkout'); //for those who are purchasing their cart w/o account

    if(!$_SESSION['valid_user']) {
        $username = filter_input(INPUT_POST, 'ReqUsername');
        if($username != '') {
            if($cAccount->IsAvailable($username)) {
                $cAccount->SetUsername($username);
            } else { $_SESSION['error_message'] .= 'Username is already taken.<br/>'; }
        } else { $_SESSION['error_message'] .= 'Please enter a username.<br/>'; }

        $password = filter_input(INPUT_POST, 'ReqPassword');
        if($password != '') {
            if($password == filter_input(INPUT_POST, 'ReqPasswordVerify')) {
                $cAccount->SetPassword($password);
            } else { $_SESSION['error_message'] .= 'Passwords do not match.<br/>'; }
        } else { $_SESSION['error_message'] .= 'Password cannot be empty.<br/>'; }
    } else {
        $cAccount->SetUsername($_SESSION['valid_user']);
    }
    
    $lastname = filter_input(INPUT_POST, 'LastName');
    if($lastname != '') {
        $cAccount->SetLastName($lastname);
    } else { $_SESSION['error_message'] .= 'Please enter your last name.<br/>'; }
    
    $firstname = filter_input(INPUT_POST, 'FirstName');
    if($firstname != '') {
        $cAccount->SetFirstName($firstname);
    } else { $_SESSION['error_message'] .= 'Please enter your first name.<br/>'; }
    
    $mi = filter_input(INPUT_POST, 'MI');
    if($mi != '') {
        $cAccount->SetMI($mi);
    } //mi can be blank
    
    $title = filter_input(INPUT_POST, 'Title');
    if($title != '') {
        $cAccount->SetTitle($title);
    }
    
    $email = filter_input(INPUT_POST, 'Email', FILTER_VALIDATE_EMAIL);
    if($email) {
        $cAccount->SetEmail($email);
    } else { $_SESSION['error_message'] .= 'Please enter a valid email address.<br/>'; }
    
    //account settings
    $prefrowcount = filter_input(INPUT_POST, 'PrefRowCount');
    $cAccount->SetPrefRowCount($prefrowcount);

    if($_SESSION['error_message'] == '') {
        $_SESSION['max_per_page'] = $cAccount->GetPrefRowCount();
        
        $aArgs = array('accounts',
            'PrefRowCount', $cAccount->GetPrefRowCount(),
            'Email', $cAccount->GetEmail(),
            'Title', $cAccount->GetTitle(),
            'LastName', $cAccount->GetLastName(),
            'FirstName', $cAccount->GetFirstName(),
            'MI', $cAccount->GetMI());
        
        if(!$_SESSION['valid_user']) {
            array_splice($aArgs, 1, 0, $cAccount->GetUsername());
            array_splice($aArgs, 1, 0, 'ID');
            array_splice($aArgs, 1, 0, $cAccount->GetPassword());
            array_splice($aArgs, 1, 0, 'ThePassword');
            
            if($db->AddToDB($aArgs)) {
                $_SESSION['valid_user'] = $cAccount->GetUsername();
                $_SESSION['FirstName'] = $cAccount->GetFirstName();
                $_SESSION['account_obj'] = null;
                $_SESSION['alert'] = 'Account created successfully.';
                
                for($i = 0; $i < count($_SESSION['cart']['ItemID']); $i++) {
                    $cCart->AddToCart($_SESSION['cart']['ItemID'][$i], $_SESSION['cart']['Quantity'][$i]);
                }

                if($goto_checkout) {
                    header("location:../?action=view_checkout");
                    exit();
                } else {
                    header("location:../?action=view_my_account");
                    exit();
                }
            } else { $_SESSION['error_message'] .= 'Unknown error.  Please try again later.<br/>'; }
        } else {
            //insert id into array at second position
            array_splice($aArgs, 1, 0, $_SESSION['valid_user']);
            
            if($db->UpdateMultipleColumnsDB($aArgs)) {
                $_SESSION['account_obj'] = null;
                $_SESSION['alert'] = 'Account updated successfully.';

                header("location:../?action=view_my_account");
                exit();
            } else {
                $_SESSION['error_message'] .= 'You did not make any changes.<br/>';
            }
        }
    } else {
        $_SESSION['error_message'] = substr($_SESSION['error_message'], 0, strlen($_SESSION['error_message'])-5);
    }
    
    $_SESSION['account_obj'] = serialize($cAccount);
    
    header("location:../?action=view_edit_account&checkout=$goto_checkout");
    exit();
