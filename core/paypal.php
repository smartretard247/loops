<?php class Paypal {
    //to use for future websites you must adjust the mode, tax, returnURL, and cancelURL below...
    
    private static $mode = 'SANDBOX';
    //private static $mode = 'LIVE';
    
    public static $returnURL = 'http://www.strawberryfountain.net/SimplySilverAKY/core/order_success.php';
    public static $cancelURL = 'http://www.strawberryfountain.net/SimplySilverAKY/core/order_cancelled.php';
    
    public static $tax = 0.03;
    
    public function GetMode() { return self::$mode; }
    
    /**
    * Last error message(s)
    * @var array
    */
   protected $_errors = array();

   /**
    * API Credentials
    * Use the correct credentials for the environment in use (Live / Sandbox)
    * @var array
    */
   protected $_credentials = array();

   /**
    * API endpoint
    * Live - https://api-3t.paypal.com/nvp
    * Sandbox - https://api-3t.sandbox.paypal.com/nvp
    * @var string
    */
   protected $_endPoint;

   /**
    * API Version
    * @var string
    */
   protected $_version = '76.0';

   /**
    * Make API request
    *
    * @param string $method string API method to request
    * @param array $params Additional request parameters
    * @return array / boolean Response array / boolean false on failure
    */
   public function request($method,$params = array()) {
      $this -> _errors = array();
      if( empty($method) ) { //Check if API method is not empty
         $this -> _errors = array('API method is missing');
         return false;
      }

      //Our request parameters
      $requestParams = array(
         'METHOD' => $method,
         'VERSION' => $this -> _version
      ) + $this -> _credentials;

      //Building our NVP string
      $request = http_build_query($requestParams + $params);

      //cURL settings
      $curlOptions = array (
         CURLOPT_URL => $this -> _endPoint,
         CURLOPT_VERBOSE => 1,
         CURLOPT_SSL_VERIFYPEER => true,
         CURLOPT_SSL_VERIFYHOST => 2,
         CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem', //CA cert file
         CURLOPT_RETURNTRANSFER => 1,
         CURLOPT_POST => 1,
         CURLOPT_POSTFIELDS => $request
      );

      $ch = curl_init();
      curl_setopt_array($ch,$curlOptions);

      //Sending our request - $response will hold the API response
      $response = curl_exec($ch);

      //Checking for cURL errors
      if (curl_errno($ch)) {
         $this -> _errors = curl_error($ch);
         curl_close($ch);
         return false;
         //Handle errors
      } else  {
         curl_close($ch);
         $responseArray = array();
         parse_str($response,$responseArray); // Break the NVP string to an array
         return $responseArray;
      }
   }
   
   public function __construct() {
       //set credentials
       switch (self::$mode) {
           case 'SANDBOX':
               $this->_credentials = array('USER' => 'anne.young21-facilitator_api1.yahoo.com',
                    'PWD' => '1394450425',
                    'SIGNATURE' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AOmrulDCOECZYFISjXJZTNWLHIlY',);
               $this->_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';
               break;
           case 'LIVE':
               $this->_credentials = array('USER' => 'anne.young21_api1.yahoo.com',
                    'PWD' => 'Q39D9GZ4CJAPGWVY',
                    'SIGNATURE' => 'AAkbY2EcDMGotX9dlANAvSzwc4EHAmwTQ1YoqHnvBkBqr9L8S72ToNsM',);
               $this->_endPoint = 'https://api-3t.paypal.com/nvp';
               break;
       }
   }
}

function SetExpressCheckout($returnURL, $cancelURL, $orderTotal, $costOfItems, $costOfShipping, $orderID, $itemArray, $currency = 'USD', $tax = 0.0) {
    //Our request parameters
    $requestParams = array('RETURNURL' => $returnURL,'CANCELURL' => $cancelURL);

    $orderParams = array(
       'ALLOWNOTE' => 0,
       'PAYMENTREQUEST_0_ITEMAMT' => number_format($costOfItems,2),
       'PAYMENTREQUEST_0_SHIPPINGAMT' => number_format($costOfShipping,2),
       'PAYMENTREQUEST_0_TAXAMT' => $tax,
       'PAYMENTREQUEST_0_AMT' => number_format($orderTotal,2),
       'PAYMENTREQUEST_0_CURRENCYCODE' => $currency,
       'PAYMENTREQUEST_0_INVNUM' => $orderID
    );

    $orderParams['GIFTMESSAGEENABLE'] = 1;
    $orderParams['GIFTRECEIPTENABLE'] = 1;
    $orderParams['GIFTWRAPENABLE'] = 1;
    $orderParams['GIFTWRAPNAME'] = "paper";
    $orderParams['GIFTWRAPAMOUNT'] = 1.0;
    $orderParams['MAXAMT'] = $orderTotal + $orderParams['GIFTWRAPAMOUNT'];
    
    $paypal = new Paypal();
    $response = $paypal -> request('SetExpressCheckout',$requestParams + $itemArray + $orderParams);

    if(is_array($response) && $response['ACK'] == 'Success') { //Request successful
        $token = $response['TOKEN'];
        
        switch ($paypal->GetMode()) {
            case 'SANDBOX':
                header( 'Location: https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
                exit();
                break;
            case 'LIVE':
                header( 'Location: https://www.paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
                exit();
                break;
        }
    } else { //problem setting  express checkout
        HandleResponse($response);
    }
}

function DoExpressCheckoutPayment($orderTotal, $costOfItems, $costOfShipping, $orderID, $currency = 'USD', $tax = 0.0, &$checkoutDetails = array()) {
    if( isset($_GET['token']) && !empty($_GET['token']) ) { // Token parameter exists
       // Get checkout details, including buyer information.
       // We can save it for future reference or cross-check with the data we have
       $paypal = new Paypal();
       $checkoutDetails = $paypal -> request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));
       
       // Complete the checkout transaction
       $requestParams = array(
           'TOKEN' => $_GET['token'],
           'PAYMENTACTION' => 'Sale',
           'PAYERID' => $_GET['PayerID'],
           'PAYMENTREQUEST_0_ITEMAMT' => $costOfItems,
           'PAYMENTREQUEST_0_SHIPPINGAMT' => $costOfShipping,
           'PAYMENTREQUEST_0_TAXAMT' => $tax,
           'PAYMENTREQUEST_0_AMT' => $orderTotal,
           'PAYMENTREQUEST_0_CURRENCYCODE' => $currency,
           'PAYMENTREQUEST_0_INVNUM' => $orderID
       );

       $response = $paypal -> request('DoExpressCheckoutPayment',$requestParams);
       if( is_array($response) && $response['ACK'] == 'Success') { // Payment successful
           // We'll fetch the transaction ID for internal bookkeeping
           return $response['PAYMENTINFO_0_TRANSACTIONID'];
       } else {
           HandleResponse($response);
       }
    }
}

function HandleResponse($response) {
    if(is_array($response)) {
        if(WhiteList($response['L_ERRORCODE0'])) {
            $_SESSION['alert'] = 'Error code: ' . $response['L_ERRORCODE0'] . '\\n\\n';
            $_SESSION['alert'] .= 'Short message: ' . $response['L_SHORTMESSAGE0'];
        } else {
            $_SESSION['alert'] = 'Response: ' . $response['ACK'] . '\\n\\n';
            $_SESSION['alert'] .= 'Error code: ' . $response['L_ERRORCODE0'] . '\\n\\n';
            $_SESSION['alert'] .= 'Short message: ' . $response['L_SHORTMESSAGE0'] . '\\n\\n';
            $_SESSION['alert'] .= 'Long message: ' . $response['L_LONGMESSAGE0'];
            
            try {
                if($this->GetMode() == 'LIVE') { $_SESSION['alert'] = 'An unknown error occurred.  Please try again later.'; }
            } catch (PDOException $exception) {
                $_SESSION['alert'] .= '\\n\\n' . $exception->getMessage();
            }
        }
    } else {
        $_SESSION['alert'] = 'No response from PayPal.';
    }
    
    header("location:../core/order_cancelled.php");
    exit();
}

function WhiteList($code) {
    switch ($code) {
        case 11607:
            return $code;
        default: return 0;
            break;
    }
}