<?php
if (!isset($Page)) { die("No...."); }

//Parse Version:
$use_version = (!empty($Page->version))?"?version=".$Page->version:"";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="description" content="<?php echo $Page->description; ?>" />
    <meta name="keywords" content="<?php echo $Page->keywords; ?>" />
    <meta name="author" content="<?php echo $Page->author; ?>" />

    <link rel="shortcut icon" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>apple-touch-icon.png" />
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>apple-touch-icon-152x152.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $Page::$conf["general"]["fav_url"]; ?>apple-touch-icon-180x180.png" />

    <title><?php echo $Page->title; ?></title>
    
    <!-- CSS head extend -->
    <?php
        //Load css resources:
        foreach ($Page->get_css() as $sheet) {
            echo "<link rel='stylesheet' href='".$Page::$conf["general"]["site_base_url"].$sheet.$use_version."' />";
        }
        //Load base style sheet if its set:
        if (!empty($Page->template)) {
            echo "<!-- Theme CSS-->";
            echo "<link rel='stylesheet' href='".$Page::$conf["general"]["site_base_url"].GPATH_LIB_STYLE.$Page->template.$use_version."' />";
        }
    ?>
    <!-- JS Script head extend -->
    <?php
        //Load js lang hooks:
        if (!empty($Page->get_js_lang())) {
            echo "<script>window['lang'] = { ";
            echo implode(",", array_filter($Page->get_js_lang()));
            echo " }; </script>";
        }
        //Load js libs to head:
        foreach ($Page->get_js() as $script) {
            echo "<script src='".$Page::$conf["general"]["site_base_url"].$script.$use_version."' type='application/javascript'></script>";
        }
    ?>
</head>
<body>
<?php
    //Tokenize page:
    if ($Page->token !== false) {
        echo "<input type='hidden' name='token' id='pagetoken' value='".$Page->token."' />";
    } else {
        echo "<input type='hidden' name='token' id='pagetoken' value='' />";
    }
    





