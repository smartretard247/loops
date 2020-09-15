<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    $adj = "../"; //correct call within include.php
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('loop');
    include_once $_SESSION['rootDir'] . '../core/include.php';
    
    $action = filter_input(INPUT_POST, 'action');
    if($_SESSION['valid_user']) { //only process order if logged in...
        $accessCode = filter_input(INPUT_POST, 'accessCode');
        $dbId = filter_input(INPUT_POST, 'dbId');
        if($dbId && $accessCode) {
            $dbIdSigBit = getSigBit($dbId);
            if($accessCode & $dbIdSigBit) { #check if bit is on within the access code
                setDbData($dbId);
                setBanner();
            } else {
                $_SESSION['alert'] = 'You do not have permission for the requested loop page.';
                header("location:../?action=view_schedule");
                exit();
            }
        } else {
            $_SESSION['alert'] = 'Invalid access code and/or database ID.';
            header("location:../?action=view_schedule");
            exit();
        }
    } else {
        $_SESSION['alert'] = 'You must be logged in to perform that function.';
        header("location:../?action=view_schedule");
        exit();
    }
    
    header("location:../?action=$action");
    exit();