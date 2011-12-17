<?php

$config = array();

$config['db_hostname'] = '127.0.0.1'; // Using localhost here might cause exception
$config['db_user'] = 'root';
$config['db_password'] = '';
$config['db_database'] = 's1';

// Upload directory should be non-public and needs chmod 777
$config['upload_dir'] = './uploads/'; // With trailing slash
$config['unknown_file_extension'] = '.unknown'; // With dot

?>

