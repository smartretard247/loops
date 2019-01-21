<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'address.php'; $cAddress = new Address();
    
    $_SESSION['error_message'] = '';
    $_SESSION['address_obj'] = '';

    //only valid if editing
    $id = filter_input(INPUT_POST, 'ID');
    $cAddress->SetID($id);
    
    //get account username to link here...
    $accountID = filter_input(INPUT_POST, 'AccountID');
    $cAddress->SetAccountID($accountID);
    
    $line1 = filter_input(INPUT_POST, 'Line1');
    if($line1 != '') {
        $cAddress->SetLine1($line1);
    } else { $_SESSION['error_message'] .= 'Please enter a valid address.<br/>'; }

    $line2 = filter_input(INPUT_POST, 'Line2');
    $cAddress->SetLine2($line2);
    
    $city = filter_input(INPUT_POST, 'City');
    if($city != '') {
        $cAddress->SetCity($city);
    } else { $_SESSION['error_message'] .= 'Please enter a city.<br/>'; }
    
    $state = filter_input(INPUT_POST, 'State');
    if($state != '') {
        $cAddress->SetState($state);
    } else { $_SESSION['error_message'] .= 'Please select a state.<br/>'; }
    
    $zip = filter_input(INPUT_POST, 'Zip');
    if(strlen($zip) == 5) {
        $cAddress->SetZip($zip);
    } else { $_SESSION['error_message'] .= 'Please enter a valid zip code.<br/>'; }
    
    $zipPlusFour = filter_input(INPUT_POST, 'ZipPlusFour');
    if(strlen($zipPlusFour) == 4 || strlen($zipPlusFour) == 0) {
        $cAddress->SetZipPlusFour($zipPlusFour);
    } else { $_SESSION['error_message'] .= 'Zip+4 was not valid.<br/>'; }
    
    $country = filter_input(INPUT_POST, 'Country');
    if($country != '') {
        $cAddress->SetCountry($country);
    } else { $_SESSION['error_message'] .= 'Please select a country.<br/>'; }
    
    $billing = filter_input(INPUT_POST, 'Billing');
    if($billing == 'on') {
        $cAddress->SetBilling(1);
    }
    
    $historical = filter_input(INPUT_POST, 'Historical');
    if($historical == 'on') {
        $cAddress->SetHistorical(1);
    }

    if($_SESSION['error_message'] == '') {
        $aArgs = array('addresses',
            'AccountID', $cAddress->GetAccountID(),
            'Line1', $cAddress->GetLine1(),
            'Line2', $cAddress->GetLine2(),
            'City', $cAddress->GetCity(),
            'State', $cAddress->GetState(),
            'Zip', $cAddress->GetZip(),
            'ZipPlusFour', $cAddress->GetZipPlusFour(),
            'Country', $cAddress->GetCountry(),
            'IsBilling', $cAddress->IsBilling(),
            'Historical', $cAddress->IsHistorical());
        
        if($cAddress->GetID() == 0) {
            $idAdded = $db->AddToDB($aArgs);
            if($idAdded) {
                $_SESSION['address_obj'] = null;
                $_SESSION['alert'] = 'Address saved successfully.';

                //on successful add goto...
                header("location:../?action=view_address_list&AccountID=$accountID");
                exit();
            } else { $_SESSION['error_message'] .= 'Unknown error.  Please try again later.<br/>'; }
        } else {
            //insert id into array at second position
            array_splice($aArgs, 1, 0, $cAddress->GetID());
            
            if($db->UpdateMultipleColumnsDB($aArgs)) {
                if($billing) {
                    //remove all other billing address bools
                    $values = array($cAddress->GetAccountID(),$id);
                    $setBillingQuery = "UPDATE addresses SET IsBilling = 0 WHERE AccountID = :0 AND ID != :1";

                    $db->SafeExec($setBillingQuery,$values);
                }
        
                $_SESSION['address_obj'] = null;
                $_SESSION['alert'] = 'Address updated successfully.';

                //on successful add goto...
                header("location:../?action=view_address_list&AccountID=$accountID");
                exit();
            } else {
                $_SESSION['error_message'] .= 'You did not make any changes.<br/>';
            }
        }
    } else {
        $_SESSION['error_message'] = substr($_SESSION['error_message'], 0, strlen($_SESSION['error_message'])-5);
    }
    
    $_SESSION['address_obj'] = serialize($cAddress);
    
    header("location:../?action=view_address_edit&AccountID=$accountID");
    exit();
