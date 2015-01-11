<?php
require_once('vendor/autoload.php');
require_once('inc/interpalsClient.php');
require_once('conf.php');

$ic = new interpals\InterpalsClient(INTERPALS_LOGIN, INTERPALS_PASSWORD);

if(!$ic->isLoggedIn()){
    $ic->login();
}

$usersList = $ic->getUsersList();
array_walk($usersList, function($userLink) {
    global $ic;
    sleep(rand(0,3));
    $ic->visitPage($userLink);
});

echo "\n-= DONE =-\n";
