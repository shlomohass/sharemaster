<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends Basic {
    
    /** Class Properties
     *
     */
    public $user_info;
    public $user_ip;
    public $user_loged;
    public $sess_save;
    
    public function __construct( $conf ) {
        parent::__construct( $conf );
        Trace::add_trace('construct class',__METHOD__);
        $this->user_ip = $this->get_ip_address();
        $this->is_loged();
    }
    
    /** is user visiter loged?
     * 
     * @return boolean
     */
    public function is_loged() {
        Trace::add_trace('user logged check',__METHOD__);
        $grab = filter_input_array(INPUT_COOKIE,array(
                'login' => array(
                    'filter' => FILTER_UNSAFE_RAW,
                    'flags' => FILTER_REQUIRE_ARRAY 
                )
        ));
        $cookies = (!is_null($grab) && is_array($grab) && isset($grab['login']))?
                    $this->Func->synth($grab['login'], array('sess','uname')):
                    false;
        $check = ($cookies)?
                 self::$conn->get_row(
                    "SELECT * FROM `users` WHERE "
                    . "`session`='".self::$conn->filter($cookies['sess'])."' "
                    . "AND `username`='".self::$conn->filter($cookies['uname'])."'"):
                 false;
        if ($check && !empty($check) && $check["sip"] === $this->user_ip) {
            Trace::add_trace('loged check finish',__METHOD__,array( 'check' => 'true' ));
            $this->user_loged = true;
            $this->sess_save = $cookies;
            $this->user_info = $check['id'];
            return $cookies;
        }
        
        Trace::add_trace('loged check finish',__METHOD__,array( 'check' => 'false' ));
        $this->Func->delete_cookie('login[sess]', self::$conf['user_account']['cookie_expire']);
        $this->Func->delete_cookie('login[uname]', self::$conf['user_account']['cookie_expire']);
        $this->Func->delete_cookie('login', self::$conf['user_account']['cookie_expire']);
        
        $this->sess_save = null;
        $this->user_loged = false;
        $this->user_info = null;
        return false;
    }
    
    /** Login procedure:
     * 
     * @param string $_upass
     * @param string $_uname
     * @return boolean|string
     * 
     */
    public function login($_upass = false,$_uname = false) {
        Trace::add_trace('user login',__METHOD__,array( 
            'upass' => $_upass,
            'uname' => $_uname
        ));
        $sess = array('sess'=>'','uname'=>'');
        $upass = md5(self::$conn->filter($_upass).TOKEN_SALT);
        $uname = self::$conn->filter($_uname);
        $check = self::$conn->get_row("SELECT * FROM `users` WHERE `password`='".$upass."' AND `username`='".$uname."'");
        if (empty($check)) { 
            Trace::add_trace('user new login - bad login',__METHOD__);
            return false; 
        }
        if ( $check['status'] == 0 ) {
            Trace::add_trace('user account login - not activated',__METHOD__);
            return 'inactive'; 
        }
        Trace::add_trace('user new login - good login',__METHOD__);
        $sess['sess']  = $this->generate_login_session($upass);
        $sess['uname'] = $uname;
        if (!self::$conn->update(
                'users', 
                array('session' => self::$conn->filter($sess['sess']), 'sip' => self::$conn->filter($this->user_ip)), 
                array(array('id',"=",self::$conn->filter($check['id'])))
        )) {
            Trace::add_trace('user new login - cant create login, DB update',__METHOD__);
            return false; 
        }
        $res1 = $this->Func->create_cookie('login[sess]', $sess['sess'], self::$conf['user_account']['cookie_expire']);
        $res2 = $this->Func->create_cookie('login[uname]', $sess['uname'], self::$conf['user_account']['cookie_expire']);    
        $this->sess_save = $sess;
        $this->user_loged = true;
        $this->user_info = $check['id'];
        $this->update_last_seen();
        return ($res1 && $res2)?true:false;
    }
    
    /** Log out user 
     * 
     */
    public function force_logout() {
        Trace::add_trace('user logout request - start',__METHOD__);
        $temp_sess = $this->sess_save;
        $this->Func->delete_cookie('login[sess]', self::$conf['user_account']['cookie_expire']);
        $this->Func->delete_cookie('login[uname]', self::$conf['user_account']['cookie_expire']);
        $this->Func->delete_cookie('login', self::$conf['user_account']['cookie_expire']);
        $this->user_info = null;
        $this->sess_save = null;
        $this->user_loged = false;
        if (
            !is_null($temp_sess) 
            && isset($temp_sess['sess'])
            && isset($temp_sess['uname'])
        ) {
            self::$conn->update(
                'users', 
                array("session" => "", "sip" => "", "last_seen" => "NOW()"), 
                array(array('username', "=", self::$conn->filter($temp_sess['uname'])))
            );
        }
        Trace::add_trace('user logout request - end',__METHOD__);
    }
    
    /** Update user row for new login / seen timestamp
     * 
     */
    private function update_last_seen() {
        Trace::add_trace('update user last seen',__METHOD__);
        if (!is_array($this->sess_save)) { return false; }
        self::$conn->update(
            'users', 
            '`last_seen` = NOW(), `seen` = `seen`+1', 
            array(
                array('username',"=",self::$conn->filter($this->sess_save['uname'])),
                array('session',"=",self::$conn->filter($this->sess_save['sess']))
            )  
        );
    }
    
    /** Generate login session token
     * 
     * @param string $pass
     * @return string
     */
    public function generate_login_session($pass) {
        Trace::add_trace('generate login session request',__METHOD__);
        return md5($pass.date("Y-m-d H:i:s"));
    }
    
    /** Try to get user IP:
     * 
     * @return string user ip address.
     * 
     */
    public function get_ip_address() {
        // check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        // check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check if multiple ips exist in var
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($iplist as $ip) {
                    if ($this->validate_ip($ip)) { return $ip; }
                }
            } else {
                if ($this->validate_ip($_SERVER['HTTP_X_FORWARDED_FOR'])) { return $_SERVER['HTTP_X_FORWARDED_FOR']; }
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validate_ip($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        // return unreliable ip since all else failed
        return $_SERVER['REMOTE_ADDR'];
    }

    /** Ensures an ip address is both a valid IP and does not fall within a private network range.
     * 
     * @param string $_ip ip address
     * @return boolen
     */
    private function validate_ip($_ip) {
        if (strtolower($_ip) === 'unknown') {
            return false;
        }
        // generate ipv4 network address
        $ip = ip2long($ip);
        // if the ip is set and not equivalent to 255.255.255.255
        if ($ip !== false && $ip !== -1) {
            // make sure to get unsigned long representation of ip
            // due to discrepancies between 32 and 64 bit OSes and
            // signed numbers (ints default to signed in PHP)
            $ip = sprintf('%u', $ip);
            // do private network range checking
            if ($ip >= 0 && $ip <= 50331647) { return false; }
            if ($ip >= 167772160 && $ip <= 184549375) { return false; }
            if ($ip >= 2130706432 && $ip <= 2147483647) { return false; }
            if ($ip >= 2851995648 && $ip <= 2852061183) { return false; }
            if ($ip >= 2886729728 && $ip <= 2887778303) { return false; }
            if ($ip >= 3221225984 && $ip <= 3221226239) { return false; }
            if ($ip >= 3232235520 && $ip <= 3232301055) { return false; }
            if ($ip >= 4294967040) { return false; }
        }
        return true;
    }
}