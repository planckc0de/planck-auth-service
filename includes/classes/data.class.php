<?php 
    namespace Auth;

    class Data {
        
        private static $data = array();
 
        public function __construct() {
            // NOP
        }

        public static function setValue( $name, $value ) {
            self::$data[$name] = $value;
        }

        public static function getValue( $name ) {
            return self::$data[$name];
        }

    }
?>