<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database($_SESSION['database']);
    include_once $_SESSION['rootDir'] . '../core/include.php';
    
    $action = filter_input(INPUT_POST, 'action');
    if($_SESSION['valid_user']) { //only process order if logged in...
        $seat = filter_input(INPUT_POST, 'seat');
        $atName = filter_input(INPUT_POST, 'atName');
        if(substr($atName,0,1) == "@") {
            $number = filter_input(INPUT_POST, 'number');
            $count = 0;
            $nextSeats = $db->SafeFetchAll("SELECT * FROM schedule WHERE EventDate >= NOW()+1 AND ($seat > 0) AND Deleted = 0 ORDER BY EventDate LIMIT $number");
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