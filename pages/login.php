<?php
Trace::add_step(__FILE__,"Loading Page: login");


/****************************** Get more classes ***********************************/



/********************* Set additional head CSS import ****************************/
Trace::add_step(__FILE__,"Define css libs for head section");
$Page->include_css(array(

));



/********************* Set additional head JS import ********************/
Trace::add_step(__FILE__,"Define js libs for head section");
$Page->include_js(array(

));



/****************************** Include JS Lang hooks ***********************************/
Trace::add_step(__FILE__,"Load page js lang hooks");
$Page->set_js_lang(Lang::lang_hook_js("script-login"));



/****************************** Set Page Meta ***********************************/
Trace::add_step(__FILE__,"Set login page data");
$Page->title = Lang::P("gen_title_prefix",false).Lang::P("login_title",false);
$Page->description = Lang::P("login_desc",false);
$Page->keywords = Lang::P("login_keys",false);



/***************  Set additional end body JS import and Conditional JS  *******************/
Trace::add_step(__FILE__,"Define conditional js libs for end body section");
$Page->include_js(array(
    GPATH_LIB_JS."jQuery/jquery-1.11.1.min.js"
), false);



/****************************** Set page header ***********************************/
Trace::add_step(__FILE__,"Load page header");
require_once PATH_STRUCT."head.php";



/****************************** Page Debugger Output ***********************************/
Trace::add_step(__FILE__,"Load page HTML");

?>

<form method="POST">
    <nav class="navbar">
        <div class="logo">
            <img src="<?php echo $Page::$conf["general"]["fav_url"]; ?>logo.png" alt="logo" />
            <em><?php echo "v".$Page::$conf["general"]["app_version"]; ?></em>
        </div>
        <ul>
            <li><input type="text" name="username" class="field" placeholder="Username" required="required" /></li>
            <li><input type="password" name="password" class="field" placeholder="Password" required="required" /></li>
            <li><button type="submit" class="btn btn-primary bg-orange px3">Login</button></li>
        </ul>
    </nav>
</form>

<!-- START Footer loader -->
<?php 
Trace::add_step(__FILE__,"Load page footer");
require_once PATH_STRUCT.'foot.php'; 
?> 
<!-- END Footer loader -->

<script>

</script>
</body>
</html>