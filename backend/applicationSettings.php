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

        $this->errorCodes = $errorCodes;
    }

}
