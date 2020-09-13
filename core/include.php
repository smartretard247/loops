<?php 
    include_once $_SESSION['rootDir'] . '../database.php'; $db = new Database($_SESSION['database']);
    
    function ShowError() {
        //display error message
        if($_SESSION['error_message'] != '') {
            echo '<p class="error">' . $_SESSION['error_message'] . '</p>';
            $_SESSION['error_message'] = '';
        }
    }
    function ShowMessage() {
        if($_SESSION['message'] != '') {
            echo '<p class="success">' . $_SESSION['message'] . '</p>';
            $_SESSION['message'] = '';
        }
    }
    function ShowAlert() {
        if($_SESSION['alert'] != '') {
            echo '<script type="text/javascript">alert("' . $_SESSION['alert'] . '")</script>';
            $_SESSION['alert'] = '';
        }
    }
    
    function NoDataRow($array, $colspan, $text = 'No data exists in the table.') {
        if($array == 0) {
            echo '<tr><td colspan="' . $colspan . '"><b>' . $text . '</b></td></tr>';
        }
    }
    
    function AddReservation($seat, $eventID, $atName, $count, $number) {
        global $db;
        if(!$db->SafeExec("UPDATE schedule SET $seat = $seat-1 WHERE ID = :0",array($eventID))) {
            $_SESSION['alert'] = "Error descreasing available $seat slots for event ID $eventID";
            return false;
        } elseif(!$db->SafeExec("INSERT INTO reservations (AtName, Seat, EventId, Number, Total) VALUES (:0, '$seat', :1, :2, :3)",array($atName,$eventID,$count,$number))) {
            $_SESSION['alert'] = "Error reserving $seat slot number $count of $number for user $atName";
            return false;
        }
        return true;
    }
    
    function RemovePendingReservation($id) {
        global $db;
        if(!$db->SafeExec("DELETE FROM pending WHERE ID = :0",array($id))) {
            $_SESSION['alert'] = "Error removing reservation for ID $id";
            return false;
        }
        return true;
    }