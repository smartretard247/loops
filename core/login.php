<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('loop'); #all accounts must be in once place
    include_once $_SESSION['rootDir'] . 'include.php';
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
            $_SESSION['access_code'] = $cAccount->GetAccessCode();
            $_SESSION['max_per_page'] = $cAccount->GetPrefRowCount();

            switch ($accountType) {
                case 4: //jesse (admin and debug)
                    $_SESSION['admin_enabled'] = true;
                    $_SESSION['debug'] = true;
                    $goto = "?action=view_schedule";
                    break;
                case 3: //anne (admin)
                    $_SESSION['admin_enabled'] = true;
                    $_SESSION['debug'] = false;
                    $goto = "?action=view_schedule";
                    break;
                case 2: //karen (admin)
                    $_SESSION['admin_enabled'] = true;
                    $_SESSION['debug'] = false;
                    $goto = "?action=view_schedule";
                    break;
                default: //regular user
                    $_SESSION['admin_enabled'] = false;
                    $_SESSION['debug'] = false;
                    $goto = "?action=view_schedule";
                    break;
            }
            
            $dbId = getDbId($_SESSION['access_code']);
            setDbData($dbId);
            setBanner();
        } else { $_SESSION['alert'] = 'Invalid username and/or password.  Please try again.'; }
    } else { $_SESSION['alert'] = 'You must enter a username and password to login.  Please try again.'; }
    
    header("location:../" . $goto);
    exit();