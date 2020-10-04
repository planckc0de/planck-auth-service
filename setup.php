<?php 
    namespace Auth;
    include_once 'includes/load.php';

    $create_table = 'CREATE TABLE IF NOT EXISTS `auth_users` (
        `user_id` INT(10) NOT NULL AUTO_INCREMENT,
        `user_uid` VARCHAR(10) NOT NULL,
        `user_name` VARCHAR(20) NOT NULL,
        `user_email` VARCHAR(30) NOT NULL,
        `user_cc` INT(3) NULL DEFAULT NULL,
        `user_no` VARCHAR(10) NULL DEFAULT NULL,
        `user_verified` TINYINT(1) NOT NULL DEFAULT 0,
        `user_block` TINYINT(1) NOT NULL DEFAULT 0,
        `user_password` VARCHAR(256) NOT NULL,
        `user_signup` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`user_id`),
        UNIQUE (`user_uid`),
        UNIQUE (`user_name`, `user_email`)
    );';

    $create_table .= 'CREATE TABLE IF NOT EXISTS `auth_user_meta` (
        `user_mid` INT(10) NOT NULL AUTO_INCREMENT,
	    `user_uid` VARCHAR(10) NOT NULL,
        `meta_name` VARCHAR(50) NOT NULL,
        `meta_value` VARCHAR(256) NULL DEFAULT NULL,
        PRIMARY KEY (`user_mid`) 
    );';

    $create_table .= 'CREATE TABLE IF NOT EXISTS `auth_services` (
        `service_id` INT(10) NOT NULL AUTO_INCREMENT,
	    `service_sid` VARCHAR(10) NOT NULL,
	    `service_uid` VARCHAR(10) NOT NULL,
        `service_name` VARCHAR(20) NOT NULL,
        `service_verified` TINYINT(1) NOT NULL DEFAULT 0,
	    `service_block` TINYINT(1) NOT NULL DEFAULT 0,
        `service_signup` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`service_id`),
        UNIQUE (`service_sid`),
	    FOREIGN KEY (`service_uid`) REFERENCES `auth_users` (`user_uid`) ON DELETE CASCADE
    );';

    $create_table .= 'CREATE TABLE IF NOT EXISTS `auth_service_meta` (
        `service_mid` INT(10) NOT NULL AUTO_INCREMENT,
	    `service_sid` VARCHAR(10) NOT NULL,
        `meta_name` VARCHAR(50) NOT NULL,
        `meta_value` VARCHAR(256) NULL DEFAULT NULL,
        PRIMARY KEY (`service_mid`)
    );';

    $create_table .= 'CREATE TABLE IF NOT EXISTS `auth_apis` (
        `api_id` INT(10) NOT NULL AUTO_INCREMENT,
	    `api_sid` VARCHAR(10) NOT NULL,
        `api_name` VARCHAR(20) NOT NULL,
	    `api_token` VARCHAR(16) NOT NULL,
	    `api_key` VARCHAR(64) NOT NULL,
	    `api_block` TINYINT(1) NOT NULL DEFAULT 0,
	    `api_limited` TINYINT(1) NOT NULL DEFAULT 0,
        `api_signup` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`api_id`),
        UNIQUE (`api_sid`),
	    FOREIGN KEY (`api_sid`) REFERENCES `auth_services` (`service_sid`) ON DELETE CASCADE
    );';

    $create_table .= 'CREATE TABLE IF NOT EXISTS `auth_user_access` (
        `user_aid` INT(10) NOT NULL AUTO_INCREMENT,
	    `user_acid` VARCHAR(10) NOT NULL,
	    `user_uid` VARCHAR(10) NOT NULL,
	    `user_sid` VARCHAR(10) NOT NULL,
	    `user_granted_access` TINYINT(1) NOT NULL DEFAULT 1,
	    `user_access_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	    `user_access_revoke_time` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`user_aid`),
        UNIQUE (`user_acid`)
    );';

    $create_table .= 'CREATE TABLE IF NOT EXISTS `auth_user_login` (
        `user_lid` INT(10) NOT NULL AUTO_INCREMENT,
	    `user_ulid` VARCHAR(10) NOT NULL UNIQUE,
	    `user_acid` VARCHAR(10) NOT NULL,
	    `user_session_active` TINYINT(1) NOT NULL DEFAULT 1,
	    `user_login_token` VARCHAR(64) NOT NULL,
	    `user_login_session` VARCHAR(256) NOT NULL,
	    `user_login_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	    `user_logout_time` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`user_lid`)
    );';

    $create_table .= 'CREATE TABLE IF NOT EXISTS `auth_user_login_meta` (
        `login_mid` INT(10) NOT NULL AUTO_INCREMENT,
        `login_ulid` VARCHAR(10) NOT NULL UNIQUE,
        `meta_name` VARCHAR(50) NOT NULL,
        `meta_value` VARCHAR(256) NULL DEFAULT NULL,
        PRIMARY KEY (`login_mid`)
    );';

    $dbc->execute($create_table);

    $auth_data->setValue('auth_user', 'planck');
    $auth_data->setValue('auth_pass', 'antimat@STUDIO18112701');
    $auth_data->setValue('auth_email', 'support@planckstud.io');

    $reg = $auth_fun->registerNewUser($auth_data);
    $auth_fun->makeUserVerified($reg->getValue('auth_uid'));

    echo '<h3>Default User</h3>';
    echo 'Username: '.$reg->getValue('auth_user');
    echo '<br/> Email: '.$reg->getValue('auth_email');
    echo '<br/> UID: '.$reg->getValue('auth_uid');

    $auth_data->setValue('auth_service_uid', $reg->getValue('auth_uid'));
    $auth_data->setValue('auth_service_name', 'Planck Auth Service');

    $ser = $auth_fun->registerNewService($auth_data);
    $auth_fun->makeServiceVerified($ser->getValue('auth_service_sid'));

    echo '<h3>Default Service</h3>';
    echo 'Name: '.$ser->getValue('auth_service_name');
    echo '<br/> SID: '.$ser->getValue('auth_service_sid');

    $auth_data->setValue('auth_api_sid', $ser->getValue('auth_service_sid'));
    $auth_data->setValue('auth_api_name', 'Auth api');

    $api = $auth_fun->registerNewApi($auth_data);

    echo '<h3>Default API</h3>';
    echo 'Name: '.$api->getValue('auth_api_name');
    echo '<br/> Token: '.$api->getValue('auth_api_token');
    echo '<br/> Key: '.$api->getValue('auth_api_key');
    
    $auth_data->setValue('auth_access_uid', $reg->getValue('auth_uid'));
    $auth_data->setValue('auth_access_sid', $ser->getValue('auth_service_sid'));
    $acs = $auth_fun->registerNewAccess($auth_data);

    echo '<h3>Default Access</h3>';
    echo 'ACID: '.$acs->getValue('auth_access_acid');

?>