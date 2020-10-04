<?php 

    # Planck Auth Version 1.0.0

    namespace Auth;
    include_once '../includes/load.php';

    $responce = array();

    if ( isset( $_POST['request'] ) && isset( $_POST['apitoken'] ) && isset( $_POST['apikey'] ) && isset ( $_POST['requestid'] ) ) {
            
        $request = $_POST['request'];
        $requestid = $_POST['requestid']; 
        $apitoken = $_POST['apitoken'];
        $apikey = $_POST['apikey'];

        $auth_data->setValue('auth_token', $apitoken);
        $auth_data->setValue('auth_key', $apikey);
        $auth_data->setValue('auth_request_id', $request);

        $api_responce = $auth_fun->checkApi($auth_data);

        if ( $api_responce->getValue('status') != FALSE) {
            $servicesid = $api_responce->getValue('auth_api_sid');
            $d_access = "SqCB7d1rwY";

            switch ( $request ) {
                case 'login':
                    authLogin($servicesid, $requestid);
                    break;
                case 'register':
                    authRegister($servicesid, $requestid, $d_access);
                    break;
                default:
                    authDefault();
                    break;
            }
        }
    } else {
        $responce['code'] = 400;
        $responce['status'] = 'failed';
        $responce['message'] = 'Unknown request';
        echo authResponce($responce);
    }

    function authLogin($sid, $rid) {

        $dbc = $GLOBALS['dbc'];
        $auth_data = $GLOBALS['auth_data'];
        $auth_common = $GLOBALS['auth_common'];
        $auth_fun = $GLOBALS['auth_fun'];

        $responce['request_id'] = $rid;
        $responce['request'] = 'login';

        if ( isset( $_POST['auth_username'] ) && isset ( $_POST['auth_password'] ) ) {

            $auth_data->setValue('auth_user', $_POST['auth_username']);
            $auth_data->setValue('auth_pass', $_POST['auth_password']);
            $auth_data->setValue('auth_sid', $sid);

            $login = $auth_fun->apiUserLogin($auth_data);

            if ( $login != FALSE ) {
                $responce['code'] = 200;
                $responce['status'] = 'success';
                $responce['action'] = NULL;
                $responce['message'] = 'User login success';
                $responce['login_id'] = $login->getValue('auth_login_ulid');
                $responce['login_token'] = $login->getValue('auth_login_token');
                $responce['login_session'] = $login->getValue('auth_login_session');
                $responce['user_id'] = $login->getValue('auth_uid');
                $responce['service_id'] = $sid;
                $responce['username'] = $_POST['auth_username'];
            } else {
                $responce['code'] = 401;
                $responce['status'] = 'failed';
                $responce['message'] = 'User login failed';
            }

        } else {
            $responce['code'] = 400;
            $responce['status'] = 'failed';
            $responce['message'] = 'Invalid login data';
        }

        echo authResponce($responce);

    }

    function authRegister($sid, $rid, $dac) {

        $dbc = $GLOBALS['dbc'];
        $auth_data = $GLOBALS['auth_data'];
        $auth_common = $GLOBALS['auth_common'];
        $auth_fun = $GLOBALS['auth_fun'];

        $responce['request_id'] = $rid;
        $responce['request'] = 'register';

        if ( isset( $_POST['auth_username'] ) && isset ( $_POST['auth_password'] ) && isset ( $_POST['auth_email'] ) && $_POST['auth_username'] != NULL) {

            $auth_data->setValue('auth_user', $_POST['auth_username']);
            $auth_data->setValue('auth_pass', $_POST['auth_password']);
            $auth_data->setValue('auth_email', $_POST['auth_email']);
            $auth_data->setValue('auth_access_sid', $dac);

            $reg_responce = $auth_fun->registerNewUser($auth_data);

            if ( $reg_responce->getValue('status') != FALSE) {
                
                $uid = $reg_responce->getValue('auth_uid');

                $responce['code'] = 200;
                $responce['status'] = 'success';
                $responce['action'] = NULL;
                $responce['message'] = $reg_responce->getValue('message');
                $responce['user_id'] = $uid;

            } else {
                $responce['code'] = 401;
                $responce['status'] = 'failed';
                $responce['message'] = $reg_responce->getValue('message');
            }
            
        } else {
            $responce['code'] = 400;
            $responce['status'] = 'failed';
            $responce['message'] = $reg_responce->getValue('message');
        }

        echo authResponce($responce);

    }

    function authResponce($responce_data) {
        header('Content-Type: application/json');
        return json_encode($responce_data, JSON_UNESCAPED_SLASHES);
    }
?> 