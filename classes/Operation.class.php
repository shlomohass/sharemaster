<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Operation {
    
    public $sys_filename_cache = ""; 
    
    public function __construct() {
        Trace::add_trace('construct class',__METHOD__);  
    }
    
    
    public function get_allowed_ext() {
        
    }
    
    public function set_allowed_ext() {
        
    }
    
    /* 
     * Uploads A file To Storage Folder
     * Will generate and cache new file name.
     *
     * @param string $tempFile
     * @param string $targetFile
     * @param string $storeFolder
     * @return boolean
     *
     */
    public function upload_file($tempFile, $targetFile, $storeFolder) {
        $targetPath = $storeFolder.DS; 
        if (!move_uploaded_file($tempFile, $targetPath.$targetFile)) {
            return false;
        }
        $this->sys_filename_cache = $targetFile;
        return true;
    }
    
    public function register_file_db() {
        
    }
    
    public function validate_file($targetFile, $fileSize) {
        
    }
    
    /*
     *
     */
}