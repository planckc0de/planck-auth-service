<?php 
    namespace Auth;
    session_start();

    include_once 'constant.php';
    
    include_once DB_CLASS;
    include_once DATA_CLASS;
    include_once COMMON_CLASS;
    include_once FUN_CLASS;
    
    use Auth\Database as DB;
    use Auth\Data as DATA;
    use Auth\Common as COMMON;

    $dbc = new DB;
    $auth_data = new DATA;
    $auth_common = new COMMON;

    use Auth\Functions as FUN;
    $auth_fun = new FUN;

?>