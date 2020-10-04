<?php 
    namespace Auth;

    defineConstant( 'ABSPATH', dirname( dirname( __FILE__ ) ));
    defineConstant( 'INCPATH', ABSPATH . '/includes/' );
    defineConstant( 'CLSPATH', INCPATH . 'classes/' );
    defineConstant( 'ASTPATH', ABSPATH . '/assets/' );
    defineConstant( 'TMPPATH', ABSPATH . '/templates/' );
    
    defineConstant( 'DB_CLASS', CLSPATH . 'database.class.php');
    defineConstant( 'DATA_CLASS', CLSPATH . 'data.class.php');
    defineConstant( 'COMMON_CLASS', CLSPATH . 'common.class.php');
    defineConstant( 'FUN_CLASS', CLSPATH . 'function.class.php');
    
    defineConstant( 'TABLE_PREFIX', 'auth_' );

    global $dbc;
    global $auth_data;
    global $auth_common;
    global $auth_fun;

    function defineConstant( $path, $value ) {
        if ( !defined($path) ) {
            define( $path, $value );
        }
    }

?>