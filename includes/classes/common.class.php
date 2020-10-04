<?php 
    namespace Auth;

    class Common {

        public function __construct() {
            // NOP
        }

        public function generateRandomString( $length ) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $string = '';
    
            for ($i = 0; $i < $length; $i++) {
                $string .= $characters[mt_rand(0, strlen($characters) - 1)];
            }
    
            return $string;
        }

        public function isDataValid($data) {
            if ( $data !== NULL ) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public function generateServiceId() {
            $key = self::generateRandomString(9);
            $sid = 'S'.$key;
            return $sid;
        }
    
        public function generateLoginId() {
            $key = self::generateRandomString(9);
            $lid = 'L'.$key;
            return $lid;
        }
    
        public function generateLoginToken() {
            $key = self::generateRandomString(16);
            return $key;
        }
    
        public function generateAccessId() {
            $key = self::generateRandomString(8);
            $lid = 'AC'.$key;
            return $lid;
        }
    
        public function generateUserId() {
            $key = self::generateRandomString(9);
            $uid = 'U'.$key;
            return $uid;
        }

        public function generateApiToken() {
            $token = self::generateRandomString(16);
            return $token;
        }
    
        public function generateApiKey() {
            $key = self::generateRandomString(64);
            return $key;
        }

        function encryptString($string = '', $salt = 'LWT28YANM1Y3GWU3NZ6XAKVY5C7HCYX65EIM7BBAJKUJWOP6IB8KD0C0PR6F9WA1') {
            return openssl_encrypt($string, "AES-128-ECB", $salt);
        }
        
        function decryptString($encodedText = '', $salt = 'LWT28YANM1Y3GWU3NZ6XAKVY5C7HCYX65EIM7BBAJKUJWOP6IB8KD0C0PR6F9WA1') {
            return openssl_decrypt($encodedText, "AES-128-ECB", $salt);
        }
    }
?>