<?php
/****************************** secure api include ***********************************/
if (!isset($conf)) { die("E:01"); }

/****************************** Build API ********************************************/
$Api = new api( $conf );

/****************************** Needed Values ****************************************/
$inputs = $Api->Func->synth($_POST, array('type'));

/****************************** Building response ***********************************/
$success = "general";
$results = false;

/****************************** API Logic  ***********************************/
if ( $inputs['type'] !== '' ) {
    
    switch (strtolower($inputs['type'])) {
        
        /**** Upload a file to storage with validation: ****/
        case "uploadfile":  
            
            if (empty($_FILES)) { $Api->error("no-file"); }
            
            //Synth needed:
            $get = array(
                "tempFile" => (isset($_FILES['file']) && isset($_FILES['file']['tmp_name'])) ? $_FILES['file']['tmp_name'] : false,
                "targetFile" => (isset($_FILES['file']) && isset($_FILES['file']['name'])) ? $_FILES['file']['name'] : false
            );
            //$get = $Api->Func->synth($_POST, array('dis_images','dis_css','ena_debugger','ena_outfile','ena_grout','ena_stamp','out_folder','timeout','loading_timeout','server','use_encoding','crawler_type','plan_name'),false);
            
            //Validation:
            if (
                    empty($get['tempFile'])
                ||  empty($get['targetFile'])
            ) {
                $Api->error("not-legal");
            }
            
            //Logic:
            $Op = new Operation();
            $res = $Op->upload_file($get['tempFile'], $get['targetFile'], $conf['storage_folder']);
            
            //Output:
            if ($res === true) {
               $results = array(
                    "fileId" => 2,
                    "filename" => $Op->sys_filename_cache,
                );
                $success = "with-results";
            } else {
                $Api->error("copy-file");
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
