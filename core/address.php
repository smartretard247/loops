<?php class Address {
    private $id;
    private $accountID;
    private $line1;
    private $line2;
    private $city;
    private $state;
    private $zip;
    private $zipPlusFour;
    private $country;
    private $isBilling;
    private $isHistorical;

    private static $AddressCount = 0;
    
    public function IsBilling() { return $this->isBilling; }
    public function SetBilling($to) { $this->isBilling = $to; }
    
    public function IsHistorical() { return $this->isHistorical; }
    public function SetHistorical($to) { $this->isHistorical = $to; }
    
    public function GetID() { return $this->id; }
    public function SetID($to) { $this->id = $to; }
    
    public function GetAccountID() { return $this->accountID; }
    public function SetAccountID($to) { $this->accountID = $to; }
    
    public function GetLine1() { return $this->line1; }
    public function SetLine1($to) { $this->line1 = $to; }
    
    public function GetLine2() { return $this->line2; }
    public function SetLine2($to) { $this->line2 = $to; }
    
    public function GetCity() { return $this->city; }
    public function SetCity($to) { $this->city = $to; }
    
    public function GetState() { return $this->state; }
    public function SetState($to) { $this->state = $to; }

    public function GetZip() { return $this->zip; }
    public function SetZip($to) { $this->zip = $to; }
    
    public function GetZipPlusFour() { return $this->zipPlusFour; }
    public function SetZipPlusFour($to) { $this->zipPlusFour = $to; }
    
    public function GetCountry() { return $this->country; }
    public function SetCountry($to) { $this->country = $to; }
    
    public static function GetAddressCount() { return self::$AddressCount; }

    public function __construct() {
        $this->id = 0;
        $this->accountID = 0;
        $this->line1 = '';
        $this->line2 = '';
        $this->city = '';
        $this->state = '';
        $this->zip = '';
        $this->zipPlusFour = '';
        $this->country = '';
        $this->isBilling = 0;
        $this->isHistorical = 0;
    
        self::$AddressCount++;
    }
    public function __destruct() {
        self::$AddressCount--;
    }
    
    public function SetFromDB($id) {
        require_once $_SESSION['rootDir'] . '../../database.php';
        global $db;

        $dbItem = $db->SafeFetch("SELECT * FROM addresses WHERE `ID` = :0",array($id));

        //set all member variables here...
        $this->SetID($id);
        $this->SetAccountID($dbItem['AccountID']);
        $this->SetLine1($dbItem['Line1']);
        $this->SetLine2($dbItem['Line2']);
        $this->SetCity($dbItem['City']);
        $this->SetState($dbItem['State']);
        $this->SetZip($dbItem['Zip']);
        $this->SetZipPlusFour($dbItem['ZipPlusFour']);
        $this->SetCountry($dbItem['Country']);
        $this->SetBilling($dbItem['IsBilling']);
        $this->SetHistorical($dbItem['Historical']);
    }
}
