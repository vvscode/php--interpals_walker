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
            $this->curl->get('http://www.interpals.net/index.php');
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

    private function debugMsg($str)
    {
        echo '[DEBUG]'.$str."\n";
    }
}
