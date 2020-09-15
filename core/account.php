<?php class Account {
    private $username;
    private $thePassword;
    private $accountType;
    private $lastname;
    private $firstname;
    private $mi;
    private $email;
    private $title;
    private $accessCode;
    
    private $prefRowCount;

    private static $AccountCount = 0;
    
    public function GetUsername() { return $this->username; }
    public function SetUsername($to) { $this->username = $to; }
    
    public function GetAccessCode() { return $this->accessCode; }
    public function SetAccessCode($to) { $this->accessCode = $to; }
    
    public function GetPassword() { return $this->thePassword; }
    public function SetPassword($to) { $this->thePassword = md5($to); }

    public function GetAccountType() { return $this->accountType; }
    public function SetAccountType($to) { $this->accountType = $to; }
    
    public function GetLastName() { return $this->lastname; }
    public function SetLastName($to) { $this->lastname = $to; }
    
    public function GetFirstName() { return $this->firstname; }
    public function SetFirstName($to) { $this->firstname = $to; }
    
    public function GetMI() { return $this->mi; }
    public function SetMI($to) { $this->mi = $to; }
    
    public function GetTitle() { return $this->title; }
    public function SetTitle($to) { $this->title = $to; }
    
    public function GetEmail() { return $this->email; }
    public function SetEmail($to) { $this->email = $to; }
    
    public function GetPrefRowCount() { return $this->prefRowCount; }
    public function SetPrefRowCount($to) { $this->prefRowCount = $to; }

    public static function GetAccountCount() { return self::$AccountCount; }

    public function __construct() {
        $this->username = '';
        $this->thePassword = '';
        $this->accountType = 0;
        $this->lastname = '';
        $this->firstname = '';
        $this->mi = '';
        $this->email = '';
        $this->title = '';
        $this->prefRowCount = 0;
        $this->accessCode = 1;
    
        self::$AccountCount++;
    }
    public function __destruct() {
        self::$AccountCount--;
    }
    
    public static function IsAvailable($user) {
        require_once '../../database.php';
        global $db;
        
        $dbItem = $db->SafeFetch("SELECT AutoID FROM accounts WHERE `ID` = :0",array($user));
        
        if($dbItem['AutoID']) { return false; } else { return true; }
    }

    public function HasValidCombo() {
        require_once '../../database.php';
        global $db;
        
        $dbItem = $db->SafeFetch("SELECT AccountType FROM accounts WHERE `ID` = :0 AND ThePassword = :1",
                array($this->username,$this->thePassword));
        
        return $dbItem['AccountType'];
    }
    
    public function SetFromDB($username) {
        require_once '../../database.php';
        global $db;

        $dbItem = $db->SafeFetch("SELECT * FROM accounts WHERE `ID` = :0",array($username));

        //set all member variables here...
        $this->SetUsername($dbItem['ID']);
        $this->SetLastName($dbItem['LastName']);
        $this->SetFirstName($dbItem['FirstName']);
        $this->SetMI($dbItem['MI']);
        $this->SetEmail($dbItem['Email']);
        $this->SetTitle($dbItem['Title']);
        $this->SetPrefRowCount($dbItem['PrefRowCount']);
        $this->SetAccessCode($dbItem['AccessCode']);
    }
}