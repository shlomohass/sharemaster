<?php 

ini_set('max_execution_time', 0);
define('PREVENT_OUTPUT', true );  

require_once 'conf.php';
require_once PATH_CLASSES.'Trace.class.php';
require_once PATH_CLASSES.'Func.class.php';
require_once PATH_CLASSES.'DB.class.php';
require_once PATH_CLASSES.'Basic.class.php';
require_once PATH_CLASSES.'Page.class.php';
require_once PATH_CLASSES.'Lang.class.php';
require_once PATH_CLASSES.'User.class.php';

/***************** Load Page (DB, Func, conf, page, user) *********************/

Trace::add_step(__FILE__,"Create DB object");
$Page = new Page( $conf );
$User = new User( $conf );

//var_dump($User->user_loged);

/************************* Load User Pref Lang ********************************/

Trace::add_step(__FILE__, "Load Language Dictionary");
if (isset($Page::$conf["general"]["uselang"]) && is_string($Page::$conf["general"]["uselang"])) {
    require_once PATH_LANG.$Page::$conf["general"]["uselang"].'.php';
}
Lang::load($Lang);

/*********************** Login | Logout  Request? *****************************/

Trace::add_step(__FILE__,"Login | Logout request ?");

$login = $Page->Func->synth($_POST, array("username", "password"));
if (!$User->user_loged && !empty($login["password"]) && !empty($login["username"])) {
    $User->login($login["password"], $login["username"]);
} elseif ($User->user_loged && isset($_GET["logout"])) {
    $User->force_logout();
    header('Location: '.$_SERVER['PHP_SELF']);
}

/************************* Page Target and token ******************************/

Trace::add_step(__FILE__,"Set Target and Page Token");
$Page->target();
if ($User->user_loged) {
    $Page->token = $User->sess_save["sess"];
}
Trace::add_trace("Page parsed target",__FILE__, array("target" => $Page->target));
Trace::add_trace("Page token",__FILE__, array("token" => $Page->token));

/**************************** Secure Request  *********************************/

Trace::add_step(__FILE__,"Secure Request Handler");
$request = $Page->Func->synth($_POST, array("req", "token"));
if (
        $User->user_loged 
        && $request["req"] !== "" 
        && $request["token"] === $User->sess_save["sess"] 
    ) {
    
    Trace::add_trace("Secure request detected.",__FILE__, $request);
    switch($request["req"]) {
        case "api":
            Trace::add_trace("Loading Api Request",__FILE__);
            $Page->secure = true;
            $Page->target = "api";
            break;
        default:
            die("E:01");
    }
}

/****************************** Page Loader ***********************************/

switch ($Page->target) {
    case "api":
        Trace::add_step(__FILE__,"Load secure api");
        if ($Page->secure) {
            
            //Include more classes:
            include_once PATH_CLASSES."Api.class.php";
            include_once PATH_CLASSES."Operation.class.php";
            
            //Load Api:
            include_once PATH_PAGES."api.php";
            
        } else {
            
            //Echo general Error Code:
            die("E:01");
        }
    break;
    case "admin":
        Trace::add_step(__FILE__,"Load admin page");
        if ($User->user_loged) {
            $Page->template = "style-admin.css";
            include_once PATH_PAGES."admin.php";
        } else {
            $Page->template = "style-login.css";
            include_once PATH_PAGES."login.php";
        }
    break;
    case "home":
    default:
        Trace::add_step(__FILE__,"Load page home");
        $Page->template = "style-home.css";
        include_once PATH_PAGES."home.php";
}

/**************************** Debuger Expose **********************************/

//Expose Trace
Trace::expose_trace();