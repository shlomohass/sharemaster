<?php
/******************************************************************************/
// Created by: shlomo hassid.
// Release Version : 1.2
// Creation Date: 19/10/2015
// Copyright 2013, shlomo hassid.
/******************************************************************************/

class Api extends Basic {
        
    //Vars General For Games
    private $err_letter = "E";
    private $suc_letter = "S";
    private $code_spacer = ":";
    private $err_codes = array(
        "general"       => "01",
        "not-secure"    => "02",
        "bad-who"       => "03",
        "query"         => "04",
        "empty-results" => "05",
        "results-false" => "06",
        "not-loged"     => "07",
        "not-legal"     => "08",
        "no-plan"       => "09",
        "dir-create"    => "10",
        "session"       => "11",
        "no-sess-dir"   => "12",
        "bad-connection" => "13"
    );
    private $suc_codes = array(
        "general"       => "01",
        "with-results"  => "02"
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
    public function response($success,$results) {
        if (is_array($results) && !empty($results)) {
            $this->success($success,false);
            echo json_encode($results);
        } else {
            $this->success($success);
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
        if ($die) {
            die(
                $this->err_letter.
                $this->code_spacer.
                $this->err_codes[$type]
            );
        } else {
            echo $this->err_letter.$this->code_spacer.$this->err_codes[$type];
        }
    }
    /** Output defined success codes:
     * 
     *  @param string $type : code name,
     *  @param bool $die : dye or echo
     *  
     */
    public function success($type, $die = true) {
        if (!isset($this->suc_codes[$type])) {  $type = 'general'; }
        if (!is_bool($die)) { $die = true; }
        if ($die) {
            die(
                $this->suc_letter.
                $this->code_spacer.
                $this->suc_codes[$type]
            );
        } else {
            echo $this->suc_letter.$this->code_spacer.$this->suc_codes[$type];
        } 
    }
        
}