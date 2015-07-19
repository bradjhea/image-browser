<?php

include './classes/errorHandler.php';
include './classes/structureHandler.php';
include './classes/structureReader.php';
include './classes/factory.php';
include './applicationSettings.php';

/**
 * Treat this page as the main controller for
 * any requests to the applciation
 */
class router {

    const ACTION_KEY = "action";

    private $currentAction = null;
    public $settings       = null;

    public function __construct() {
        $this->setSettings();
        $this->setAction();
    }

    public function setSettings() {
        $this->settings = new applicationSettings();
    }

    public function getSettings() {
        return $this->settings;
    }

    public function setAction() {
        $this->currentAction = filter_input(INPUT_GET, self::ACTION_KEY);
    }

    public function getAction() {
        return $this->currentAction;
    }

    /**
     * Create a switch statement
     * which load different features based on the pages action
     */
    public function initialise() {

        $output = "";

        switch ($this->getAction()) {

            case "view":
                $output = factory::loadView($this->settings->folderTarget, $this->settings->mapFile);
                break;

            case "read":
                $output = factory::loadChapterMap($this->settings->mapFile);
                break;

            case "synchronise":
                $output = factory::synchroniseFiles($this->settings->folderTarget, $this->settings->mapFile);
                break;

            case "previewAll":
                $output = factory::previewFiles($this->settings->mapFile, $this->settings->coverFile);
                break;

            default:
                $output = $this->settings->errorCodes->error_message_1;
                break;
        }
        echo $output;
    }

    public function __destruct() {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
    }

}

$router = new router();
$router->initialise();
exit;
