<?php class Review {
    private $id;
    private $itemID;
    private $rating;
    private $reviews;
    private $ratings;
    private $reviewCountForItem;

    private static $ReviewCount = 0;
    
    public function ApplyRatingToItem() {
        global $db;
        
        if($this->GetItemID()) {
            return $db->SafeExec("UPDATE items SET Rating = :0 WHERE `ID` = :1",array($this->GetRating(),$this->GetItemID()));
        } else {
            return 0;
        }
    }
    
    public function GetID() { return $this->id; }
    public function SetID($to) { $this->id = $to; }
    
    public function GetItemID() { return $this->itemID; }
    public function SetItemID($to) { $this->itemID = $to; }
    
    public function GetRating() { return $this->rating; }
    public function SetRating($to) { $this->rating = $to; }
    
    public function GetRatings() { return $this->ratings; }
    
    public function GetReviews() { return $this->reviews; }
    public function GetReview($num) { return $this->reviews[$num]; }
    public function SetReview($num, $to) { $this->reviews[$num] = $to; }
    
    public function GetReviewCount() { return $this->reviewCountForItem; }
    public function SetReviewCount($to) { $this->reviewCountForItem = $to; }

    public static function GetItemCount() { return self::$ReviewCount; }

    public function __construct() {
        $this->id = 0;
        $this->itemID = 0;
        $this->rating = 0;
        $this->reviews = array();
        $this->ratings = array();
        
        $this->reviewCountForItem = 0;
    
        self::$ReviewCount++;
    }
    public function __destruct() {
        self::$ReviewCount--;
    }
    
    public function SetFromDB($id) {
        require_once $_SESSION['rootDir'] . '../database.php';
        global $db;
        
        $values = array($id);

        $totalItems = $db->SafeFetch("SELECT COUNT(ID) AS Total FROM reviews WHERE ItemID = :0 AND Approved = 1",$values);
        $this->SetReviewCount($totalItems['Total']);
        
        $dbItem = $db->SafeFetchAll("SELECT Rating, Review FROM reviews WHERE ItemID = :0 AND Approved = 1",$values);

        //set all member variables here...
        $this->SetItemID($id);
        
        $tally = 0;
        if($dbItem) {
            foreach($dbItem as $row) {
                $tally += $row['Rating'];
                array_splice($this->reviews, 0, 0, $row['Review']);
                array_splice($this->ratings, 0, 0, $row['Rating']);
            }
        }
        
        if($this->GetReviewCount()) {
            $this->SetRating($tally / $this->GetReviewCount());
        } else {
            $this->SetRating(0);
        }
    }
}