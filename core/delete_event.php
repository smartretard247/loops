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
            AuditLog($_SESSION['valid_user'] . " deleted event $eventID");
            
            #grab all reservations with this event id and reschedule
            $cancellations = $db->SafeFetchAll("SELECT AtName, Seat, Number, Total FROM reservations WHERE EventID = :0 ORDER BY ID",array($eventID));
            if($cancellations) {
                foreach($cancellations as $next) {
                    $seat = $next['Seat'];
                    $atName = $next['AtName'];
                    $number = $next['Number'];
                    $total = $next['Total'];
                    $nextSeat = $db->SafeFetchAll("SELECT * FROM schedule WHERE EventDate >= NOW()+1 AND ($seat > 0) AND ID NOT IN (SELECT EventID FROM reservations WHERE AtName = :0) AND Deleted = 0 ORDER BY EventDate LIMIT 1",array("$atName"));
                    if($nextSeat) {
                        $eventID = $nextSeat[0]['ID'];
                        $success = AddReservation($seat, $eventID, $atName, $number, $total);
                        if(!$success) {
                            $_SESSION['alert'] = "Error reserving $seat slot number $number of $total for user $atName";
                            header("location:../?action=view_schedule");
                            exit();
                        }
                        AuditLog($_SESSION['valid_user'] . " reserved $atName for event $eventID as $seat ($number of $total)");
                    } else {
                        #add to pending
                        if(!$db->SafeExec("INSERT INTO pending (AtName, Number, Total, Seat) VALUES (:0, :1, :2, '$seat')",array($atName,$number,$total))) {
                            $_SESSION['alert'] = "Error adding to $seat waitlist slot number $number of $total for user $atName";
                            header("location:../?action=view_schedule");
                            exit();
                        }
                        AuditLog($_SESSION['valid_user'] . " added pending $seat for $atName ($number of $total)");
                    }   
                }
            }
        }
    } else {
        $_SESSION['alert'] = 'You must be logged in to perform that function.';
        header("location:../?action=view_schedule");
        exit();
    }
    
    header("location:../?action=$action");
    exit();