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
        
        case "tableuser":
            $get = $Api->Func->synth($_REQUEST, array('draw','columns','order','start','length','search'),false);
            $get["order"] = (isset($_REQUEST["order"]) && is_array($_REQUEST["order"]))?$_REQUEST["order"]:"";
            $get["search"] = (isset($_REQUEST["search"]) && is_array($_REQUEST["search"]))?$_REQUEST["search"]:"";
            $columns = array( 
                // datatable column index  => database column name
                0 => 'username', 
                1 => 'seen',
                2 => 'created',
                3 => 'last_seen',
                4 => 'email'
            );
            
            if (empty($get['order'])) {
                $get['order'] = array(
                    array( "column" => 0, "dir" => "asc")
                );
            }
            $totalData = $Api::$conn->num_rows("SELECT * FROM `users`");
            $totalFiltered = $totalData;
            $where = false;
            if( !empty($get['search']['value']) ) {
               $where = array();
               foreach($columns as $col) {
                   $where[] = "`".$col."` LIKE '".$Api::$conn->filter($get['search']['value'])."%' ";
               }
               $where = implode("OR ", $where);
            }
            $data = $Api::$conn->select(
                    'users', 
                    " `username`,`seen`,`created`,`last_seen`,`email` ",
                    $where,
                    false,
                    array(
                        $get['order'][0]['dir'],
                        array($columns[$get['order'][0]['column']])
                    ),
                    array(
                        $get['start'],
                        $get['length']
                    )
                );
            $final_data = array();
            if (is_array($data)) {
                foreach ($data as $key => $duser) {
                    $final_data[] = array(
                        $duser['username'],
                        $duser['seen'],
                        $duser['created'],
                        $duser['last_seen'],
                        $duser['email']
                    );
                }
            } else {
                //Error:
            }
            $results = array(
                "draw"            => intval( $get['draw'] ), 
                "recordsTotal"    => intval( $totalData ),  // total number of records
                "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data"            => $final_data   // total data array
			);
            echo json_encode($results); 
            die();
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