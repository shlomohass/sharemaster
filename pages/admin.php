<?php

Trace::add_step(__FILE__,"Loading Page: admin");


/****************************** Get more classes ***********************************/




/********************* Set additional head CSS import ****************************/
Trace::add_step(__FILE__,"Define css libs for head section");
$Page->include_css(array(
    GPATH_LIB_STYLE."font-awesome.min.css",
    GPATH_LIB_STYLE."bootstrap.min.css",
    GPATH_LIB_STYLE."dropzone.css"
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
Trace::add_step(__FILE__,"Set admin page data");
$Page->title = Lang::P("gen_title_prefix",false).Lang::P("home_title",false);
$Page->description = Lang::P("home_desc",false);
$Page->keywords = Lang::P("home_keys",false);



/****************************** Set Page Variables ***********************************/
$_view = $Page->Func->synth($_GET, array("t"))["t"];
$Page->variable("load-view", (!empty($_view) && ($_view === "dash" || $_view === "users" || $_view === "share" || $_view === "stats" || $_view === "maintain" || $_view === "frontend" || $_view === "adver")) ? $_view : "dash" );


/****************************** Load  Page Data ***********************************/
//$Page->variable("all-plans", $Page::$conn->get("settingsplan"));



/***************  Set additional end body JS import and Conditional JS  *******************/
Trace::add_step(__FILE__,"Define conditional js libs for end body section");
$Page->include_js(array(
    GPATH_LIB_JS."bootstrap.min.js",
    GPATH_LIB_JS."dropzone.js",
    GPATH_LIB_JS."app.js"
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
    
<section class='admin-top-bar'>
    Top
</section>
<section class='container-fluid'>
    <div class="row">
        <div class='admin-left-bar col-fixed-240'>
            <?php 
                $tabs = array(
                    "dash"      => ($Page->variable("load-view") === "dash")?"nav-active":"",
                    "users"     => ($Page->variable("load-view") === "users")?"nav-active":"",
                    "share"     => ($Page->variable("load-view") === "share")?"nav-active":"",
                    "stats"     => ($Page->variable("load-view") === "stats")?"nav-active":"",
                    "maintain"  => ($Page->variable("load-view") === "maintain")?"nav-active":"",
                    "frontend"  => ($Page->variable("load-view") === "frontend")?"nav-active":"",
                    "adver"     => ($Page->variable("load-view") === "adver")?"nav-active":""
                );
            ?>
            <ul class="nav-but">
                <li class='<?php echo $tabs["dash"]; ?>'>
                    <a href="?page=admin&t=dash">
                        <span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span>
                        Dashbord
                    </a>
                </li>
                <li class='<?php echo $tabs["users"]; ?>'>
                    <a href="?page=admin&t=users">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                        Users
                    </a>
                </li>
                <li class='<?php echo $tabs["share"]; ?>'>
                    <a href="?page=admin&t=share">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                        Sharing
                    </a>
                </li>
                <li class='<?php echo $tabs["stats"]; ?>'>
                    <a href="?page=admin&t=stats">
                        <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
                        Statistics
                    </a>
                </li>
                <li class='<?php echo $tabs["maintain"]; ?>'>
                    <a href="?page=admin&t=maintain">
                        <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
                        Maintain
                    </a>
                </li>
                <li class='<?php echo $tabs["frontend"]; ?>'>
                    <a href="?page=admin&t=frontend">
                        <span class="glyphicon glyphicon-modal-window" aria-hidden="true"></span>
                        Frontend
                    </a>
                </li>
                <li class='<?php echo $tabs["adver"]; ?>'>
                    <a href="?page=admin&t=adver">
                        <span class="glyphicon glyphicon-gift" aria-hidden="true"></span>
                        Advertise
                    </a>
                </li>
            </ul>
        </div>
        <div class='admin-main-bar col-md-12 col-offset-240'>
            <?php
                include_once PATH_PAGES."admin".DS."page-".$Page->variable("load-view").".php";
            ?>
        </div>
    </div>
</section>

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