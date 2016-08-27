<?php
/******************************************************************************/
// Created by: shlomo hassid.
// Release Version : 1.2
// Creation Date: 19/10/2015
// Copyright 2013, shlomo hassid.
/******************************************************************************/

class Api extends Basic {
        
    //Vars General For Games
    private $err_letter = "code";
    private $suc_letter = "code";
    private $code_spacer = ":";
    private $err_codes = array(
        "general"       => "101",
        "not-secure"    => "102",
        "bad-who"       => "103",
        "query"         => "104",
        "empty-results" => "105",
        "results-false" => "106",
        "not-loged"     => "107",
        "not-legal"     => "108",
        "no-file"       => "109",
        "copy-file"     => "110",
    );
    private $suc_codes = array(
        "general"       => "201",
        "with-results"  => "202"
    );

    /** Constructor
     * 
     *  @param array $conf
     * 
     */
    public function __construct( $conf ) {  
        parent::__construct( $conf );
        Trace::add_trace('construct class',__METHOD__);
    }

    /** Response construct:
     * 
     * @param array|bool $results
     * @return array : not empty or false.
     */
    public function response($success, $results = false) {
        if (is_array($results) && !empty($results)) {
            $this->success($success, $results, true);
        } else {
            $this->success($success, array(), true); 
        }
    }
    /** Output defined error codes:
     * 
     *  @param string $type : code name,
     *  @param bool $die : dye or echo
     *  
     */
    public function error($type = 'general', $die = true) {
        if (!isset($this->err_codes[$type])) {  $type = 'general'; }
        if (!is_bool($die)) { $die = true; }
        $err = array(
            "code" => $this->err_codes[$type],
            "mes"  => $type
        );
        if ($die) {
            die(json_encode($err));
        } else {
            echo json_encode($err);
        }
    }
    /** Output defined success codes:
     * 
     *  @param string $type : code name,
     *  @param bool $die : dye or echo
     *  
     */
    public function success($type, $result, $die = true) {
        if (!isset($this->suc_codes[$type])) {  $type = 'general'; }
        if (!is_bool($die)) { $die = true; }
        $res = array(
            "code" => $this->suc_codes[$type],
            "mes"  => $type,
            "results" => $result
        );
        if ($die) {
            die(json_encode($res));
        } else {
            echo json_encode($res);
        } 
    }
        
}