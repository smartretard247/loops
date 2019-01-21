<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'review.php'; $cReview = new Review();
    
    $_SESSION['error_message'] = '';

    //only valid if editing
    $id = filter_input(INPUT_POST, 'ID');
    $approval = filter_input(INPUT_POST, 'Approved');
    $itemID = filter_input(INPUT_POST, 'ItemID');
    $rating = filter_input(INPUT_POST, 'Rating');
    $review = filter_input(INPUT_POST, 'Review');
    if($review == "Enter your comment here...") {
        $review = '';
    }
    
    if(!$_SESSION['valid_user']) {
        $_SESSION['error_message'] .= 'Please <a href="index.php?action=view_create_account" style="text-decoration: underline;">create an account</a> with us to rate or review items.<br/>';
    }
    
    if($_SESSION['error_message'] == '') {
        $aArgs = array('reviews',
            'Approved', $approval,
            'Rating', $rating,
            'Review', $review,
            'ItemID', $itemID);
        
        if(!$approval) {
            if($db->AddToDB($aArgs)) {
                $_SESSION['alert'] = 'Your review is now pending approval.  Thanks for your support!';

                header("location:../?action=view_item&id=$itemID");
                exit();
            } else { $_SESSION['error_message'] .= 'Unknown error.  Please try again later.<br/>'; }
        } else {
            //insert id into array at second position
            array_splice($aArgs, 1, 0, $id);
            
            if($db->UpdateMultipleColumnsDB($aArgs)) {
                $cReview->SetFromDB($itemID);
                
                $cReview->ApplyRatingToItem();
                $_SESSION['message'] = 'Review was processed successfully.';

                header("location:../?action=view_pending_reviews");
                exit();
            } else {
                $_SESSION['error_message'] .= 'You did not make any changes.<br/>';
            }
        }
    } else {
        $_SESSION['error_message'] = substr($_SESSION['error_message'], 0, strlen($_SESSION['error_message'])-5);
    }
    
    header("location:../?action=view_item&id=$itemID");
    exit();