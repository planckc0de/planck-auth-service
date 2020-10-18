<?php 
$post = [
    'request' => 'logout',
    'requestid' => 'treq2',
    'apitoken' => 'NODTOS8C98VEDVIJ',
    'apikey' => 'WNSdlsr0OZPzRlgl9i4YjTfhmE5vxQDceXMiPmUds0pcn4GAZRLksexIZ2xEbUE3',
    'auth_username' => 'test',
    'auth_password' => '1234',
    'auth_email'   => 'test@planckstudio.in',
    'auth_lid' => 'L0DMVtZUyH',
    'default_service_id' => 'Sljx0VVIVM'
];

$ch = curl_init('https://api.planckstudio.in/auth/v1/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

// execute!
$response = curl_exec($ch);
curl_close($ch);
echo $response;
?>