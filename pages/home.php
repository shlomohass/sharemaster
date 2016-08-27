<?php

Trace::add_step(__FILE__,"Loading Page: home");


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

    <section class="container-fluid main-nav">
      <div class="row">
          <a href="#" class='pull-left '>
            <img src='dummy/200x40' class="logo-nav"/>
          </a>
          <div href="#" class='hamburger-nav pull-right'>
            <span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
          </div>
        <div class="col-xs-12 hide-nav-xs col-sm-8 pull-right noselect">
          <div class="row">
            <div class="col-xs-12 nopadding-xs">
              <ul class="list-reset pull-right add-nav">
                <li>
                  <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                  <span>Home</span>
                </li>
                <li>
                  <span class="glyphicon glyphicon-education" aria-hidden="true"></span>
                  <span>About</span>
                </li>
                <li class="add-collapse">
                  <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                  <span>Dashboard</span>
                  <ul class="list-reset add-menu">
                    <li>Profile</li>
                    <li>Archive</li>
                    <li>Contacts</li>
                  </ul>
                </li>
                <li class="add-collapse">
                  <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span>
                  <span>Sign In / Up</span>
                  <ul class="list-reset add-menu">
                    <li>Sign In</li>
                    <li>Sign Up</li>
                    <li>Terms</li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="container main-body">
      <!-- Nav tabs -->
      <ul id="page-tabs" class="nav nav-tabs otabs" role="tablist">
        <li role="presentation" class="active">
          <a href="#file-deliv" aria-controls="file-deliv" role="tab" data-toggle="tab">
            <span class="glyphicon glyphicon-send" aria-hidden="true"></span>
            File Delivery
          </a>
        </li>
        <li role="presentation">
          <a href="#share-link" aria-controls="share-link" role="tab" data-toggle="tab">
            <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
            Share Link
          </a>
        </li>
        <li role="presentation">
          <a href="#cron-share" aria-controls="cron-share" role="tab" data-toggle="tab">
            <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
            Cron Share
          </a>
        </li>
        <li role="presentation">
          <a href="#my-cloud" aria-controls="my-cloud" role="tab" data-toggle="tab">
            <span class="glyphicon glyphicon-cloud" aria-hidden="true"></span>
            My Cloud
          </a>
        </li>
        <li role="presentation">
          <a href="#my-stats" aria-controls="my-stats" role="tab" data-toggle="tab">
            <span class="glyphicon glyphicon-stats" aria-hidden="true"></span>
            My Stats
          </a>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content otabcon">
        <div role="tabpanel" class="tab-pane active" id="file-deliv">
            <div class="container-fluid otab-info-header">
                testing header
            </div>
            <div class="container-fluid otab-container">
              <div class="row otab-adver-area">
                  adver area
              </div>
              <div class="row otab-main-area">
                <div class="col-md-4 otab-left-col">
                  <div class="row">
                    <div class="col-sm-12">
                      <h4 class="sub-section-header">Recipients:</h4>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="input-group">
                        <input type="text" class="form-control" placeholder="Add Email Address">
                        <span class="input-group-btn">
                          <button class="btn btn-default" type="button">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                          </button>
                        </span>
                      </div><!-- /input-group -->
                    </div>
                  </div>
                  <div class="spacer-10"></div>
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="input-group">
                        <select type="text" class="form-control" placeholder="Add Email Address">
                          <option value='-1'>Mailing Lists</option>
                        </select>
                        <span class="input-group-btn">
                          <button class="btn btn-default" type="button">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                          </button>
                        </span>
                      </div><!-- /input-group -->
                    </div>
                  </div>
                  <div class="border-spacer-22"></div>
                  <div class="row mailing-list-container">
                    <div class="col-sm-12">
                      <ul class="list-reset mailing-list">
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                        <li>
                          shlomihassid@gmail.com
                          <span class="glyphicon glyphicon-remove pull-right" aria-hidden="true"></span>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="border-spacer-22"></div>
                  <div class="row">
                    <span class="save-as-list pull-right">save as a mailing list</span>
                  </div>
                </div>
                <div class="col-md-8 otab-right-col">
                  <div class="row">
                    <div class="col-sm-12">
                      <h4 class="sub-section-header">From:</h4>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <input type="email" class="form-control" name="deliv-sender-email" placeholder="Sender Email" />
                      </div>
                    </div>
                    <div class="col-sm-4">
                      <div class="form-group">
                        <input type="text" class="form-control" name="deliv-sender-name" placeholder="Full name" />
                      </div>
                    </div>
                    <div class="col-sm-2">
                      <button type="button" class="btn btn-purp">From Me</button>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                      <h4 class="sub-section-header">Files:</h4>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-12">
                        <form id="shareMasterUpload" action="index.php" class="dropzone">
                            <input type="hidden" name="req" value="api" />
                            <input type="hidden" name="token" value="<?php echo $Page->token; ?>" />
                            <input type="hidden" name="type" value="uploadfile" />
                        </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="share-link">
            <div class="container-fluid otab-info-header">
                testing header
            </div>
            <div class="container-fluid otab-container">
              <div class="row otab-adver-area">
                  adver area
              </div>
              <div class="row otab-main-area">
                  Single Col
              </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="cron-share">
            <div class="container-fluid otab-info-header">
                testing header
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="my-cloud">
            <div class="container-fluid otab-info-header">
                testing header
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="my-stats">
            <div class="container-fluid otab-info-header">
                testing header
            </div>
        </div>
      </div>
    </section>

    <section class="container-fluid main-footer"> 
    © Copyright 2016 SM Projects | Powered by SMProj              
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