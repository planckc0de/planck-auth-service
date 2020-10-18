<?php 
    namespace Auth;

    class Functions {

        private static $con;
        private static $db;
        private static $cmn;

        public function __construct() {
            self::$db = $GLOBALS['dbc'];
            self::$con = self::$db->connect();
            self::$cmn = $GLOBALS['auth_common'];
        }

        public function userLogout($logout) {
            if (self::$cmn->isDataValid($logout->getValue('auth_login_ulid'))) {
                $id = $logout->getValue('auth_login_ulid');
                $query = 'UPDATE `auth_user_login` SET `user_session_active` = 0, `user_logout_time` = CURRENT_TIME() WHERE `user_ulid` = ?';
                $sql = self::$con->prepare($query);
                $sql->execute([ $id ]);
                return TRUE;
            } else{
                return FALSE;
            }
        }

        public function userLogin($login) {

            if ( self::$cmn->isDataValid($login->getValue('auth_user')) && 
                self::$cmn->isDataValid($login->getValue('auth_pass')) ) {

                $user = $login->getValue('auth_user');
                $pass = $login->getValue('auth_pass');

                $query = 'SELECT * FROM `auth_users` WHERE `user_name` LIKE ? LIMIT 1';
    
                $sql = self::$con->prepare($query);
                $sql->execute([ $user ]);
                $result = $sql->fetch();

                //$login->setValue('status', FALSE);

                    if ( $result['user_block'] != 1 ) {
                        $pass = self::$cmn->encryptString( $login->getValue('auth_pass') );
                        if ( $result['user_password'] == $pass ) {
                            $login->setValue('auth_uid', $result['user_uid']);
                            $login->setValue('auth_user', $result['user_name']);
                            $login->setValue('status', TRUE);
                            return $login;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return FALSE;
                    }
            }
        }

        public function addLoginHistory($login) {

            $query = 'INSERT INTO `auth_user_login`(
                `user_ulid`,
                `user_acid`,
                `user_login_token`,
                `user_login_session`
            ) VALUES (?, ?, ?, ?)';
        
                $sql = self::$con->prepare($query);
                $ulid = self::$cmn->generateLoginId();
                $ltoken = self::$cmn->generateLoginToken();
                $lsession = session_id();
        
                $sql->execute([
                    $ulid,
                    $login->getValue('auth_acid'),
                    $ltoken,
                    $lsession
                ]);

                $login->setValue('auth_login_ulid', $ulid);
                $login->setValue('auth_login_token', $ltoken);
                $login->setValue('auth_login_session', $lsession);
                $login->setValue('status', TRUE);

                return $login;
    
        }

        public function apiUserLogin($data) {

            $login = self::userLogin($data);
    
            if ( $login !== FALSE ) {

                $login->setValue('auth_sid', $data->getValue('auth_sid'));
                $access = self::checkAccess($login);
    
                if ( $access !== FALSE ) {

                    $hi = self::addLoginHistory($access);
                    $hi->setValue('auth_uid', $login->getValue('auth_uid'));
    
                    if ( $hi !== FALSE ) {
                        return $hi;
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                } 
            } else {
                return FALSE;
            }
        }

        public function registerNewUser($user) {

            if ( $user->getValue('auth_user') !== NULL &&
                $user->getValue('auth_email') !== NULL &&
                $user->getValue('auth_pass') !== NULL ) {

                if ( !( self::isUsernameExists($user) || self::isEmailExists($user) ) ) {

                    $query = "INSERT INTO `auth_users`(
                        `user_email`,
                        `user_name`,
                        `user_password`,
                        `user_uid`
                    ) VALUES (?, ?, ?, ?)";
    
                    $sql = self::$con->prepare($query);
                    $pass = self::$cmn->encryptString($user->getValue('auth_pass'));
                    $uid = self::$cmn->generateUserId();
    
                    $sql->execute([
                        $user->getValue('auth_email'),
                        $user->getValue('auth_user'),
                        $pass,
                        $uid
                    ]);
    
                    $user->setValue('auth_uid', $uid);
                    $user->setValue('status', TRUE);
                    $user->setValue('message', 'User registered');
    
                    if ( $user->getValue('auth_access_sid') !== NULL ) {
                        $user->setValue('auth_access_uid', $uid);
                        self::registerNewAccess($user);
                    }
    
                } else {
                    $user->setValue('status', FALSE);
                    $user->setValue('message', 'Username or Email already exists');
                }
                    
                
            } else{
                $user->setValue('status', FALSE);
                $user->setValue('message', 'Invalid data');
            }
            return $user;
        }
        
        public function registerNewService($service) {

            if ( $service->getValue('auth_service_uid') !== NULL &&
                $service->getValue('auth_service_name') !== NULL ) {
            
                $query = 'INSERT INTO `auth_services`(
                    `service_uid`,
                    `service_sid`,
                    `service_name`
                ) VALUES (?, ?, ?)';
        
                $sql = self::$con->prepare($query);
                $sid = self::$cmn->generateServiceId();
        
                $sql->execute([
                    $service->getValue('auth_service_uid'),
                    $sid,
                    $service->getValue('auth_service_name')
                ]);
                
                $service->setValue('auth_service_sid', $sid);
                $service->setValue('status', TRUE);
            } else{
                $service->setValue('status', FALSE);
            }

            return $service;
        }

        public function checkApi($api) {

            if ( self::$cmn->isDataValid($api->getValue('auth_token')) && 
                self::$cmn->isDataValid($api->getValue('auth_key')) ) {
                
                $query = 'SELECT * FROM `auth_apis` WHERE `api_token` LIKE ?';

                $sql = self::$con->prepare($query);
                $sql->execute([ $api->getValue('auth_token') ]);
                $result = $sql->fetch();

                if ( !$result ) {
                    $api->setValue('status', FALSE);
                } else if ( $result['api_block'] != 1 && $result['api_limited'] != 1 ) {
                    if ( $result['api_key'] == $api->getValue('auth_key') ) {
                        $api->setValue('auth_api_sid', $result['api_sid']);
                        $api->setValue('status', TRUE);
                    } else { 
                        $api->setValue('status', FALSE);
                    }
                } else {
                    $api->setValue('status', FALSE);
                }
                return $api;
            }
        }

        public function isUsernameExists($data) {

            $query = 'SELECT `user_name` FROM `auth_users` WHERE `user_name` LIKE ?';

            $sql = self::$con->prepare($query);
            $sql->execute([ 
                $data->getValue('auth_user'),
            ]);

            $result = $sql->fetch();

            if ( isset ( $result['user_name'] ) ) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public function isEmailExists($data) {

            $query = 'SELECT `user_email` FROM `auth_users` WHERE `user_email` LIKE ?';

            $sql = self::$con->prepare($query);
            $sql->execute([ 
                $data->getValue('auth_email'),
            ]);

            $result = $sql->fetch();

            if ( isset ( $result['user_email'] ) ) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        public function checkAccess($data) {

                $query = 'SELECT * FROM `auth_user_access` WHERE `user_sid` LIKE ? AND `user_uid` LIKE ?';
    
                $sql = self::$con->prepare($query);
                $sql->execute([ 
                    $data->getValue('auth_sid'),
                    $data->getValue('auth_uid')
                ]);

                $result = $sql->fetch();

                //$data->setValue('status', FALSE);
    
                    if ( $result['user_granted_access'] == TRUE ) {
                        $data->setValue('auth_acid', $result['user_acid']);
                        $data->setValue('status', TRUE);
                        return $data;
                    } else {
                        return FALSE;
                    }
                
        }

        public function registerNewApi($api) {

            if ( $api->getValue('auth_api_sid') !== NULL &&
                $api->getValue('auth_api_name') !== NULL ) {

                $query = 'INSERT INTO `auth_apis`(
                    `api_sid`,
                    `api_name`,
                    `api_token`,
                    `api_key`
                ) VALUES (?, ?, ?, ?)';

                $sql = self::$con->prepare($query);

                $sid = $api->getValue('auth_api_sid');
                $name = $api->getValue('auth_api_name');
                $token = strtoupper( self::$cmn->generateApiToken() );
                $key = self::$cmn->generateApiKey();


                $sql->execute([
                    $sid,
                    $name,
                    $token,
                    $key
                ]);

                $api->setValue('auth_api_token', $token);
                $api->setValue('auth_api_key', $key);
                $api->setValue('status', TRUE);

            } else{
                $api->setValue('status', FALSE);
            }
            return $api;
        }

        public function registerNewAccess($access) {

            if ( $access->getValue('auth_access_uid') !== NULL &&
                $access->getValue('auth_access_sid') !== NULL ) {

                $query = 'INSERT INTO `auth_user_access`(
                    `user_acid`,
                    `user_uid`,
                    `user_sid`
                ) VALUES (?, ?, ?)';

                $sql = self::$con->prepare($query);

                $uid = $access->getValue('auth_access_uid');
                $sid = $access->getValue('auth_access_sid');
                $acid = self::$cmn->generateAccessId();

                $sql->execute([
                    $acid,
                    $uid,
                    $sid
                ]);
                
                $access->setValue('auth_access_acid', $acid);
                $access->setValue('status', TRUE);
            } else {
                $access->setValue('status', FALSE);
            }
            return $access;
        }

        public function makeUserVerified($uid) {
            $query = "UPDATE `auth_users` SET `user_verified` = '1' WHERE `user_uid` LIKE ?"; 
            $sql = self::$con->prepare($query);
            $sql->execute([ $uid ]);
        }
    
        public function makeServiceVerified($sid) {
            $query = "UPDATE `auth_services` SET `service_verified` = '1' WHERE `service_sid` LIKE ?"; 
            $sql = self::$con->prepare($query);
            $sql->execute([ $sid ]);
        }
    }
?>