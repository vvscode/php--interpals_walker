<?php
namespace interpals;

use Curl\Curl;

class InterpalsClient
{
    /**
     * @var Curl
     */
    private $curl;

    private $pass;

    private $login;

    public function __construct($login, $pass)
    {
        $this->curl = new cURL();
        $this->curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
        $this->curl->setopt(CURLOPT_FOLLOWLOCATION, TRUE);
        $this->curl->setopt(CURLOPT_COOKIEFILE, session_save_path().'/'.$this->login.'.cookie');

        $this->login = $login;
        $this->pass = $pass;
    }

    public function isLoggedIn($reload = false)
    {
        if($reload OR empty($this->curl->response)) {
            $this->visitPage('http://www.interpals.net/index.php');
        }
        $isLoggedIn = substr_count($this->curl->response, '<input type="text" id="topLoginEmail" name="login"') == 0;
        $this->debugMsg(sprintf('Check is logged in: %b', $isLoggedIn));
        return (bool)$isLoggedIn;
    }

    public function login()
    {
        $this->curl->setReferrer('http://www.interpals.net');
        $this->curl->post('http://www.interpals.net/login.php', [
            'action' => 'login',
            'login' => $this->login,
            'auto_login'=> 1,
            'password' => $this->pass
        ]);

        $this->debugMsg(sprintf('Try login with result: %b', $this->isLoggedIn()));
        return $this;
    }

    public function getUsersList(array $filter = [])
    {
        $url = 'https://www.interpals.net/app/online?age1=18&age2=64&sort=last_login&order=desc&offset=-1';
        $this->visitPage($url);
        preg_match_all('%<a profile.+?href=\"/(.+?)\"%s', $this->curl->response, $arr, PREG_PATTERN_ORDER);
        $users = $arr[1];

        array_walk($users, function(&$url) {
            $url = 'http://www.interpals.net/'.$url;
        });
        return $users;
        // return array_slice($users, 0 , 100);
    }

    public function visitPage($url)
    {
        $this->debugMsg('Visit '.$url);
        $this->curl->get($url);
    }

    private function debugMsg($str)
    {
        echo '[DEBUG]'.$str."\n";
    }
}
