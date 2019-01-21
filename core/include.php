<?php 
    include_once $_SESSION['rootDir'] . '../database.php'; $db = new Database('ssa');
    include_once $_SESSION['rootDir'] . 'item.php'; $cItem = new Item;
    include_once $_SESSION['rootDir'] . 'account.php'; $cAccount = new Account();
    include_once $_SESSION['rootDir'] . 'address.php'; $cAddress = new Address();
    include_once $_SESSION['rootDir'] . 'review.php'; $cReview = new Review();
    include_once $_SESSION['rootDir'] . 'cart.php'; $cCart = new Cart();

    $item_category = $db->GetTable('item_category', 'ID')->fetchAll(); //global item category list
    
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
    
    function ShowStars($num) {
        for($i = 0; $i < $num; $i++) {
            echo '<img src="images/star.png" height="20px" width="20px"/>';
        }
    }
    
    function GetThumbnailFilename($filename) {
        if($filename != '') {
            $temp = explode(".", $filename);
            $extension = end($temp);
            return substr_replace($filename, '_t', strlen($filename)-strlen($extension)-1, 0);
        } else {
            return '../comingsoon_t.png';
        }
    }
    
    function GetStatusNoun($forInteger) {
        switch($forInteger) {
            case -1: return 'Complete'; break;
            case 0: return 'Ordered (payment not received)'; break;
            case 1: return 'Payment received'; break;
            case 2: return 'Processing'; break;
            case 3: return 'Shipped'; break;
            case 8: return 'Pending return'; break;
            case 9: return 'Returned, refunded'; break;
            default: return 'Error';
        }
    }
    
    function InsertPageNavigation($page, $numPages, $cat = 0, $admin = false, $action = '') {
        $sortby = filter_input(INPUT_GET, 's');
        $isDescending = filter_input(INPUT_GET, 'd');
    
        if($cat) { $act = 'view_category'; } else { $act = 'view_all'; }
        if($admin) {
            if($action == '') {
                $act = 'view_inventory';
            } else {
                $act = $action;
            }
        } else {
            if($action != '') {
                $act = $action;
            }
        }
        
        echo '<table class="pagenav topmargin" align="center"><tr><td>Page:&nbsp;';
        
        if($page) {
            echo '&nbsp;<a href="index.php?action=' . $act;
            if($cat) { echo '&cat=' . $cat; }
            if($sortby != '') { echo '&s=' . $sortby; }
            if($isDescending) { echo '&d=1'; }
            echo '&p=' . ($page-1) . '">&leftarrow;</a>&nbsp;';
        } else {
            echo '&nbsp;&leftarrow;&nbsp;';
        }
    
        if($numPages > 1) {
            for($i = 1; $i < $numPages; $i++) {
                if($page == $i - 1) {
                    echo ' ' . $i . ' ';
                } else {
                    echo '&nbsp;<a href="index.php?action=' . $act;
                    if($cat) { echo '&cat=' . $cat; }
                    if($sortby != '') { echo '&s=' . $sortby; }
                    if($isDescending) { echo '&d=1'; }
                    echo '&p=' . ($i-1) . '">' . $i . '</a>&nbsp;';
                }
            }
        } else {
            echo ' 1 ';
        }
    
        if($page < $numPages-2) {
            echo '&nbsp;<a href="index.php?action=' . $act;
            if($cat) { echo '&cat=' . $cat; }
            if($sortby != '') { echo '&s=' . $sortby; }
            if($isDescending) { echo '&d=1'; }
            echo '&p=' . ($page+1) . '">&rightarrow;</a>&nbsp;';
        } else {
            echo '&nbsp;&rightarrow;&nbsp;';
        }
        echo '</td></tr></table>';
    }
    
    function GetTotalOrders($for, $taxyear = '2014') {
        global $db;
        
        switch ($for) {
            case 'adminmenu': $totalOrders = $db->Query("SELECT COUNT(ID) AS Total FROM orders WHERE Status > -1 AND Status != 3")->fetch();
                break;
            case 'userorderpage': $totalOrders = $db->SafeFetch("SELECT COUNT(ID) AS Total FROM orders WHERE Status > -2 AND AccountID = :0",array($_SESSION['valid_user']));
                break;
            case 'adminorderpage': $totalOrders = $db->Query("SELECT COUNT(ID) AS Total FROM orders WHERE Status > -1")->fetch();
                break;
            case 'fortaxinfo': $totalOrders = $db->SafeFetch("SELECT COUNT(ID) AS Total FROM orderhistory WHERE OrderYear = :0",array($taxyear));
                break;
            case 'purchases': $totalOrders = $db->SafeFetch("SELECT COUNT(ID) AS Total FROM purchasehistory WHERE PurchaseYear = :0",array($taxyear));
                break;
            default: $totalOrders = 0;
                break;
        }
        
        return $totalOrders['Total'];
    }
    
    function GetTotalPendingReviews() {
        global $db;
        $totalItems = $db->Query("SELECT COUNT(ID) AS Total FROM reviews WHERE Approved = '0'")->fetch();
        
        return $totalItems['Total'];
    }
    
    function GetTotalAccountDeletions() {
        global $db;
        $totalItems = $db->Query("SELECT COUNT(ID) AS Total FROM deletionrequests WHERE ID > 0")->fetch();
        
        return $totalItems['Total'];
    }
    
    function InsertSortMenu($currURL, $isDesc = 0) {
        echo '<table class="sortmenu topmargin"><tr><th>Sort by:</th></tr>';
        
        echo '<tr><td><a href="' . $currURL . '&s=Rating&d=' . $isDesc . '">Rating</a></td></tr>';
        echo '<tr><td><a href="' . $currURL . '&s=Price&d=' . $isDesc . '">Price</a></td></tr>';
        
        echo '</table>';
    }
    
    function InsertAdminMenu() {
        $totorders = GetTotalOrders('adminmenu');
        $totpenreview = GetTotalPendingReviews();
        $totaccdels = GetTotalAccountDeletions();
        
        echo '<li><a href="index.php?action=view_inventory">Admin</a>';
        
        echo '<ul><li>';
        
        //orders
        if($totorders) {
            echo '<a href="index.php?action=view_orders&s=OrderDateTime">Open Orders <span class="highlight">(' . $totorders . ')</span></a>';
        } else {
            echo '<a href="index.php?action=view_orders&s=OrderDateTime">Open Orders</a>';
        }
        echo '<a href="index.php?action=view_orders&s=OrderDateTime&d=1&vc=1">Completed Orders</a>';

        //inventory
        echo '<a href="index.php?action=view_inventory&s=id&d=1">Current Inventory</a>';
        if($_SESSION['debug']) {
            echo '<a href="index.php?action=view_removed">Removed Items</a>';
        }
        
        echo '<a href="index.php?action=view_tax_info">Order/Purchase History</a>';
        
        //account deletions
        if($totaccdels) {
            echo '<a href="index.php?action=view_account_dels">Account Deletions <span class="highlight">(' . $totaccdels . ')</span></a>';
        } else {
            echo '<a href="index.php?action=view_account_dels">Account Deletions</a>';
        }
        
        //reviews
        if($totpenreview) {
            echo '<a href="index.php?action=view_pending_reviews">Pending Reviews <span class="highlight">(' . $totpenreview . ')</span></a>';
        } else {
            echo '<a href="index.php?action=view_pending_reviews">Pending Reviews</a>';
        }
        
        echo '<a href="index.php?action=view_address_list">User Addresses</a>';
        
        echo '<a href="index.php?action=view_item_add">Add Item To Inventory</a>';
        echo '<a href="index.php?action=view_upload_file">Upload Images</a>';
        echo '<a href="index.php?action=view_promo&s=PromoCode">Add Promotion Code</a>';
        echo '</li></ul>';
        
        echo '</li>';
    }
    
    function InsertMyAccountMenu($current = '') {
        if($current == 'MyAccount') {
            echo '<li><a class="current" href="index.php?action=view_my_account">Account</a>';
        } else {
            echo '<li><a href="index.php?action=view_my_account">Account</a>';
        }
        
        echo '<ul><li>';
        echo '<a href="index.php?action=view_my_account">My Account</a>';
        echo '<a href="index.php?action=view_my_orders&s=OrderDateTime&d=1">My Orders</a>';
        echo '<a href="index.php?action=view_my_cart">View Cart</a>';
        echo '</li></ul>';
        
        echo '</li>';
    }

     function InsertInventoryMenu() {
        global $item_category;
        
        echo '<li><a href="index.php?action=view_all">Category</a>';
        
        echo '<ul><li>';
        echo '<a href="index.php?action=view_all">View All</a>';
        
        foreach ($item_category as $titemcat) {
            echo '<a href="index.php?action=view_category&cat=' . $titemcat['ID'] . '">' . $titemcat['Name'] . '</a>';
        }
        
        echo '</li></ul>';
        echo '</li>';
    }

    function InsertNavigationBar($current = '', $catID = 0) {
        global $db, $item_category;
        
        $loggedIn = $_SESSION['valid_user'];
        
        echo '<ul id="nav">';
        
        switch ($current) {
            case 'NewItems': echo '<li><a href="index.php">Home</a></li>';
                echo '<li><a href="index.php?action=view_about_us">About Us</a></li>';
                echo '<li><a class="current" href="index.php?action=view_new_items">New Items</a></li>';
                break;
            case 'AboutUs': echo '<li><a href="index.php">Home</a></li>';
                echo '<li><a class="current" href="index.php?action=view_about_us">About Us</a></li>';
                echo '<li><a href="index.php?action=view_new_items">New Items</a></li>';
                break;
            default:
                echo '<li><a href="index.php">Home</a></li>';
                echo '<li><a href="index.php?action=view_about_us">About Us</a></li>';
                echo '<li><a href="index.php?action=view_new_items">New Items</a></li>';
                break;
        }
        
        
        if($_SESSION['admin_enabled']) { InsertAdminMenu(); }
        if($loggedIn) { InsertMyAccountMenu($current); }
        InsertInventoryMenu();
        
        echo '</ul>';
    }
    
    function NoDataRow($array, $colspan, $text = 'No data exists in the table.') {
        if($array == 0) {
            echo '<tr><td colspan="' . $colspan . '"><b>' . $text . '</b></td></tr>';
        }
    }
    
    function UpdateDBAndOutputText($table, $id, $colTitle, $colData, $text) {
        global $db;
        $numRowsAffected = $db->UpdateDB($table, $id, $colTitle, $colData);
        if($numRowsAffected) { echo '<p class=success>'; } else { $numRowsAffected = 0; echo '<p class=error>'; }
        echo $numRowsAffected . ' ' . $text . '</p>';
    }
    
    function RemoveFromDBByIDAndOutputText($table, $id, $text, $colName = 'ID') {
        global $db;
        $numRowsAffected = $db->RemoveFromDBByID($table, $id, $colName);
        if($numRowsAffected) { echo '<p class=success>'; } else { $numRowsAffected = 0; echo '<p class=error>'; }
        echo $numRowsAffected . ' ' . $text . '</p>';
    }
    
    function UploadFile($categoryName) {
    
    $_SESSION['alert'] = '';
    $_SESSION['ImgName'] = '';

    $category = $categoryName;
    $upload_path = '../images/inv/' . $category . '/';
    $thumb_path = '../images/inv/thumbs/';

    $max_file_size = 40000000; //in bytes
    $allowedExts = array("gif", "GIF", "jpeg", "JPEG", "jpg", "JPG", "png", "PNG");
    $temp = explode(".", $_FILES["ImgFile"]["name"]);
    $extension = end($temp);
    
    if (in_array($extension, $allowedExts)) {
        if($_FILES["ImgFile"]["size"] < $max_file_size) {
            if ($_FILES["ImgFile"]["error"] > 0) {
                $_SESSION['alert'] .= "Return Code: " . $_FILES["ImgFile"]["error"] . "\\n";
            } else {
                $_SESSION['alert'] .= "Upload: " . $_FILES["ImgFile"]["name"] . "\\n";
                $_SESSION['alert'] .= "Type: " . $_FILES["ImgFile"]["type"] . "\\n";
                $_SESSION['alert'] .= "Size: " . ($_FILES["ImgFile"]["size"] / 1024) . " kB\\n\\n";

                if (file_exists($upload_path . $_FILES["ImgFile"]["name"])) {
                    $_SESSION['alert'] .= $_FILES["ImgFile"]["name"] . " already exists on the server.\\n";
                } else {
                    CreateThumb($thumb_path, $_FILES["ImgFile"]["name"], $extension, $_SESSION['thumb_lw']);
                    CreateImage($upload_path, $_FILES["ImgFile"]["name"], $extension, $_SESSION['image_lw']);
                    //move_uploaded_file($_FILES["ImgFile"]["tmp_name"], $upload_path . $_FILES["ImgFile"]["name"]);

                    $_SESSION['alert'] .= "Stored in: " . $upload_path . $_FILES["ImgFile"]["name"];
                }

                $_SESSION['ImgName'] = $_FILES["ImgFile"]["name"];
            }
        } else { $_SESSION['alert'] .= "File size exceeded.\\n"; }
    } else {
        $_SESSION['alert'] .= "Invalid file.\\n";
    }
}
    
    function CreateThumb($path, $filename, $extension, $newwidth, $quality = 100) {
        $uploadedfile = $_FILES['ImgFile']['tmp_name'];
                
        if($extension=="jpg" || $extension=="JPG") {
            $filename = substr_replace($filename, '_t', strlen($filename)-4, 0);
            $src = imagecreatefromjpeg($uploadedfile);
        } else if($extension=="jpeg" || $extension=="JPEG") {
            $filename = substr_replace($filename, '_t', strlen($filename)-5, 0);
            $src = imagecreatefromjpeg($uploadedfile);
        } else if($extension=="png" || $extension=="PNG") {
            $filename = substr_replace($filename, '_t', strlen($filename)-4, 0);
            $src = imagecreatefrompng($uploadedfile);
        } else {
            $filename = substr_replace($filename, '_t', strlen($filename)-4, 0);
            $src = imagecreatefromgif($uploadedfile);
        }
        
        list($width,$height)=getimagesize($uploadedfile);
        
        $newheight=($height/$width)*$newwidth;
        $tmp=imagecreatetruecolor($newwidth,$newheight);
        
        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
        
        imagejpeg($tmp,$path . $filename,$quality);
        
        imagedestroy($src);
        imagedestroy($tmp);
    }
    
    function CreateImage($path, $filename, $extension, $newwidth, $quality = 100) {
        $uploadedfile = $_FILES['ImgFile']['tmp_name'];
                
        if($extension=="jpg" || $extension=="JPG") {
            $src = imagecreatefromjpeg($uploadedfile);
        } else if($extension=="jpeg" || $extension=="JPEG") {
            $src = imagecreatefromjpeg($uploadedfile);
        } else if($extension=="png" || $extension=="PNG") {
            $src = imagecreatefrompng($uploadedfile);
        } else {
            $src = imagecreatefromgif($uploadedfile);
        }
        
        list($width,$height)=getimagesize($uploadedfile);
        
        $newheight=($height/$width)*$newwidth;
        $tmp=imagecreatetruecolor($newwidth,$newheight);
        
        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
        
        imagejpeg($tmp,$path . $filename,$quality);
        
        imagedestroy($src);
        imagedestroy($tmp);
    }
