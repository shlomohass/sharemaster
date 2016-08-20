<?php

Trace::add_step(__FILE__,"Loading Page: home");


/****************************** Get more classes ***********************************/




/********************* Set additional head CSS import ****************************/
Trace::add_step(__FILE__,"Define css libs for head section");
$Page->include_css(array(
    //GPATH_LIB_JS."App/scrollbar/perfect-scrollbar.css"
));
    

/********************* Set additional head JS import ********************/
Trace::add_step(__FILE__,"Define js libs for head section");
$Page->include_js(array(
    GPATH_LIB_JS."jquery-1.12.3.min.js"
));



/****************************** Include JS Lang hooks ***********************************/
Trace::add_step(__FILE__,"Load page js lang hooks");
$Page->set_js_lang(Lang::lang_hook_js("script-frontend"));



/****************************** Set Page Meta ***********************************/
Trace::add_step(__FILE__,"Set home page data");
$Page->title = Lang::P("gen_title_prefix",false).Lang::P("home_title",false);
$Page->description = Lang::P("home_desc",false);
$Page->keywords = Lang::P("home_keys",false);



/****************************** Set Page Variables ***********************************/
$_view = $Page->Func->synth($_GET, array("t"))["t"];
$Page->variable("load-view", (!empty($_view) && ($_view === "debugger" || $_view === "process" || $_view === "settings" || $_view === "server")) ? $_view : "process" );



/****************************** Load  Page Data ***********************************/
//$Page->variable("all-plans", $Page::$conn->get("settingsplan"));



/***************  Set additional end body JS import and Conditional JS  *******************/
Trace::add_step(__FILE__,"Define conditional js libs for end body section");
$Page->include_js(array(
    GPATH_LIB_JS."App/scrollbar/jquery.mousewheel.js",
    GPATH_LIB_JS."App/scrollbar/perfect-scrollbar.js",
    GPATH_LIB_JS."App/custom.js",
    GPATH_LIB_JS."App/app.js"
), false);
   


/****************************** Set page header ***********************************/
Trace::add_step(__FILE__,"Load page header");
require_once PATH_STRUCT.'head.php';



/****************************** Page Debugger Output ***********************************/
//Trace::reg_var("onload view", $Page->variable("load-view"));
//Trace::reg_var("all plans", $Page->variable("all-plans"));
//Trace::reg_var("all encodings", $Page->variable("all-encodings"));
Trace::add_step(__FILE__,"Load page HTML");


?>

shlomi hassid!


<!-- START Footer loader -->
<?php 
Trace::add_step(__FILE__,"Load page footer");
//require_once PATH_STRUCT.'modals.php'; 
require_once PATH_STRUCT.'foot.php'; 
?> 
<!-- END Footer loader -->

<script>

</script>
</body>
</html>