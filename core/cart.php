<?php class Cart {
    private $id;
    private $accountID;
    private $itemID;
    private $quantity;

    private static $CartCount = 0;
    
    public function GetID() { return $this->id; }
    public function SetID($to) { $this->id = $to; }
    
    public function GetAccountID() { return $this->accountID; }
    public function SetAccountID($to) { $this->accountID = $to; }

    public function GetItemID() { return $this->itemID; }
    public function SetItemID($to) { $this->itemID = $to; }
    
    public function GetQuantity() { return $this->quantity; }
    public function SetQuantity($to) { $this->quantity = $to; }

    public static function GetCartCount() { return self::$CartCount; }

    public function __construct() {
        $this->id = 0;
        $this->accountID = 0;
        $this->itemID = 0;
        $this->quantity = 0;
    
        self::$CartCount++;
    }
    public function __destruct() {
        self::$CartCount--;
    }
    
    public function SetFromDB($id) {
        require_once $_SESSION['rootDir'] . '../../database.php';
        global $db;

        $dbItem = $db->SafeFetch("SELECT * FROM carts WHERE `ID` = :0",array($id));

        //set all member variables here...
        $this->SetID($dbItem['ID']);
        $this->SetAccountID($dbItem['AccountID']);
        $this->SetItemID($dbItem['ItemID']);
        $this->SetQuantity($dbItem['Quantity']);
    }
    
    public static function AddToCart($itemID, $q) {
        global $db;
        
        $listing = $db->SafeFetchAll("SELECT carts.Quantity, carts.ID, (items.QOH - items.QOO) AS Available "
                . "FROM carts INNER JOIN items ON carts.ItemID=items.ID WHERE AccountID = :0 AND ItemID = :1 AND CartID = 0",array($_SESSION['valid_user'],$itemID));
        if($listing) {
            foreach($listing as $row) {
                if($row['Quantity']) {
                    if(($row['Available']-$row['Quantity']-$q)>=0) {
                        $q += $row['Quantity'];
                    } else {
                        return -1;
                    }
                    $exist = true;
                } else { $exist = false; }
            }
        } else { $exist = false; }
        
        $aArgs = array('carts',
            'AccountID', $_SESSION['valid_user'],
            'ItemID', $itemID,
            'Quantity', $q);
        
        if(!$exist) {
            if($db->AddToDB($aArgs)) {
                    return 1; //added
                } else { return 0; }
        } else {
            if($db->UpdateDB('carts', $row['ID'], 'Quantity', $q)) {
                    return 2; //updated
                } else { return 0; }
        }
    }
    
    public static function UpdateCart($id, $q) {
        global $db;
        
        $db->UpdateDB('carts', $id, 'Quantity', $q);
    }
    
    public static function DeleteFromCart($id) {
        global $db;
        
        $db->RemoveFromDBByID('carts', $id);
    }
    
    public function PurchaseCartFor($for) {
        global $db;
        
        $account = array($for); //array for select and update stmts
        
        //update the qoo for all items in the cart
        $results = $db->SafeFetchAll("SELECT ItemID, Quantity FROM carts WHERE AccountID = :0 AND CartID = 0",$account);
        
        foreach($results as $row) {
            if(!$db->SafeExec("UPDATE items SET QOO = QOO + :0 WHERE ID = :1",array($row['Quantity'],$row['ItemID']))) {
                return 0;
            }
        }
        
        if($db->SafeExec("UPDATE carts SET CartID = CartID + 1 WHERE AccountID = :0",$account)) {
            return $db->SafeExec("UPDATE orders SET CartID = CartID + 1 WHERE AccountID = :0",$account);
        } else { return 0; }
    }
}
