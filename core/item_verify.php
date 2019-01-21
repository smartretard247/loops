<?php $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'); //get root folder for relative paths
    session_save_path($root . '/sessions'); session_start();
    
    include_once $_SESSION['rootDir'] . '../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'item.php'; $cItem = new Item();
    
    require_once 'include.php';
    
    $_SESSION['error_message'] = '';
    $_SESSION['item_obj'] = '';
    
    //saving previous location
    $sortby = filter_input(INPUT_GET, 's');
    if(!$sortby) { $sortby = 'ID'; }
    $isDescending = filter_input(INPUT_GET, 'd'); 
    if(!$isDescending) { $isDescending = 0; }
    $page = filter_input(INPUT_GET, 'p');
    if(!$page) { $page = 0; }

    //only valid if editing
    $id = filter_input(INPUT_POST, 'ID');
    $cItem->SetID($id);
    
    $description = filter_input(INPUT_POST, 'Description');
    if($description != '') {
        $cItem->SetDescription($description);
    } else { $_SESSION['error_message'] .= 'Please enter a description.<br/>'; }

    $category = filter_input(INPUT_POST, 'CategoryID');
    if($category != '') {
        $cItem->SetCategoryID($category);
        $catArray = Item::GetCategoryArrayFromID($category);
        $cItem->SetCategoryArray($catArray);
    } else { $_SESSION['error_message'] .= 'You must select an item category.<br/>'; }
    
    $purchasePrice = filter_input(INPUT_POST, 'PurchasePrice');
    $cItem->SetPurchasePrice($purchasePrice);
    
    $price = filter_input(INPUT_POST, 'Price');
    if($price > 0) {
        $cItem->SetPrice($price);
    } else { $_SESSION['error_message'] .= 'Just giving it away?<br/>'; }
    
    $shipping = filter_input(INPUT_POST, 'Shipping');
    if($shipping >= 0) {
        $cItem->SetShipping($shipping);
    } else { $_SESSION['error_message'] .= 'Shipping cannot be less than zero.<br/>'; }
    
    $purchaseDate = filter_input(INPUT_POST, 'PurchaseDate');
    if($purchaseDate != '0000-00-00') {
        $cItem->SetPurchaseDate($purchaseDate);
    } else { $_SESSION['error_message'] .= 'Please select the date of purchase.<br/>'; }
    
    //check if image changed or is new image from adding a new item
    $imageChanged = filter_input(INPUT_POST, 'x');
    if($imageChanged == 'YES') {
        UploadFile($cItem->GetCategoryName());
        
        $cItem->SetImgFile($_SESSION['ImgName']);
        $_SESSION['ImgName'] = '';
    } else {
        $imgFile = filter_input(INPUT_POST, 'ImgFile');
        if($imgFile != '') {
            $cItem->SetImgFile($imgFile);
        } else { $_SESSION['error_message'] .= 'You must select an image.<br/>'; }
    }
        
    //specific for items only
    $qtyPurchased = filter_input(INPUT_POST, 'PurchaseQty');
    $qoh = filter_input(INPUT_POST, 'QOH');
    if($qtyPurchased || $qoh) {
        $qoh += $qtyPurchased;
    } else { $_SESSION['error_message'] .= 'You must have purchased at least one.<br/>'; }

    if($_SESSION['error_message'] == '') {
        $cItem->SetQOH($qoh);
        
        $aArgs = array('items',
            'Shipping', $cItem->GetShipping(),
            'ImgFile', $cItem->GetImgFile(),
            'QOH', $cItem->GetQOH(),
            'PurchaseDate', $cItem->GetPurchaseDate(),
            'PurchasePrice', $cItem->GetPurchasePrice(),
            'Price', $cItem->GetPrice(),
            'Description', $cItem->GetDescription(),
            'CategoryID', $cItem->GetCategoryID());
        
        if($cItem->GetID() == 0) {
            if($db->AddToDB($aArgs)) {
                $_SESSION['item_obj'] = null;
                $_SESSION['message'] = 'Item added to database successfully.';
                
                $db->SafeExec("INSERT INTO purchasehistory (ItemID, PurchaseYear, PurchaseMonth, PurchasePrice, Quantity) VALUES (:0, :1, :2, :3, :4)",
                        array($db->GetDB()->lastInsertId("ID"),$cItem->GetPurchaseYear(),$cItem->GetPurchaseMonth(),$cItem->GetPurchasePrice(),$qtyPurchased));

                header("location:../?action=view_item_add");
                exit();
            } else { $_SESSION['error_message'] .= 'Unknown error.  Please try again later.<br/>'; }
        } else {
            //insert id into array at second position
            array_splice($aArgs, 1, 0, $cItem->GetID());
            
            if($db->UpdateMultipleColumnsDB($aArgs)) {
                $_SESSION['item_obj'] = null;
                $_SESSION['message'] = 'Item updated successfully.';
                
                //update purchasehistory here...
                if(!$db->SafeExec("UPDATE purchasehistory SET Quantity = (Quantity + :0) WHERE ItemID = :1 AND PurchaseYear = :2",
                        array($qtyPurchased,$cItem->GetID(),$cItem->GetPurchaseYear()))) {
                    $db->SafeExec("INSERT INTO purchasehistory (ItemID, PurchaseYear, PurchasePrice, Quantity) VALUES (:0, :1, :2, :3)",
                        array($cItem->GetID(),$cItem->GetPurchaseYear(),$cItem->GetPurchasePrice(),$qtyPurchased));
                }
                
                header("location:../?action=view_inventory&s=$sortby&d=$isDescending&p=$page"); //go back to previous location
                exit();
            } else {
                $_SESSION['error_message'] .= 'You did not make any changes.<br/>';
            }
        }
    } else {
        $_SESSION['error_message'] = substr($_SESSION['error_message'], 0, strlen($_SESSION['error_message'])-5);
    }
    
    $_SESSION['item_obj'] = serialize($cItem);
    
    header("location:../?action=view_item_edit&s=$sortby&d=$isDescending&p=$page");
    exit();