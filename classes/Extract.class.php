<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Extract
 *
 * @author shlomi
 */
class Extract extends Func {
    
    public $getDateFormat = "d/m/Y H:i:s";
    public $setDateFormat = "Y-m-d H:i:s";

    /** Bild command line arguements for bots
     * 
     * @param string $start
     * @param bool $file
     * @param bool $debugger
     * @param int $timeout
     * @param int $loadtimeout
     * @param bool $discss
     * @param bool $disimages
     * @param bool $stamp
     * @param bool $grout
     * @param string $folder
     * @param string $encoding
     * @param string $end
     * @return string
     */
    public function get_console_arguments(
            $start     = false, 
            $file      = false, 
            $debugger  = false, 
            $timeout   = false,
            $loadtimeout = false,
            $discss    = false, 
            $disimages = false, 
            $stamp     = false, 
            $grout     = false, 
            $folder    = false, 
            $encoding  = false,
            $end       = false
    ) {
        return (($start)?$start." ":"")    
            . (($file)?"-f"." ":"")
            . (($debugger)?"-p"." ":"")
            . (($timeout !== false && intval($timeout) > 0)?"--timeout=".$timeout." ":"")
            . (($loadtimeout !== false && intval($loadtimeout) > 0)?"--loadtimeout=".$loadtimeout." ":"")
            . (($discss)?"--discss"." ":"")
            . (($disimages)?"--disimages"." ":"")
            . (($stamp)?"-t"." ":"")
            . (($grout)?"--grout"." ":"")
            . (($folder)?"-o=".$folder." ":"")
            . (($encoding !== false && !is_null($encoding))?"-e=".$encoding." ":"")
            . (($end)?$end:"");
    }
    
    /** Attach needed target values for bot operations:
     * 
     * @param int $num
     * @param int $month
     * @param int $year
     * @param string $prev
     * @return string
     */
    public function attach_target_arguments($num, $month, $year, $prev = "") {
        
        return "-n=".intval($num)." "
               ."-m=".intval($month)." "
               ."-y=".intval($year)
               .(($prev !== "")?" ".$prev:"");
        
    }
    
    /** parse an operation summary of any bot crawler:
     * 
     * @param string $input
     * @return array
     * 
     */
    public function parse_bot_operation_summary($input) {
        $return = array();
        if (!is_string($input) || empty($input)) { return false; }
        $values = explode("|||",$input);
        if (!is_array($values) || empty($values)) { return false; }
        foreach ($values as $key => $data) {
            $values[$key] = explode(":::",$data);
            if (count($values[$key]) > 1) {
                $return[$values[$key][0]] = (is_numeric($values[$key][1]))?
                    floatval($values[$key][1]):
                    $values[$key][1];
            }
        }
        return $return;
    }
    
    /** Returns the data to insert into session log:
     * 
     * @param array $data
     * @return array 
     */
    public function parse_session_log_input($data) {
        $ret = array(
            "user"             => $data["user"],
            "session_type"     => $data["typesess"],
            "session_name"     => $data["namesess"],
            "session_settings" => json_encode($data),
            "localStorage"     => $data["folderPath"],
            "workers_report"   => ""
        );
        $workers = array();
        for ($i = 0; $i < intval($data["workers"]); $i++) {
            $workers["W.".($i + 1)] = array(
                "state"     => 1, // 1-> run, 2-> paused, 3 -> complete
                "done"      => 0, 
                "failed"    => 0, 
                "reload"    => 0  
            );
        }
        $ret["workers_report"] = json_encode($workers);
        return $ret;
    }
    
    /** update worker in log:
     * 
     * @param array $origin
     * @param string $worker => json workers that has updated values:
     * 
     */
    public function update_session_log_worker($origin, $worker) {
        $workers_report = json_decode($origin["workers_report"], true);
        if (empty($workers_report) || !isset($workers_report["W.".$worker["name"]])) { return false; }
        $workers_report["W.".$worker["name"]] = array(
            "state"     => $worker["state"],
            "done"      => $worker["done"], 
            "failed"    => $worker["failed"], 
            "reload"    => $worker["reload"]  
        );
        return json_encode($workers_report);
    }
    
    /** return case full number:
     * 
     * @param number $n
     * @param number $_m
     * @param number $_y
     * @return string
     */
    public function build_case_num($n, $_m, $_y) {
        $m = (intval($_m) < 10)?"0".intval($_m):"".intval($_m);
        $y = (strlen((string)$_y) < 2)?"0".$_y:"".$_y;
        return $n."-".$m."-".$y;
    }
    
    /** Normalize side naming system and breaks it into components:
     * 
     * @param string $name
     * @return array -> ["name" => string , "index" => integer, "full" => string]
     */
    public function normalize_party($name) {
        $return = array(
            "name"  => "",
            "index" => 0,
            "full"  => ""
        );
        $out = array();
        $return["full"] = trim($name);
        $return["name"] = trim(preg_replace("#\s+#", " ", preg_replace("#[0-9]+#","", $return["full"])));
        preg_match_all("#[0-9]+#", $return["full"], $out);
        if (is_array($out) && isset($out[0]) && isset($out[0][0])) {
            $return["index"] = intval($out[0][0]);
        }
        return $return;
    }
    
    /** normailze dates for DB insert
     * 
     * @param string $str
     * @param string|FALSE $_from
     * @param string|FALSE $_to
     * @return string
     */
    public function normalize_date($str, $_from = false, $_to = false) {
        $from = (!$_from)?$this->getDateFormat:$_from;
        $to   = (!$_to)?$this->setDateFormat:$_to;
        $dateTime = DateTime::createFromFormat($from, $str);
        return $dateTime->format($to);
    }
    /** normailze floats for DB insert
     * 
     * @param string $str
     * @return string|float
     */
    public function normalize_float($str) {
        if (is_string($str) && strlen($str) > 2) {
            return floatval(preg_replace("#[^0-9\.]#", "", $str));
        }
        return "NULL";
    }

//Data center operations:
    public function parse_remote_and_validate($info) {
        //Push to remote:
        $reomteConf = array('host' => $info["address"], 'dbuser' => $info["user"], 'dbpass' => $info["password"], "dbname" => $info["dbname"], 'port' => $info["port"]);
        $remote = new DB($reomteConf, false);
        if ($remote->ping_db()) {
            $remote = null;
            return true;
        } else {
            $remote = null;
            return false;
        }
    }
    /** Generate a full case meta set:
     * 
     * @param string $case
     * @param DB $rconn
     * @return mixed / integer error, array results:
     */
    public function get_case_full_meta($case, $rconn) {
        //Connect:
        if (!is_array($rconn)) { return 2; }
        $res = array('case' => $case, 'rows' => false, 'parties' => false, 'prejudges' => false, 'diag' => false);
        $reomteConf = array('host' => $rconn["address"], 'dbuser' => $rconn["user"], 'dbpass' => $rconn["password"], "dbname" => $rconn["dbname"], 'port' => $rconn["port"]);
        $remote = new DB($reomteConf, false);
         if ($remote->ping_db()) {
            
            //Build the set:
            $res['rows'] = $remote->get_joined(
                array(
                    array('LEFT JOIN', 'casemainrows.id', 'casesubrows.ofMainRow'),
                    array('LEFT JOIN', 'casesubrows.rowJudgeTypeName', 'all_judges.judge_full_name'),
                    array('LEFT JOIN', 'casesubrows.rowCourt', 'courts.courtName')
                ),
                "`casemainrows`.*, `casesubrows`.*, `all_judges`.`judge_only_name` ,`courts`.`courtPlace`",
                "`casemainrows`.`fullCaseNumber` = '".$remote->filter($case)."'"
            );
            
            if (!empty($res['rows'])) {
                $res['parties'] = $remote->get_joined(
                    array(
                        array('LEFT JOIN', 'casesparties.partyName', 'all_entities.ent_name'),
                        array('LEFT JOIN', 'casesparties.representedBy', 'all_lawyers.lawyer_name')
                    ),
                    "`casesparties`.*, `all_entities`.`ent_gov`, `all_entities`.`ent_firm`, `all_entities`.`ent_public_personal`, `all_entities`.`ent_priv`, `all_lawyers`.`firm` AS lawywer_firm",
                    "`casesparties`.`ofCase` = '".$res['rows'][0]['ofMainRow']."'"
                );
                if (!empty($res['rows'][0]['diag'])) {
                    $res['diag'] = $remote->get("casediag", $res['rows'][0]['diag']);
                }
            } else {
                return 3;
            }
            //Close and return:
            $remote = null;
            return $res;
            
        } else {
            $remote = null;
            return 2; // connection error.
        }
    }
    
//Remote insertion:
    /** searchech results to test if pre judges are used:
     * 
     * @param array $all
     * @return boolean
     */
    public function search_prejudges($all) {
        foreach($all as $data) {
            if (isset($data['preJud']) && is_array($data['preJud']) && count($data['preJud']) > 0) {
                return true;
            }
        }
        return false;
    }
    
    /** Procedure to insert or get a court key
     * 
     * @param DB $link
     * @param string $target_court
     * @return integer
     */
    public function update_court_keys(&$link, $target_court) {
        $court = trim($target_court);
        if (is_string($court) && strlen($court) > 3) {
            $_court = $link->filter($court);
            $testQuery = $link->exec_query("INSERT INTO `courts` (`courtName`) VALUES ('".$_court."') ON DUPLICATE KEY UPDATE `flag_dup` = 1 ");
            if (!empty($testQuery)) {
                return $court;
            }
        }
        return "NULL";
    }
    
    /** Procedure to insert new judges without duplicates
     * 
     * @param DB $link
     * @param string $target_judge
     * @return integer
     */
    public function update_judge_keys(&$link, $target_judge, $judge_type) {
        $judge = trim($target_judge);
        $type = trim($judge_type);
        $judge_full = $judge."-".$type;
        if (is_string($judge) && strlen($judge) > 3) {
            $_judge = $link->filter($judge);
            $_type = $link->filter($type);
            $_judge_full = $link->filter($judge_full);
            $testQuery = $link->exec_query("INSERT INTO `all_judges` (`judge_full_name`,`judge_only_name`,`judge_type`) VALUES ('".$_judge_full."','".$_judge."','".$_type."') ON DUPLICATE KEY UPDATE `flag_dup` = 1 ");
            if (!empty($testQuery)) {
                return $judge_full;
            }
        }
        return "NULL";
    }
    
    /** Procedure to insert new lawyers without duplicates
     * 
     * @param DB $link
     * @param string $target_lawyer
     * @return integer
     */
    public function update_lawyer_keys(&$link, $target_lawyer) {
        $lawyer = trim($target_lawyer);
        if (is_string($lawyer) && strlen($lawyer) > 3) {
            $_lawyer = $link->filter($lawyer);
            $testQuery = $link->exec_query("INSERT INTO `all_lawyers` (`lawyer_name`) VALUES ('".$_lawyer."') ON DUPLICATE KEY UPDATE `flag_dup` = 1 ");
            if (!empty($testQuery)) {
                return $lawyer;
            }
        }
        return "NULL";
    }
    
    /** Procedure to insert new lawyers without duplicates
     * 
     * @param DB $link
     * @param string $target_ent
     * @return integer
     */
    public function update_entities_keys(&$link, $target_ent) {
        $ent = trim($target_ent);
        if (is_string($ent) && strlen($ent) > 3) {
            $_ent = $link->filter($ent);
            $testQuery = $link->exec_query("INSERT INTO `all_entities` (`ent_name`) VALUES ('".$_ent."') ON DUPLICATE KEY UPDATE `flag_dup` = 1 ");
            if (!empty($testQuery)) {
                return $ent;
            }
        }
        return "NULL";
    }

    /** Procedure to insert a new case diagnostic row:
     * 
     * @param DB $link
     * @param array $parse_op
     * @return integer
     */
    public function store_caseDiag(&$link, $parse_op) {
        if (!is_array($parse_op) || empty($parse_op)) {
            return "NULL";
        }
        $caseDiag = array(
            "timeInit"          => (isset($parse_op["init"]))?$parse_op["init"]:-1,
            "timeStep_one"      => (isset($parse_op["Step1"]))?$parse_op["Step1"]:-1,
            "timeStep_two"      => (isset($parse_op["Step2"]))?$parse_op["Step2"]:-1,
            "timeStep_three"    => (isset($parse_op["Step3"]))?$parse_op["Step3"]:-1,
            "timeStep_rows"     => implode(",",$this->get_array_with_keys_include($parse_op,"Step4")),
            "timeStep_five"     => (isset($parse_op["Step5"]))?$parse_op["Step5"]:-1,
            "timeStep_six"      => (isset($parse_op["Step6"]))?$parse_op["Step6"]:-1,
            "timeTotal"         => (isset($parse_op["TOTAL"]))?$parse_op["TOTAL"]:-1
        );
        $testQueryOp = $link->insert_safe("casediag",$caseDiag);
        if ($testQueryOp) {
            return $link->lastid();
        }
        return "NULL";
    }
    
    /** Stores main row in remote database:
     * 
     * @param DB $link
     * @param array $row
     * @param integer $crawlStatus
     * @param integer|string $courtKey
     * @param integer|string $parse_opKey
     * @param integer $preJudgeTest
     * @return integer
     */
    public function store_mainRow(&$link, $row, $crawlStatus, $courtKey, $parse_opKey, $preJudgeTest) {
        $buildMainRow = array(
            "fullCaseNumber"    => $row['caseFullNum'],
            "crawlStatus"       => $crawlStatus,
            "caseStatus"        => $row['status'],
            "subStatus"         => (isset($row['subStatus']))?$row['subStatus']:"",
            "court"             => $courtKey,
            "rowsNum"           => (isset($row['rowsNum']))?intval($row['rowsNum']):0,
            "docketsNum"        => (isset($row['docketsNum']))?intval($row['docketsNum']):0,
            "caseFullName"      => (isset($row['caseFullName']))?$row['caseFullName']:"",
            "diag"              => $parse_opKey,
            "yearVal"           => (isset($row['yearVal']))?intval($row['yearVal']):0,
            "monthVal"          => (isset($row['monthVal']))?intval($row['monthVal']):0,
            "caseVal"           => (isset($row['caseVal']))?intval($row['caseVal']):0,
            "preJudges"         => ($preJudgeTest)?1:0
        );
        $testQueryMain = $link->insert_safe("casemainrows",$buildMainRow);
        if ($testQueryMain) {
            return $link->lastid();
        }
        return "NULL";
    }
    
    /** Srores all parties in the remote db:
     * 
     * @param DB $link
     * @param array $rows
     * @param integer $ofRow
     * @return int|boolean
     */
    public function store_parties(&$link, $rows, $ofRow) {
        if (!is_array($rows) || empty($rows)) { return false; }
        $i = 0;
        foreach ($rows as $party) {
            $i++;
            $naming = $this->normalize_party((isset($party["knownAsPartie"]) && $party["knownAsPartie"] !== "notSet")?$party["knownAsPartie"]:"");
            $insert = array(
                "ofCase"            => $ofRow,
                "partyNum"          => $i,
                "knownAsPartyFull"  => $naming["full"],
                "knownAsParty"      => $naming["name"],
                "knownAsPartyNum"   => $naming["index"],
                "partyName"         => (isset($party["namePartie"]) && $party["namePartie"] !== "notSet")?trim($party["namePartie"]):"",
                "partySide"         => (isset($party["partieSide"]) && $party["partieSide"] !== "notSet")?trim($party["partieSide"]):"",
                "representedBy"     => (isset($party["representedByName"]) && $party["representedByName"] !== "notSet")?trim($party["representedByName"]):"",
                "resultForSide"     => (isset($party["resultForSide"]) && $party["resultForSide"] !== "notSet")?trim($party["resultForSide"]):"",
                "earased"           => (isset($party["earased"]) && $party["earased"] !== "notSet")?trim($party["earased"]):""
            );
            
            // Update Lawyers and entities:
            $insert["representedBy"] = $this->update_lawyer_keys($link, $insert["representedBy"]);
            $insert["partyName"] = $this->update_entities_keys($link, $insert["partyName"]);
            
            //Save parties records:
            $link->insert_safe("casesparties",$insert);
        }
        return $i;
    }
    
    /** store all sub rows in remote db
     * 
     * @param DB $link
     * @param array $rows
     * @param integer $ofRow
     * return int|boolean
     * 
     */
    public function store_subRows(&$link, $rows, $ofRow) {
        if (!is_array($rows) || empty($rows)) { return false; }
        $i = 0;
        foreach ($rows as $sub) {
            $i++;
            //Handle court:
            $courtKey = "NULL";
            if ($sub["rowCourt"] !== "notSet" && !empty($sub["rowCourt"])) {
                $courtKey = $this->update_court_keys($link, $sub["rowCourt"]);
            }
            
            //Store subrow:
            $row = array(
                "ofMainRow"         => $ofRow,
                "rowNum"            => $i,
                "fullCaseNumber"    => $sub["fullCaseNumber"],
                "rowKtavNameFull"   => (isset($sub["fullRowName"]) && $sub["fullRowName"] !== "notSet")?$sub["fullRowName"]:"",
                "rowKtavName"       => (isset($sub["rowKtavName"]) && $sub["rowKtavName"] !== "notSet")?$sub["rowKtavName"]:"",
                "dateOpen"          => (isset($sub["dateOpen"]) && $sub["dateOpen"] !== "notSet")? $this->normalize_date($sub["dateOpen"]) : "NULL",
                "dateProccess"      => (isset($sub["dateProcess"]) && $sub["dateProcess"] !== "notSet")? $this->normalize_date($sub["dateProcess"]) : "NULL",
                "rowStatus"         => (isset($sub["rowStatus"]) && $sub["rowStatus"] !== "notSet")?$sub["rowStatus"]:"",
                "rowCourt"          => $courtKey,
                "rowProcedure"      => (isset($sub["rowProcedure"]) && $sub["rowProcedure"] !== "notSet")?$sub["rowProcedure"]:"",
                "rowCaseType"       => (isset($sub["rowCaseType"]) && $sub["rowCaseType"] !== "notSet")?$sub["rowCaseType"]:"",
                "rowIntrest"        => (isset($sub["rowIntrest"]) && $sub["rowIntrest"] !== "notSet")?$sub["rowIntrest"]:"",
                "rowDetailsGen"     => (isset($sub["rowDetailsGen"]) && $sub["rowDetailsGen"] !== "notSet")?$sub["rowDetailsGen"]:"",
                "rowTypeTeanot"     => (isset($sub["rowTypeTeanot"]) && $sub["rowTypeTeanot"] !== "notSet")?$sub["rowTypeTeanot"]:"",
                "rowHisayon"        => (isset($sub["rowHisayon"]) && $sub["rowHisayon"] !== "notSet")?$sub["rowHisayon"]:"",
                "rowAmount"         => (isset($sub["rowAmount"]) && $sub["rowAmount"] !== "notSet")?$this->normalize_float($sub["rowAmount"]):"NULL",
                "rowZacautPtor"     => (isset($sub["rowZacautPtor"]) && $sub["rowZacautPtor"] !== "notSet")?$sub["rowZacautPtor"]:"",
                "rowZacautApproove" => (isset($sub["rowZacautApproove"]) && $sub["rowZacautApproove"] !== "notSet")?$sub["rowZacautApproove"]:"",
                "rowStatIravon"     => (isset($sub["rowSateIravon"]) && $sub["rowSateIravon"] !== "notSet")?$sub["rowSateIravon"]:"",
                "rowDateClose"      => (isset($sub["rowDateClose"]) && $sub["rowDateClose"] !== "notSet")? $this->normalize_date($sub["rowDateClose"],"d/m/Y","Y-m-d") : "NULL",
                "rowCloseReason"    => (isset($sub["rowCloseReason"]) && $sub["rowCloseReason"] !== "notSet")?$sub["rowCloseReason"]:"",
                "rowResultTaken"    => (isset($sub["rowResultTaken"]) && $sub["rowResultTaken"] !== "notSet")?$sub["rowResultTaken"]:"",
                "rowOldFile"        => (isset($sub["rowOldFile"]) && $sub["rowOldFile"] !== "notSet")?$sub["rowOldFile"]:"",
                "rowOpenedInCourse" => (isset($sub["rowOpenedInCourse"]) && $sub["rowOpenedInCourse"] !== "notSet")?$sub["rowOpenedInCourse"]:"",
                "rowGniza"          => (isset($sub["rowGniza"]) && $sub["rowGniza"] !== "notSet")?$sub["rowGniza"]:"",
                "rowReasonDeposit"  => (isset($sub["rowReasonDeposit"]) && $sub["rowReasonDeposit"] !== "notSet")?$sub["rowReasonDeposit"]:"",
                "rowTypeJudgeType"  => (isset($sub["rowTypeJudgeType"]) && $sub["rowTypeJudgeType"] !== "notSet")?$sub["rowTypeJudgeType"]:"",
                "rowJudgeTypeDate"  => (isset($sub["rowJudgeTypeDate"]) && $sub["rowJudgeTypeDate"] !== "notSet")? $this->normalize_date($sub["rowJudgeTypeDate"],"d/m/Y","Y-m-d") : "NULL",
                "rowJudgeTypeName"  => (isset($sub["rowJudgeTypeName"]) && $sub["rowJudgeTypeName"] !== "notSet")? $sub["rowJudgeTypeName"]:"NULL",
                "rowGishurType"     => (isset($sub["rowGishurType"]) && $sub["rowGishurType"] !== "notSet") ? $sub["rowGishurType"]:"",
                "rowGishurDetails"  => (isset($sub["rowGishurDetails"]) && $sub["rowGishurDetails"] !== "notSet") ? $sub["rowGishurDetails"]:""
            );
            $link->insert_safe("casesubrows", $row);
        }
        return $i;
    }   
}

/*
 * 
Get main and sub row:
SELECT 
  `casemainrows`.*, 
  `casesubrows`.*, 
  `all_judges`.`	judge_only_name` ,
  `courts`.`courtPlace`
FROM `casemainrows` 
LEFT JOIN `casesubrows` ON `casemainrows`.`id` = `casesubrows`.`ofMainRow`
LEFT JOIN `all_judges` ON `casesubrows`.`rowJudgeTypeName` = `all_judges`.`judge_full_name`
LEFT JOIN `courts` ON `casesubrows`.`rowCourt` = `courts`.`courtName`
WHERE `casemainrows`.`fullCaseNumber` = '20-11-15'
 * 
 * 
 * 
 * 
 */