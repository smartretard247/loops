<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database($_SESSION['database']);
    include_once $_SESSION['rootDir'] . '../core/include.php';
    
    $action = filter_input(INPUT_POST, 'action');
    if($_SESSION['valid_user']) { //only process order if logged in...
        $seat = filter_input(INPUT_POST, 'seat');
        $atName = filter_input(INPUT_POST, 'atName');
        if(substr($atName,0,1) == "@") {
            if($seat != 'Feature') {
                $requestedEventID = filter_input(INPUT_POST, 'eventID');
                $number = filter_input(INPUT_POST, 'number');
                $count = 0;
                $nextSeats = $db->SafeFetchAll("SELECT * FROM schedule WHERE EventDate >= NOW()+INTERVAL 1 DAY AND ($seat > 0) AND ID NOT IN (SELECT EventId FROM reservations WHERE AtName = :0) AND Deleted = 0 ORDER BY EventDate LIMIT $number",array("$atName"));
                if($nextSeats[0]['ID'] != $requestedEventID) {
                    $_SESSION['message'] = "$atName was already part of requested event.  Moved to " . $nextSeats[0]['EventDate'];
                }
                foreach ($nextSeats as $next) {
                    ++$count;
                    $eventID = $next['ID'];
                    AddReservation($seat, $eventID, $atName, $count, $number);
                }
                #add pending
                for($i = $count; $i < $number; $i++) {
                    if(!$db->SafeExec("INSERT INTO pending (AtName, Number, Total, Seat) VALUES (:0, :1, :2, '$seat')",array($atName,$i+1,$number))) {
                        $_SESSION['alert'] = "Error reserving $seat slot number $i of $number for user $atName";
                        header("location:../?action=view_schedule");
                        exit();
                    }
                }
            } else {
                //process feature allocation dates
                $week = filter_input(INPUT_POST, 'week');
                $year = filter_input(INPUT_POST, 'year');
                $db->SafeExec("UPDATE features SET AtName = NULL WHERE ID < :0 AND Year < :1",array(getWeekNum()-1),date('Y')); //remove old reservations
                if(!$db->SafeExec("UPDATE features SET AtName = :0, Year = :1 WHERE ID = :2",array("$atName",$year,$week))) {
                    $_SESSION['alert'] = "Error reserving feature for $atName, week number $week";
                    header("location:../?action=view_schedule");
                    exit();
                }
            }
        } else {
            $_SESSION['alert'] = "Username must begin name with @ symbol, no reservations were made.";
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