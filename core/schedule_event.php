<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database($_SESSION['database']);
    include_once $_SESSION['rootDir'] . '../core/include.php';
    
    $action = filter_input(INPUT_POST, 'action');
    if($_SESSION['valid_user']) { //only process order if logged in...
        $eventDate = filter_input(INPUT_POST, 'eventDate');
        $ghosts = filter_input(INPUT_POST, 'ghosts');
        $vips = filter_input(INPUT_POST, 'vips');
        $features = filter_input(INPUT_POST, 'features');
        if($eventDate) {
            if(!$db->SafeExec("INSERT INTO schedule (EventDate, Ghost, VIP, Feature) VALUES (:0, :1, :2, :3)",array($eventDate,$ghosts,$vips,$features))) {
                $_SESSION['alert'] = "Error scheduling event for $eventDate";
                header("location:../?action=view_schedule");
                exit();
            } else {
                #success, add the pending ghost/vips
                $lastId = $db->SafeFetch("SELECT MAX(ID) AS LastID FROM schedule");
                $eventID = $lastId['LastID'];

                $pending[0] = $db->SafeFetchAll("SELECT ID, AtName, Number, Total, Seat FROM (SELECT ID, MIN(AtName) AS AtName, Number, Total, Seat FROM pending GROUP BY AtName ORDER BY ID) uni WHERE Seat = 'Ghost' LIMIT $ghosts");
                $pending[1] = $db->SafeFetchAll("SELECT ID, AtName, Number, Total, Seat FROM (SELECT ID, MIN(AtName) AS AtName, Number, Total, Seat FROM pending GROUP BY AtName ORDER BY ID) uni WHERE Seat = 'VIP' LIMIT $vips");
                $pending[2] = $db->SafeFetchAll("SELECT ID, AtName, Number, Total, Seat FROM (SELECT ID, MIN(AtName) AS AtName, Number, Total, Seat FROM pending GROUP BY AtName ORDER BY ID) uni WHERE Seat = 'Feature' LIMIT $features");
                
                foreach($pending as $package) {
                    foreach ($package as $next) {
                        $id = $next['ID'];
                        $atName = $next['AtName'];
                        $count = $next['Number'];
                        $number = $next['Total'];
                        $seat = $next['Seat'];
                        $success = AddReservation($seat, $eventID, $atName, $count, $number);
                        if($success) {
                            RemovePendingReservation($id);
                        }
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