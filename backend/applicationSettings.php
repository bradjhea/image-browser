<?php

class applicationSettings {

    public $mapFile      = "map.json";
    public $coverFile    = "cover.jpg";
    public $folderTarget = "images/";
    public $errorCodes   = null;

    public function __construct() {
        $this->setErrorCodes();
        if(file_exists('./config.php')) {
            include './config.php';
            config::Create($this);            
        }
    }

    public function setErrorCodes() {

        $errorCodes = new \stdClass();

        $errorCodes->error_message_1 = "Error: Could not find action";
        $errorCodes->error_message_2 = "Error: Could not find action value";
        $errorCodes->error_message_3 = "Error: Action value not valid";
        $errorCodes->error_message_4 = "Error: Test";

        $this->errorCodes = $errorCodes;
    }

}
