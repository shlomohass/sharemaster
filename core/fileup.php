<?php

    $ds = DIRECTORY_SEPARATOR;
    $storeFolder = 'storage';
    $result = array(
        "code" => 200,
        "fileId" => 2,
        "mes" => "",
    );
    if (!empty($_FILES)) {
        $tempFile = $_FILES['file']['tmp_name'];           
        $targetPath = "..".$ds.$storeFolder.$ds; 
        $targetFile =  $targetPath. $_FILES['file']['name'];
        if (!move_uploaded_file($tempFile, $targetFile)) {
            $result["code"] = 300;
            $result["mes"] = "Error PHP: ".$targetFile;
        } else {
            $result["mes"] = "Success PHP: ".$targetFile;
        }
    }

    echo json_encode($result);
