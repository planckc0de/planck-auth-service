<?php 
$post = [
    'request' => 'register',
    'requestid' => 'treq2',
    'apitoken' => 'B8EDMSKRMUPSTOEB',
    'apikey' => 'kP4k6c3ah07YP2Qq9ofksSQ6x8Z8gZzK0760vHjGRdjLAZ7NxTIw7SOvjJLbiBMq',
    'auth_username' => 'test5',
    'auth_password' => '1234',
    'auth_email'   => 'test5@test.com',
];

$ch = curl_init('http://localhost/localhost/planckstud.io/auth/v1/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

// execute!
$response = curl_exec($ch);
curl_close($ch);
echo $response;
?>