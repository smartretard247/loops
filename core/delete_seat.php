<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database($_SESSION['database']);
    include_once $_SESSION['rootDir'] . '../core/include.php';
    
    $action = filter_input(INPUT_POST, 'action');
    if($_SESSION['valid_user']) { //only process order if logged in...
        $eventID = filter_input(INPUT_POST, 'eventID');
        if($eventID > 0) {
            $reservationId = filter_input(INPUT_POST, 'reservationID');
            $seat = filter_input(INPUT_POST, 'seat');
            $atName = filter_input(INPUT_POST, 'atName');
            if(!$db->SafeExec("DELETE FROM reservations WHERE ID = :0",array($reservationId))) {
                $_SESSION['alert'] = "Error deleting reservation number $reservationId";
                header("location:../?action=view_schedule");
                exit();
            }
            AuditLog($_SESSION['valid_user'] . " deleted $atName's $seat reservation for event $eventID");
            
            if(!$db->SafeExec("UPDATE schedule SET $seat = $seat+1 WHERE ID = :0",array($eventID))) {
                $_SESSION['alert'] = "Error increasing $seat count for event ID $eventID ($atName)";
                header("location:../?action=view_schedule");
                exit();
            }
            AuditLog(" and increased $seat's available", false);
        }
    } else {
        $_SESSION['alert'] = 'You must be logged in to perform that function.';
        header("location:../?action=view_schedule");
        exit();
    }
    
    header("location:../?action=$action");
    exit();