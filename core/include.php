<?php 
    include_once $_SESSION['rootDir'] . $adj . '../database.php';
    
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
    
    function AuditLog($message, $startNewline = true) {
        if($startNewline) {
            error_log("\r\n$message", 3, "../" . $_SESSION['database'] .".log");
        } else {
            error_log($message, 3, "../" . $_SESSION['database'] .".log");
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
            AuditLog($_SESSION['valid_user'] . " failed to reserve $seat $count of $number for $atName. NEED TO MANUALLY INCREASE '$seat' FOR EVENT $eventID");
            return false;
        }
        AuditLog($_SESSION['valid_user'] . " reserved $seat on event $eventID for $atName ($count of $number)");
        return true;
    }
    
    function RemovePendingReservation($id) {
        global $db;
        if(!$db->SafeExec("DELETE FROM pending WHERE ID = :0",array($id))) {
            $_SESSION['alert'] = "Error removing reservation for ID $id";
            return false;
        }
        AuditLog($_SESSION['valid_user'] . " removed pending reservation ID $id");
        return true;
    }
    
    function getDbId($accessCode) {
        $sigBit = getMsb($accessCode);
        return log($sigBit,2)+1; #get the ID of the most recent page/database
    }
    
    function getSigBit($n) {
        return pow(2, $n-1); #convert to power of 2
    }
    
    function setDbData($dbId) {
        global $db;
        $dbData = $db->SafeFetch("SELECT * FROM pages WHERE ID = :0",array($dbId));
        if($dbData) {
            $_SESSION['database'] = $dbData['DatabaseName'];
            $_SESSION['page'] = $dbData['LoopName'];
            $_SESSION['activeDbId'] = $dbData['ID'];
        } else {
            $_SESSION['database'] = "loop";
            $_SESSION['page'] = "Anne's InstaLoops";
            $_SESSION['activeDbId'] = 1;
        }
    }
    
    function setBanner() {
        $_SESSION['banner'] = "Images/banners/" . $_SESSION['database'] .  "/banner.png";
        $_SESSION['banneruser'] = "Images/banners/" . $_SESSION['database'] .  "/banneruser.png";
    }
    
    function getMsb($n)  { 
        $k =(int)(log($n, 2)); 
        return (int)(pow(2, $k)); 
    }
    
    function getWeekNum() {
        return date("W");
    }
    
    function getStartAndEndDate($week) {
        if($week < getWeekNum()) {
            $year = date("Y")+1;
        } else {
            $year = date("Y");
        }
        $ret['week_year'] = $year;
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('m-d');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('m-d');
        return $ret;
    }