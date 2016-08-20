<?php

class CrawlerANew {

    public $execPath = "";
    public $outFolder = "";
    public $userArgs = "";
    protected $disguiArg = "-g";
    public $execCode = null;
    public $failType = "";
    public $outBuffer = array();
    public $usedLocalCheck = false;
    protected $charsets = array(
        "utf8" => "UTF-8"
    );


    public function __construct($_execPath, $_outFolder = false, $_userArgs = false) {
        $this->execPath = $_execPath;
        $this->outFolder = $_outFolder;
        $this->userArgs = $_userArgs;
    }
    
    public function buildCommand() {
        return  $this->execPath." "
                .$this->disguiArg." "
                .($this->userArgs?$this->userArgs." ":"");
                
    }
    
    public function runCrawl($encode = false) {
        exec($this->buildCommand(), $this->outBuffer, $this->execCode);
        if ($encode) {
            $this->encode($encode);
        }
        return $this->execCode;
    }
    
    public function runCrawlProc($encode = false) {
        $descr = array(0=>array('pipe','r'),1=>array('pipe','w'),2=>array('pipe','w'));
        $pipes = array();
        $res = array();
        $process = proc_open($this->buildCommand(), $descr, $pipes);
        if (is_resource($process)) {
            while ($f = fgets($pipes[1])) {
                $res[] = $f;
            }
            fclose($pipes[1]);
            while ($f = fgets($pipes[2])) {
                $res[] = $f;
            }
            fclose($pipes[2]);
            proc_close($process);
        }
        $this->outBuffer = array( 0 => implode("", $res));
        $this->execCode = 0;
    }
    
    public function parseResult($tryLocal = false) {
        $this->failType = "";
        
        //First check execution return code:
        if ($this->execCode !== 0) { 
            $this->failType = "Internal error code: ".$this->execCode;
            return "fail"; 
        }
        
        //Check if there is data captured:
        $buffer = trim(implode("",$this->outBuffer));
        if ($buffer === "") { 
            //Try local?
            if ($tryLocal) {
                $localBuffer = @file_get_contents($tryLocal);
                $this->usedLocalCheck = true;
                if (is_string($localBuffer) && strlen($localBuffer) > 5) {
                    $buffer = trim($localBuffer);
                    $this->outBuffer = array( 0 => $buffer );
                } else {
                    $this->failType = "Empty result with local check";
                    return "fail"; 
                }
            } else {
                $this->failType = "Empty result without local check";
                return "fail";    
            }
        }
        
        //Parse errors:
        if (substr($buffer, 0, 5) === "ERROR") {
            $error_name = explode(":",$buffer);
            if (isset($error_name[1]) && !empty($error_name[1])) {
                switch (trim(strtoupper($error_name[1]))) {
                    case "OPTIONS":
                    case "SERIALIZE":
                    case "ENCODING":
                    case "APPTIMER":
                    case "LOADINGTIMER":
                        $this->failType = "Error message :".strtolower($error_name[1]);
                        return "fail";
                    case "PRIVATE":
                        return "priv";
                    case "NOCASE":
                        return "no";
                    default:
                        $this->failType = "Unknown error mes:".strtolower($error_name[1]);
                        return "fail";
                }
            } else {
                $this->failType = "Mal formatted error";
                return "fail";
            }
        }
        
        //Decode result:
        $decode = @json_decode($buffer, true);
        if (!empty($decode)) {
            return $decode;
        }
        //Incase Json failed:
        $this->failType = "JSON error detected";
        return "fail";
    }
    public function encode($charset) {
        if (isset($this->charsets[$charset])) {
            $temp = array();
            $skip = false;
            foreach($this->outBuffer as $index => $row) {
                $temp[$index] = iconv(
                    mb_detect_encoding($row, mb_detect_order(), true), 
                    $this->charsets[$charset]."//IGNORE", 
                    $row
                );
                if (!$temp[$index]) {
                   $skip = true;
                   break;
                }
            }
            if (!$skip) {
                $this->outBuffer = $temp;
                return true;
            }
        }
        return false;
    }
    
    public function win1255ToUtf8($str) {
        static $tbl = null;
        if (!$tbl) {
            $tbl = array_combine(range("\x80", "\xff"), array(
                "\xe2\x82\xac", "\xef\xbf\xbd", "\xe2\x80\x9a", "\xc6\x92",
                "\xe2\x80\x9e", "\xe2\x80\xa6", "\xe2\x80\xa0", "\xe2\x80\xa1",
                "\xcb\x86", "\xe2\x80\xb0", "\xef\xbf\xbd", "\xe2\x80\xb9",
                "\xef\xbf\xbd", "\xef\xbf\xbd", "\xef\xbf\xbd", "\xef\xbf\xbd",
                "\xef\xbf\xbd", "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c",
                "\xe2\x80\x9d", "\xe2\x80\xa2", "\xe2\x80\x93", "\xe2\x80\x94",
                "\xcb\x9c", "\xe2\x84\xa2", "\xef\xbf\xbd", "\xe2\x80\xba",
                "\xef\xbf\xbd", "\xef\xbf\xbd", "\xef\xbf\xbd", "\xef\xbf\xbd",
                "\xc2\xa0", "\xc2\xa1", "\xc2\xa2", "\xc2\xa3", "\xe2\x82\xaa",
                "\xc2\xa5", "\xc2\xa6", "\xc2\xa7", "\xc2\xa8", "\xc2\xa9",
                "\xc3\x97", "\xc2\xab", "\xc2\xac", "\xc2\xad", "\xc2\xae",
                "\xc2\xaf", "\xc2\xb0", "\xc2\xb1", "\xc2\xb2", "\xc2\xb3",
                "\xc2\xb4", "\xc2\xb5", "\xc2\xb6", "\xc2\xb7", "\xc2\xb8",
                "\xc2\xb9", "\xc3\xb7", "\xc2\xbb", "\xc2\xbc", "\xc2\xbd",
                "\xc2\xbe", "\xc2\xbf", "\xd6\xb0", "\xd6\xb1", "\xd6\xb2",
                "\xd6\xb3", "\xd6\xb4", "\xd6\xb5", "\xd6\xb6", "\xd6\xb7",
                "\xd6\xb8", "\xd6\xb9", "\xef\xbf\xbd", "\xd6\xbb", "\xd6\xbc",
                "\xd6\xbd", "\xd6\xbe", "\xd6\xbf", "\xd7\x80", "\xd7\x81",
                "\xd7\x82", "\xd7\x83", "\xd7\xb0", "\xd7\xb1", "\xd7\xb2",
                "\xd7\xb3", "\xd7\xb4", "\xef\xbf\xbd", "\xef\xbf\xbd",
                "\xef\xbf\xbd", "\xef\xbf\xbd", "\xef\xbf\xbd", "\xef\xbf\xbd",
                "\xef\xbf\xbd", "\xd7\x90", "\xd7\x91", "\xd7\x92", "\xd7\x93",
                "\xd7\x94", "\xd7\x95", "\xd7\x96", "\xd7\x97", "\xd7\x98",
                "\xd7\x99", "\xd7\x9a", "\xd7\x9b", "\xd7\x9c", "\xd7\x9d",
                "\xd7\x9e", "\xd7\x9f", "\xd7\xa0", "\xd7\xa1", "\xd7\xa2",
                "\xd7\xa3", "\xd7\xa4", "\xd7\xa5", "\xd7\xa6", "\xd7\xa7",
                "\xd7\xa8", "\xd7\xa9", "\xd7\xaa", "\xef\xbf\xbd", "\xef\xbf\xbd",
                "\xe2\x80\x8e", "\xe2\x80\x8f", "\xef\xbf\xbd",
            ));
        }
        return strtr($str, $tbl);
    }
}
