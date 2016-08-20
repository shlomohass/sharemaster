<?php
if (!isset($conf)) { die("E:01"); }

//API:
$Api = new api( $conf );

//Needed Arrays:
$inputs = $Api->Func->synth($_POST, array('type'));

//Building response:
$success = "general";
$results = false;

//Procedures:
if ( $inputs['type'] !== '' ) {
    
    switch (strtolower($inputs['type'])) {
        
        //Get all settings plan:
        case "getplans" :  
            $Extract = new Extract();
            $get = $Api::$conn->get("settingsplan");
            if ($get !== false) {
                foreach ($get as $key => $row) {
                    $get[$key]['plan_name'] = ucfirst($row['plan_name']);
                    $get[$key]['crawler_type'] = ucfirst($row['crawler_type']);
                    $get[$key]['argu'] = $Extract->get_console_arguments(
                        false, 
                        ($row["ena_outfile"])?true:false, 
                        ($row["ena_debugger"])?true:false, 
                        (is_numeric($row["timeout"]))?$row["timeout"]:false, 
                        ($row["dis_css"])?true:false, 
                        ($row["dis_images"])?true:false, 
                        ($row["ena_stamp"])?true:false, 
                        ($row["ena_grout"])?true:false, 
                        (!empty($row["out_folder"]))?$row["out_folder"]:false
                    );
                }
                $results = array(
                    "rows" => $get
                );
                $success = "with-results";
            } else {
                $Api->error("query");
            }
        break;
        
        //Get all datacenter list:
        case "getdatasites":
            $get = $Api::$conn->get("datacenter_queries");
            if ($get !== false) {
                $results = array( "rows" => $get );
                $success = "with-results";
            } else {
                $Api->error("query");
            }
        break;
        
        //Create Settings plan:
        case "createplan" : 
            
            //Synth needed:
            $get = $Api->Func->synth($_POST, array('dis_images','dis_css','ena_debugger','ena_outfile','ena_grout','ena_stamp','out_folder','timeout','loading_timeout','server','use_encoding','crawler_type','plan_name'),false);
            
            //Validation:
            if (
                    $get['plan_name']  === ''
                ||  $get['crawler_type'] === ''
                ||  $get['server'] === ''
                ||  $get['out_folder'] === ''
                ||  !is_numeric($get['timeout'])
                ||  !is_numeric($get['loading_timeout'])
            ) {
                $Api->error("not-legal");
            }
            
            $get["dis_images"]   = ($get["dis_images"] === 'on')?1:0;
            $get["dis_css"]      = ($get["dis_css"] === 'on')?1:0;
            $get["ena_debugger"] = ($get["ena_debugger"] === 'on')?1:0;
            $get["ena_outfile"]  = ($get["ena_outfile"] === 'on')?1:0;
            $get["ena_grout"]    = ($get["ena_grout"] === 'on')?1:0;
            $get["ena_stamp"]    = ($get["ena_stamp"] === 'on')?1:0;
            $get["use_encoding"] = ($get["use_encoding"] === '-1') ? 'null' : intval($get["use_encoding"]) ; 
            
            if ($Api::$conn->insert_safe("settingsplan", $get)) {
                $success = "general";
            } else {
                $Api->error("query");
            }
        break;
        
        //Delete Settings plan:
        case "delplan" : 
            //Synth needed:
            $get = $Api->Func->synth($_POST, array('row'),false);
            //Validate:
            if (
                    $get['row']  === ''
                ||  !is_numeric($get['row'])
            ) {
                $Api->error("not-legal");
            }
            if ($Api::$conn->delete("settingsplan",array(array("id",'=',$get['row'])))) {
                $results = array(
                        "row" => intval($get['row'])
                    );
                $success = "with-results";
            } else {
                $Api->error("query");
            }
        break;
        
        //Get all sessions:
        case "getsessions" :
            $get = $Api::$conn->get_joined(
                array(
                    array('LEFT JOIN', 'sessionlog.user', 'users.id')
                ),
                "`sessionlog`.*, `users`.`username`",
                false,
                false,
                array(array("sessionlog.start_date"),"DESC")
            );
            if (!is_array($get)) {
                $Api->error("query");
            }
            foreach ($get as $key => $row) {
                if (!empty($row["session_settings"])) {
                    $get[$key]["session_settings"] = json_decode($row["session_settings"], true);
                }
                if (!empty($row["workers_report"])) {
                    $get[$key]["workers_report"] = json_decode($row["workers_report"], true);
                }
            }
            $results = array(
                "rows" => $get
            );
            $success = "with-results";
        break;
        
        //Log a session start:
        case "logsession" : 
            $Extract = new Extract();
            //Synth needed:
            $get = $Api->Func->synth($_POST, array("namesess", "typesess","limit","month","year","sample","sense","workers","paused","done","failed","scopeHash","folderPath"), false);
            //Validate:
            if (
                    $get['namesess']  === ''
                ||  $get['typesess']  === ''
                ||  $get['limit']  === ''
                ||  $get['month']  === ''
                ||  $get['year']  === ''
                ||  $get['sample']  === ''
                ||  $get['sense']  === ''
                ||  $get['workers']  === ''
                ||  $get['done']  === ''
                ||  $get['failed']  === ''
                ||  $get['scopeHash']  === ''
                ||  $get['folderPath']  === ''
            ) {
                $Api->error("not-legal");
            }
            //Extract values:
            $get["user"] = $User->user_info;
            $ins = $Extract->parse_session_log_input($get);

            if ($Api::$conn->insert_safe("sessionlog", $ins)) {
                $results = array(
                        "indexlog" => $Api::$conn->lastid(),
                        "sessname" => $get['namesess']
                    );
                $success = "with-results";
            } else {
                $Api->error("query");
            }
        break;
        
        //Log worker report:
        case "logworker" : 
            $Extract = new Extract();
            //Synth needed:
            $get = $Api->Func->synth($_POST, array("logindex","name","state","done","failed","reload"), false);
            //Validate:
            if (
                    $get['logindex']  === ''
                ||  !is_numeric($get['logindex'])
                ||  $get['name']  === ''
                ||  $get['state']  === ''
                ||  $get['done']  === ''
                ||  $get['failed']  === ''
                ||  $get['reload']  === ''
            ) {
                $Api->error("not-legal");
            }
            //Extract values:
            $_prevLogWorker = $Api::$conn->get("sessionlog", $get['logindex']);
            if (empty($_prevLogWorker)) {
                $Api->error("query");
            }
            $prevLogWorker = $Extract->update_session_log_worker($_prevLogWorker, $get);
            if (!$prevLogWorker) {
                $Api->error("results-false");
            }
            $Api::$conn->transBegin();
            if (
                $Api::$conn->update(
                    "sessionlog", 
                    array("workers_report" => $prevLogWorker), 
                    array(array("id","=",$get['logindex'])), 
                    array(1)
                )
           ) {
                $Api::$conn->transCommit();
                $success = "general";
            } else {
                $Api::$conn->transRollback();
                $Api->error("query");
            }
        break;
        
        //Local result logs store in folders:
        case "listlocallogs": 
            //Synth needed:
            $get = $Api->Func->synth($_POST, array('range', 'path', 'sessname') ,false);
            if (
                $get['path']  === '' || 
                !is_dir($get['path']) ||
                $get['sessname']  === ''
            ) {
                $Api->error("not-legal");
            }
            //Parse Range:
            $Rbottom = 0;
            $Rupper = 100;
            if ( $get['range'] !== '' ) {
                $temp = explode('-', $get['range']);
                $Rbottom = intval($temp[0]);
                $Rupper  = intval($temp[1]);
            }
            //Extract sess dir path
            //$dirs = array_filter(glob($get['path'].$get['sessname'].'_*'), 'is_dir');
            $dirs = array_filter(glob($get['path']), 'is_dir');
            $target_dir = "";
            foreach ($dirs as $key => $data) {
                $target_dir = $data;
                break;
            }
            if ($target_dir == "") {
                $Api->error("no-sess-dir");
            }
            //Extract Log directory of session: 
            $dirs_of_sess = array_filter(glob($target_dir.'\\*'), 'is_dir');
            $out_dirs = array();
            foreach ($dirs_of_sess as $key => $data) {
                if ($key == $Rupper) break;
                if ($key >= $Rbottom) {
                    $out_dirs[] = $data;
                }
            }
            //Buils response rersult:
            $results["dirs_total_count"] = count($dirs_of_sess);
            $results["dirs_extracted"] = $out_dirs;
            $results["range"] = array( "b" => $Rbottom, "u"=> $Rupper);
            $results["session_name"] = $get['sessname'];
            $results["gen_path"] = $get['path'];
            $success = "with-results";
            
        break;
        
        //Local Storage extract content:
        case "listlocallogsfiles":
            //Synth needed:
            $get = $Api->Func->synth($_POST, array('path') ,false);
            if (
                $get['path']  === '' || 
                !is_dir($get['path'])
            ) {
                $Api->error("not-legal");
            }
            //Extract Log directory of session: 
            $files = array_filter(glob($get['path'].'\\*'), 'is_file');
            $out_files = array();
            
            foreach ($files as $key => $data) {
                $file_parts = pathinfo($data);
                switch($file_parts['extension'])
                {
                    case "jpg":
                    case "png":
                        $out_files[] = array(
                            "path" => $data,
                            "url"  => "/".substr( preg_replace('/\\\\/','/',$data) , strlen($_SERVER['DOCUMENT_ROOT']) ),
                            "name" => $file_parts['basename'],
                            "ext" => $file_parts['extension']
                        );
                    break;
                    case "txt":
                        $out_files[] = array(
                            "path" => $data,
                            "content" => file_get_contents($data),
                            "url"  => "/".substr( preg_replace('/\\\\/','/',$data) , strlen($_SERVER['DOCUMENT_ROOT']) ),
                            "name" => $file_parts['basename'],
                            "ext" => $file_parts['extension']
                        );
                    break;
                }
            }
            //Buils response rersult:
            $results["files_total_count"] = count($out_files);
            $results["gen_path"] = $get['path'];
            $results["files"] = $out_files;
            $success = "with-results";
        break;
        
        //Get a valid operation scope for session:
        case "getscope" : 
            //Synth needed:
            $get = $Api->Func->synth($_POST, array('selectmonth','selectyear','namespace','selectplan','newtryfailed','selectmonth','selectyear','selectlimit','selectsamples','selectworkers'),false);
            $get['newtryfailed'] = (empty($get['newtryfailed']))?false:true;
            //Validate:
            if (
                    $get['selectmonth']  === ''
                ||  !is_numeric($get['selectmonth'])
                ||  $get['selectyear']  === ''
                ||  !is_numeric($get['selectyear'])
                ||  $get['namespace']  === ''
                ||  $get['selectplan']  === ''
                ||  !is_numeric($get['selectplan'])
                ||  !is_numeric($get['selectlimit'])
                ||  !is_numeric($get['selectsamples'])
                ||  !is_numeric($get['selectworkers'])
            ) {
                $Api->error("not-legal");
            }
            
            //Scopes Tests:
                // "1","3-15","18","21","23-40","90*" lim 10
                // Expected: 1,3,4,5,6,7,8,9,10,

                // "3-6","8","21-28","45*" lim 50
                // Expected: 3,4,5,6,8,21,22,23,24,25,26,27,28,46,47,48,49,50, 

                // "1","3-6","20","58" lim 50
                // Expected: 1,3,4,5,6,20,

                // "1", "46-60" lim 50
                // Expected: 1,46,47,48,49,50,

                // "44*" lim 50
                // Expected: 45,46,47,48,49,50,
                
            //Get Scope:
            
            //Craet IO space:
            $use_plan = $Api::$conn->get("settingsplan",$get['selectplan']);
            if (empty($use_plan)) { $Api->error("no-plan"); }
            $outfolder = $use_plan["out_folder"];
            $today = getdate();
            $folder_name = $get['namespace']."_".$today["mday"].$today["mon"].$today["year"]."_".$today[0];
            $newPath = rtrim($outfolder,"\\")."\\".$folder_name;
            $create = @mkdir($newPath);
            
            //Test Folder creation:
            if(!$create) {
                 $Api->error("dir-create");
            }
            
            //Attach results:
            $results = array(
                "name"  => $get['namespace'],
                "plan"  => intval($get['selectplan']),
                "path"  => $newPath,
                "dir"   => $folder_name,
                "goal"  => array( 
                                    "month" => intval($get['selectmonth']),
                                    "year"  => intval($get['selectyear']),
                                    "lim"  => intval($get['selectlimit'])
                ),
                "load"  => array(
                                    "workers" => intval($get['selectworkers']),
                                    "samples" => intval($get['selectsamples'])
                ),
                //"cases" => array("1","3-15","18","21","23-40","90*")
                //"cases" => array("3-6","8","21-28","45*")
                //"cases" => array("1","3-6","20","58")
                //"cases" => array("1", "46-60")
                "cases" => array("8*")
            );
            $success = "with-results";
        break;
        
        //Crawler ANew:
        case "workeragentnew" : 
            //Synth needed:
            $get = $Api->Func->synth($_POST, array('workerId','case','month','year','scopei','plan', 'sample', 'sesspath', 'foldername'), false);
            //Validate:
            if (
                       $get['workerId']  === ''
                    || $get['sesspath']  === ''
                    || $get['foldername']  === ''
                    || !is_numeric($get['case'])
                    || !is_numeric($get['month'])
                    || !is_numeric($get['year'])
                    || !is_numeric($get['scopei'])
                    || !is_numeric($get['plan'])
                    || !is_numeric($get['sample'])
            ) {
                $Api->error("not-legal");
            }
            
            //Load plan settings:
            $all_encodings = $Api::$conn->get("supported_encoding");  
            $use_plan = $Api::$conn->get("settingsplan",$get['plan']);
            
            if (empty($use_plan)) { $Api->error("no-plan"); }
            
            //Build args:
            $Extract = new Extract();
            $encoding = false;
            $finalCasePath = rtrim($get['sesspath'],"\\").
                    (($use_plan["ena_grout"])?"\\c".$get['case']."m".$get['month']."y".$get['year']:"").
                    "\\n".$get['case']."m".$get['month']."y".$get['year'].".txt";
            
            if (!empty($use_plan["use_encoding"])) {
                $encoding = $Page->Func->search_multi_secondDim(
                    (!empty($all_encodings) ? $all_encodings : []), 
                    "id", 
                    intval($use_plan["use_encoding"])
                );
                $encoding = ($encoding !== false) ? $all_encodings[$encoding]["encoding_value"] : false;
            }
            $outfolder = (!empty($get["sesspath"])) ? addslashes(rtrim($get["sesspath"],"\\")."\\") : false;
            $arguments = $Extract->get_console_arguments(
                false, 
                ($use_plan["ena_outfile"])?true:false, 
                ($use_plan["ena_debugger"])?true:false, 
                (is_numeric($use_plan["timeout"]))?$use_plan["timeout"]:false,
                (is_numeric($use_plan["loading_timeout"]))?$use_plan["loading_timeout"]:false, 
                ($use_plan["dis_css"])?true:false, 
                ($use_plan["dis_images"])?true:false, 
                ($use_plan["ena_stamp"])?true:false, 
                ($use_plan["ena_grout"])?true:false, 
                $outfolder,
                $encoding
            );
            $arguments = $Extract->attach_target_arguments($get['case'], $get['month'], $get['year'], $arguments);
            
            //Initialize the Crawler:
            $CANew = new CrawlerANew(
                    $Api::$conf['crawl']['anewpath'], 
                    $outfolder, 
                    $arguments
            );
            //Run crawler
            $CANew->runCrawl();
            //$CANew->runCrawlProc();
            $resCrawl = $CANew->parseResult(  ($use_plan["ena_outfile"])? $finalCasePath : false  );
            
            //Prepare return:
            $crawl_status = "";
            if (is_array($resCrawl) && isset($resCrawl['caserow']) && isset($resCrawl['caserow']['opSummary'])) {
                $parse_op = $Extract->parse_bot_operation_summary($resCrawl['caserow']['opSummary']);
                $crawl_status = "done";
                $results = array(
                    "workerId"  => $get['workerId'],
                    "time"      => (isset($parse_op['TOTAL']))?intval($parse_op['TOTAL']):50,
                    "status"    => $crawl_status,
                    "statusmes" => $CANew->failType,
                    "case"      => intval($get['case']),
                    "scopei"    => intval($get['scopei']),
                    "useLocalCheck" => $CANew->usedLocalCheck,
                    "issample"  => ($get['sample'] == 1)? true : false
                );
            } else {
                $crawl_status = ($resCrawl == "no" || $resCrawl == "priv" || $resCrawl == "fail")? $resCrawl : "fail";
                $results = array(
                    "workerId"    => $get['workerId'],
                    "time"        => 30,
                    "status"      => $crawl_status,
                    "statusmes"   => $CANew->failType,
                    "debugBuffer" => ($resCrawl == "fail") ? json_encode($CANew->outBuffer, JSON_UNESCAPED_UNICODE) : "",
                    "debugCode"   => $CANew->execCode,
                    "case"        => intval($get['case']),
                    "scopei"      => intval($get['scopei']),
                    "useLocalCheck" => $CANew->usedLocalCheck,
                    "issample"    => ($get['sample'] == 1)? true : false
                );
            }
            
            //Log if sample:
            if ($results["issample"]) {
                $push_sample = array(
                    "session_type"     => "ANew",
                    "session_name"     => explode(".",$get['workerId'])[0],
                    "case_name"        => $get['case']."-".$get['month']."-".$get['year'],
                    "crawl_status"     => $crawl_status,
                    "crawl_status_mes" => $CANew->failType,
                    "json_value"       => (is_array($resCrawl))? json_encode($resCrawl, JSON_UNESCAPED_UNICODE) : $resCrawl,
                    "debug_folder"     => (isset($parse_op) && is_array($parse_op) && isset($parse_op['FOLDER']))? $parse_op['FOLDER'] : "",
                    "sum_file"         => (isset($parse_op) && is_array($parse_op) && isset($parse_op['FILE']))? $parse_op['FILE']: "",
                    "byuser"           => $User->user_info
                );
                $Api::$conn->insert_safe("sampledata", $push_sample);
            }
            
            //Push to remote:
            $reomteConf = array('host' => "localhost", 'dbuser' => "shlomo", 'dbpass' => "sh4hs1", "dbname" => "midatadin", 'port' => "3306");
            $remote = new DB($reomteConf);
            if ($crawl_status === "done") {
                
                //Process court:
                $courtKey = $Extract->update_court_keys($remote, $resCrawl['allsubrows'][0]['rowCourt']);
                
                //Process judges:
                $resCrawl['allsubrows'][0]['rowJudgeTypeName'] = $Extract->update_judge_keys($remote, $resCrawl['allsubrows'][0]['rowJudgeTypeName'], $resCrawl['allsubrows'][0]['rowTypeJudgeType']);
                
                //Process lawyers:
                
                //Search for prejudges:
                $preJudgeTest = $Extract->search_prejudges($resCrawl['allsubrows']);

                //Store Diag:
                $parse_opKey = $Extract->store_caseDiag($remote, (isset($parse_op)) ? $parse_op : array());

                //Insert Main row:
                $mainRowKey = $Extract->store_mainRow($remote, $resCrawl['caserow'], 1, $courtKey, $parse_opKey, $preJudgeTest);
                
                //Insert all parties ! normalize
                $parties = $Extract->store_parties($remote, $resCrawl['parties'], $mainRowKey);
                
                //Insert all sub rows:
                $subRows = $Extract->store_subRows($remote, $resCrawl['allsubrows'], $mainRowKey);
                
                
            } elseif ($crawl_status === "no" || $crawl_status === "priv") {
                
                //Insert Main row:
                $row = array(
                    "caseFullNum"       => $Extract->build_case_num($get['case'], $get['month'], $get['year']),
                    "status"            => $crawl_status,
                    "yearVal"           => $get['year'],
                    "monthVal"          => $get['month'],
                    "caseVal"           => $get['case']
                );
                $mainRowKey = $Extract->store_mainRow($remote, $row, 1, "NULL", "NULL", false);
                
            }
            
            $success = "with-results";
            
        break;
        
        //Add new data center:
        case "newdatacenter":
            
            $Extract = new Extract();
            //Synth needed:
            $get = $Api->Func->synth($_POST, array("target"), true, true);
            $get["target"] = explode(":",$get["target"]);
            //Validate:
            if (
                !is_array($get['target'])
                || count($get['target']) !== 6
            ) {
                $Api->error("not-legal");
            }
            //Extract values:
            
            $ins["engine"]           = $get['target'][0];
            $ins["address"]          = $get['target'][1];
            $ins["port"]             = $get['target'][2];
            $ins["dbname"]           = $get['target'][3];
            $ins["user"]             = $get['target'][4];
            $ins["password"]         = $get['target'][5];
            $ins["last_connected"]   = "NOW()";
            
            //Process
            if (!$Extract->parse_remote_and_validate($ins)) {
                $Api->error("bad-connection");
            }
            
            //Save to db:
            if ($Api::$conn->insert_safe("datacenter_queries", $ins)) {
                $results = array(
                        "added_id" => $Api::$conn->lastid(),
                        "server" => $ins
                    );
                $success = "with-results";
            } else {
                $Api->error("query");
            }
            
        break;
        //Test data center connection:
        case "datacentertestconnection":
            
            $Extract = new Extract();
            //Synth needed:
            $get = $Api->Func->synth($_POST, array("targetid"), true, true);
            //Validate:
            if (
                !is_numeric($get['targetid'])
            ) {
                $Api->error("not-legal");
            }
            //Extract values:
            $res = $Api::$conn->get("datacenter_queries", intval($get['targetid']));

            //Process
            if (!$Extract->parse_remote_and_validate($res)) {
                $Api->error("bad-connection");
            }
            
            //Save to db:
            if (is_array($res)) {
                $results = array(
                        "test" => true
                    );
                $success = "with-results";
            } else {
                $Api->error("query");
            }
        break;
        
        case "datacenterloadcase":
            $Extract = new Extract();
            //Synth needed:
            $get = $Api->Func->synth($_POST, array("targetconnid", "case"), true, true);
            //Validate:
            if (
                    !is_numeric($get['targetconnid'])
                ||  empty($get['case'])
            ) {
                $Api->error("not-legal");
            }
            
            //Extract values:
            $res = $Api::$conn->get("datacenter_queries", intval($get['targetconnid']));

            //Process:
            $set = $Extract->get_case_full_meta($get['case'], $res);
            
            if (!is_array($set)) {
                switch ($set) {
                    case 2: $Api->error("bad-connection");
                    case 3: $Api->error("empty-results");
                    case 4: $Api->error("query");
                    default:
                        $Api->error("general");
                } 
            } else {
                $results = $set;
                $success = "with-results";
            }
        break;
        
        //Unknown type - error:
        default : 
            $Api->error("bad-who");
        
    }
    
    //Run Response generator:
    $Api->response($success, $results);
    
} else {
    $Api->error("not-secure");
}

//Kill Page.
exit;
/*

SELECT `sessionlog`.*, `users`.`username` FROM `sessionlog` LEFT JOIN `users` ON `sessionl45og`.`user` = `users`.`id` ORDER BY `sessionlog`.`start_date` DESC 

INSERT INTO `all_lawyers` (`lawyer_name`, `gender`) VALUES ('yaron',1) ON DUPLICATE KEY UPDATE `flag_dup` = 1;
 */