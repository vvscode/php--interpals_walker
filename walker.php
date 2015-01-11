<?php
require_once('vendor/autoload.php');
require_once('inc/interpalsClient.php');
require_once('conf.php');

$ic = new interpals\InterpalsClient(INTERPALS_LOGIN, INTERPALS_PASSWORD);

if(!$ic->isLoggedIn()){
    $ic->login();
}
