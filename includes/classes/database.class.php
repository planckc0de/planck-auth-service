<?php

/* File Name        :   database.class.php
 * Project          :   Planck Auth
 * Version 		    :   1.0.0
 * Date Created	    :   OCT 30 2020
 * Date Modified	:   OCT 30 2020
 * Author			:   Yash Gohel ( Planck Studio )
 * Local URL        :   https://api.planckstud.io/auth/includes/class/database.class.php
 */

namespace Auth;
use \PDO as PDO;

class Database extends PDO {
    
    private static $dns;
    private static $user;
    private static $pass;
    private static $host;
    private static $db;
    private static $con;

    public function __construct() {
        // NOP
    }

    public static function connect() {
        
        self::$host = "localhost";
        self::$db = "planck";
        self::$dns = "mysql:host=".self::$host.";dbname=".self::$db.";";
        self::$user = "root";
        self::$pass = "";
        self::$con = NULL;

        if ( self::$con == NULL ) {
            try {
                self::$con = new PDO( self::$dns, self::$user, self::$pass );
                
                self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return self::$con;
            } catch( PDOException $e ) {
                return "Failed to connect database: " . $e->getMessage();
            }
        }
    }

    public static function close() {
        self::$con = NULL;
    }

    public static function execute( $query ) {
        try {
            self::connect();
            self::$con->exec($query);
            self::close();
        } catch( PDOException $e ) {
            return "Failed to execute query: " . $e->getMessage();
        }
    }

    public static function fetch( $query, $all = FALSE ) {
        try {
            self::connect();

            if ( $all ) {
                return self::$con->query($query)->fetchAll();
            } else {
                return self::$con->query($query)->fetch();
            }

            self::close();
        } catch( PDOException $e ) {
            return "Failed to fetch record(s): " . $e->getMessage();
        }
    }
}
?>