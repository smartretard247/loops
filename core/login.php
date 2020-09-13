<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database($_SESSION['database']);
    include_once $_SESSION['rootDir'] . 'account.php'; $cAccount = new Account;
    
    $cAccount->SetUsername(strtolower(filter_input(INPUT_POST, 'Username')));
    $cAccount->SetPassword(filter_input(INPUT_POST, 'ThePassword'));
    
    $username = $cAccount->GetUsername();
    $password = $cAccount->GetPassword();
    
    $goto = "";

    if($username != '' && $password != '') {
        $accountType = $cAccount->HasValidCombo();
        if($accountType) {
            $cAccount->SetFromDB($username);

            $_SESSION['valid_user'] = $cAccount->GetUsername();
            $_SESSION['FirstName'] = $cAccount->GetFirstName();

            for($i = 0; $i < count($_SESSION['cart']['ItemID']); $i++) {
                $cCart->AddToCart($_SESSION['cart']['ItemID'][$i], $_SESSION['cart']['Quantity'][$i]);
            }

            $_SESSION['cart'] = null;

            $_SESSION['max_per_page'] = $cAccount->GetPrefRowCount();

            switch ($accountType) {
                case 4: //jesse
                    $_SESSION['admin_enabled'] = true;
                    $_SESSION['debug'] = true;
                    $goto = "?action=view_schedule";
                    break;
                case 3: //anne
                    $_SESSION['admin_enabled'] = true;
                    $_SESSION['debug'] = false;
                    $goto = "?action=view_schedule";
                    break;
                case 2: //karen
                    $_SESSION['admin_enabled'] = true;
                    $goto = "?action=view_schedule";
                    break;
                default:
                    $goto = "?action=view_schedule";
                    break;
            }
        } else { $_SESSION['alert'] = 'Invalid username and/or password.  Please try again.'; }
    } else { $_SESSION['alert'] = 'You must enter a username and password to login.  Please try again.'; }
    
    header("location:../" . $goto);
    exit();