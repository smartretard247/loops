<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database($_SESSION['database']);
    include_once $_SESSION['rootDir'] . '../core/include.php';
    
    $action = filter_input(INPUT_POST, 'action');
    if($_SESSION['valid_user']) { //only process order if logged in...
        $eventID = filter_input(INPUT_POST, 'eventID');
        if($eventID > 0) {
            if(!$db->SafeExec("UPDATE schedule SET Deleted = 1 WHERE ID = :0",array($eventID))) {
                $_SESSION['alert'] = "Error deleting event ID $eventID";
                header("location:../?action=view_schedule");
                exit();
            }
        }
    } else {
        $_SESSION['alert'] = 'You must be logged in to perform that function.';
        header("location:../?action=view_schedule");
        exit();
    }
    
    header("location:../?action=$action");
    exit();