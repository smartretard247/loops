<?php class Item {
    private $id;
    private $description;
    private $categoryID;
    private $categoryName;
    private $sellingPrice;
    private $purchasePrice;
    private $purchaseDate;
    private $quantityOnHand;
    private $quantityOnOrder;
    private $imgPath;
    private $imgFile;
    private $shipping;
    
    private $isHidden;

    private static $ItemCount = 0;

    public function GetID() { return $this->id; }
    public function SetID($to) { $this->id = $to; }
    
    public function IsHidden() { return $this->isHidden; }
    public function SetHidden($to) { $this->isHidden = $to; }
    
    public function GetShipping() { return $this->shipping; }
    public function SetShipping($to) { $this->shipping = $to; }
    
    public function GetThumbFile() {
        if($this->imgFile != '') {
            $temp = explode(".", $this->imgFile);
            $extension = end($temp);
            
            return substr_replace($this->imgFile, '_t', strlen($this->imgFile)-strlen($extension)-1, 0);
        } else { return ''; }
    }
    public function GetThumbPath() {
        $path = 'Images/inv/thumbs/' . $this->GetThumbFile();
        
        if(@getimagesize($path)) {
            return '' . $path;
        } else { return 'Images/inv/comingsoon_t.png'; }
    }
    
    public function GetImgFile() { return $this->imgFile; }
    public function SetImgFile($to) { $this->imgFile = $to; }

    public function GetImgPath() {
        if($this->imgFile != '' && $this->categoryName['Name'] != '') {
            $path = 'Images/inv/' . $this->categoryName['Name'] . '/' . $this->imgFile;
        
            if(@getimagesize($path)) {
                return '' . $path;
            } else { return 'Images/inv/comingsoon.png'; }
        } else { return 'Images/inv/comingsoon.png'; }
    }
    
    public function GetDescription() { return $this->description; }
    public function SetDescription($to) { $this->description = $to; }
    
    public function GetCategoryID() { return $this->categoryID; }
    public function SetCategoryID($to) { $this->categoryID = $to; }

    public function GetPrice() { return $this->sellingPrice; }
    public function SetPrice($to) { $this->sellingPrice = $to; }
    
    public function GetPurchasePrice() { return $this->purchasePrice; }
    public function SetPurchasePrice($to) { $this->purchasePrice = $to; }
    
    public function GetPurchaseDate() { return $this->purchaseDate; }
    public function SetPurchaseDate($to) { $this->purchaseDate = $to; }
    
    public function GetPurchaseYear() { return date('Y',strtotime($this->purchaseDate)); }
    public function GetPurchaseMonth() { return date('m',strtotime($this->purchaseDate)); }
    
    public function GetQOH() { return $this->quantityOnHand; }
    public function SetQOH($actualAmountOnHand) { $this->quantityOnHand = $actualAmountOnHand; }
    public function AddToOH($amountAddingToInventory) { $this->quantityOnHand += $amountAddingToInventory; }
    
    private function GetQOO() { return $this->quantityOnOrder; }
    public function SetQOO($actualAmountOnOrder) { $this->quantityOnOrder = $actualAmountOnOrder; }
    public function AddToOO($amountAddingToInventory) { $this->quantityOnOrder += $amountAddingToInventory; }
    
    public function GetQuantityForSale() {
        $left = $this->GetQOH() - $this->GetQOO();
        if($left>0) { return $left; } else { return 0; }
    }
    
    public static function GetCategoryArrayFromID($byID) {
        global $db;
        
        return $db->GetByID('item_category', $byID);
    }
    public function GetCategoryName() { return $this->categoryName['Name']; }
    public function GetCategorySingular() { return $this->categoryName['Singular']; }
    public function SetCategoryArray($to) { $this->categoryName = $to; }

    public static function GetItemCount() { return self::$ItemCount; }

    public function __construct() {
        $this->id = 0;
        $this->description = '';
        $this->categoryID = 0;
        $this->categoryName = array('Name' => '', 'Singular' => '');
        $this->sellingPrice = 0.0;
        $this->purchasePrice = 0.0;
        $this->purchaseDate = '2014-01-01';
        $this->quantityOnHand = 0;
        $this->quantityOnOrder = 0;
        $this->imgFile = '';
        $this->shipping = 2.5;
        
        $this->isHidden = 0;
    
        self::$ItemCount++;
    }
    public function __destruct() {
        self::$ItemCount--;
    }
    
    public function SetFromDB($id, $cols = '*') {
        require_once $_SESSION['rootDir'] . '../database.php';;
        global $db;

        $dbItem = $db->SafeFetch("SELECT $cols FROM items WHERE `ID` = :0",array($id));

        //set all member variables here...
        $this->SetID($id);
        $this->SetDescription($dbItem['Description']);
        $this->SetCategoryID($dbItem['CategoryID']);
        $this->SetCategoryArray(self::GetCategoryArrayFromID($dbItem['CategoryID']));
        $this->SetPrice($dbItem['Price']);
        $this->SetPurchasePrice($dbItem['PurchasePrice']);
        $this->SetPurchaseDate($dbItem['PurchaseDate']);
        $this->SetQOH($dbItem['QOH']);
        $this->SetQOO($dbItem['QOO']);
        $this->SetImgFile($dbItem['ImgFile']);
        $this->SetShipping($dbItem['Shipping']);
        
        $this->SetHidden($dbItem['Hidden']);
    }
}