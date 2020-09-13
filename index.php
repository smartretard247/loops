<?php
	// Require https
	if ($_SERVER['HTTPS'] != "on") {
		$url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		header("Location: $url");
		exit;
	}

	#$root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    $lifetime = 60 * 60 * 48; //48 hours
    ini_set('session.use_only_cookies', true);
    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);
    session_set_cookie_params($lifetime, '/'); //all paths, must be called before session_start()
    session_save_path(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/sessions'); session_start();
    
    date_default_timezone_set('America/New_York');

    #$_SESSION['rootDir'] = "/";
    $_SESSION['rootDir'] = "";
    $_SESSION['database'] = "madforthegram"; #global database
    include $_SESSION['rootDir'] . 'core/include.php';

    $topOfNewItems = -620;
    
    if(empty($_SESSION['valid_user'])) { $_SESSION['valid_user'] = false; $topOfLogin = -400; $topOfNewItems -= 150; }
    if(empty($_SESSION['admin_enabled'])) { $_SESSION['admin_enabled'] = false; }
    if(empty($_SESSION['debug'])) { $_SESSION['debug'] = false; }
    if(empty($_SESSION['error_message'])) { $_SESSION['error_message'] = ''; }
    if(empty($_SESSION['message'])) { $_SESSION['message'] = ''; }
    if(empty($_SESSION['alert'])) { $_SESSION['alert'] = ''; }
    if(empty($_SESSION['edit_mode'])) { $_SESSION['edit_mode'] = false; }
    if(empty($_SESSION['max_per_page'])) { $_SESSION['max_per_page'] = 10; }

    $_SESSION['thumb_lw'] = 150;
    $_SESSION['image_lw'] = 400;

    include_once 'view/header.php';

    $action = filter_input(INPUT_POST, 'action');
    if(!$action) { $action = filter_input(INPUT_GET, 'action'); }
    if(!$action) { $action = 'view_schedule'; }
    $select_all = filter_input(INPUT_POST, 'select_all');
    if(!$select_all) { $select_all = filter_input(INPUT_GET, 'select_all'); }
    
    
    $sortby = filter_input(INPUT_GET, 's');
    if(!$sortby) { $sortby = 'EventDate'; }
    
    $isDescending = filter_input(INPUT_GET, 'd'); 
    if($isDescending) { $desc = ' DESC'; } else { $desc = ''; $isDescending = 0; }
    
    $viewID = filter_input(INPUT_GET, 'id');
    if(!$viewID) { $viewID = 0; }

    ShowError();
    ShowMessage();
    
    

    //perform necessary action, sent by forms
    switch($action) {
        case 'delete_event':
            include 'view/view_schedule.php';
            break;
        case 'schedule_event':
            include 'view/view_schedule.php';
            break;
        case 'view_event':
            if($viewID > 0) {
                include 'view/view_event.php';
            } else {
                include 'view/view_schedule.php';
            }
            break;
        case 'reserve_seat': #decrease eventID ghost slot by 1 and assign an atName to the AtName field matching ID
            include 'view/view_schedule.php';
            break;
        default: //do default action
            ShowAlert();
            include 'view/view_schedule.php';
            break;
    } //end of switch statement
    
    ShowAlert();

    include 'view/footer.php';