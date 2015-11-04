<?php

class structureHandler {

    const KEY_ACTION        = "action";
    const KEY_FOLDER        = "folder";
    const KEY_SUB_FOLDER    = "sub_folder";
    const KEY_INDEX         = "index";
    const SYNCHRONISE_TIMER = 600;

    public $valueAction       = null;
    public $valueFolder       = null;
    public $valueSubFolder    = null;
    public $valueIndex        = null;
    public $isImageSearchable = false;
    public $coverFile         = 'cover.jpg';
    public $folderStructure   = null;
    private $mapFile          = null;
    private $fetchedImage     = null;

    public function __construct($folderTarget, $mapFile) {

        // return content as JSON
        header('Content-Type: application/json');

        $this->valueAction    = filter_input(INPUT_GET, self::KEY_ACTION);
        $this->valueFolder    = filter_input(INPUT_GET, self::KEY_FOLDER);
        $this->valueSubFolder = filter_input(INPUT_GET, self::KEY_SUB_FOLDER);
        $this->valueIndex     = filter_input(INPUT_GET, self::KEY_INDEX);

        $this->isImageSearchable = (
                !is_null($this->valueAction) &&
                !is_null($this->valueFolder) &&
                !is_null($this->valueSubFolder) &&
                !is_null($this->valueIndex)
                );

        $this->setMapFile($mapFile);

        // if image is searchable generate folder structure
        //if ($this->isImageSearchable && file_exists($folderTarget)) {
        
        
        
        if (file_exists($folderTarget)) {
            $this->setDirectoryStructure($folderTarget);
        } else {
            echo "Could not find folder: " . $folderTarget;
        }

        $this->setFetchedImage();
    }

    public function setMapFile($mapFile) {

        if (!file_exists($mapFile)) {
            touch($mapFile);
        }

        $this->mapFile = $mapFile;
    }

    public function getMapFile() {
        return $this->mapFile;
    }

    public function getDirectoryStructure() {


        return $this->folderStructure;
    }

    /*
     * 
     * @param type $filename
     * @param type $timeLimit
     * @return type
     */

    public function isFileRecentlyModified($filename, $timeLimit = 3600) {
        return (filemtime($filename) < (time() - $timeLimit));
    }

    /**
     * 
     * @param type $folder
     * @return type
     */
    public function setDirectoryStructure($folder) {

        $mapFileContents = json_decode(file_get_contents($this->getMapFile()));

        // read file structure if file has not been altered recently
        if (!is_null($mapFileContents) && !$this->isFileRecentlyModified($this->getMapFile(), self::SYNCHRONISE_TIMER)) {
            $this->folderStructure = $mapFileContents;
            return;
        }

        $structure = $this->readStructure($folder);

        file_put_contents($this->getMapFile(), json_encode($structure));
        $this->folderStructure = json_decode(file_get_contents($this->getMapFile()));
    }

    public function readStructure($folder) {

        // Create recursive dir iterator which skips dot folders
        $dir = new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS);

        // Flatten the recursive iterator, folders come before their files
        $it = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);

        // Maximum depth is 1 level deeper than the base folder
        $it->setMaxDepth(2);

        $folderMap      = array();
        $currentBook    = "";
        $currentChapter = "";
        $validFiles     = array("png", "jpeg", "jpg", "gif");

        // Basic loop displaying different messages based on file or folder
        foreach ($it as $fileinfo) {

            switch ($it->getDepth()) {
                case 0:

                    if ($fileinfo->isDir() && $currentBook !== $fileinfo->getFilename()) {
                        $currentBook = $fileinfo->getFilename();
                    }

                    break;
                case 1:

                    if ($fileinfo->isDir() && $currentChapter !== $fileinfo->getFilename()) {
                        $currentChapter = $fileinfo->getFilename();
                    }

                    break;
                case 2:

                    if ($fileinfo->isFile() && in_array($fileinfo->getExtension(), $validFiles)) {
                        $folderMap[$currentBook][$currentChapter][] = $fileinfo->getFilename();
                    }

                    break;
                default:
                    break;
            }
        }

        return $folderMap;
    }

    public function synchronise($folder) {
        $structure = $this->readStructure($folder);
        return file_put_contents($this->getMapFile(), json_encode($structure));
    }

    /**
     * Return an array of current, previous and next images
     * @return type
     */
    public function setFetchedImage() {

        $imageMap = array(
            "firstPageHash"   => "",
            "previousHash"    => "",
            "currentImage"    => "",
            "nextHash"        => "",
            "lastPageHash"    => "",
            "chapterCount"    => 0,
            "chapterPrevious" => "",
            "chapterNext"     => "",
        );

        if (!$this->isImageSearchable) {
            $this->fetchedImage = $imageMap;
            return;
        }

        if (!isset($this->folderStructure->{$this->valueFolder}) ||
                !isset($this->folderStructure->{$this->valueFolder}->{$this->valueSubFolder}) ||
                !isset($this->folderStructure->{$this->valueFolder}->{$this->valueSubFolder}[$this->valueIndex])) {
            $this->fetchedImage = $imageMap;
            return;
        }

        $previousHash        = "";
        $previousChapterHash = "";

        // iterate through sub directory
        foreach ($this->folderStructure->{$this->valueFolder} as $keySubFolder => $subFolder) {

            // $previousChapterHash = $this->getLinkHash($this->valueAction, $this->valueFolder, $keySubFolder, "0");
            // echo 'Key sub-folder: ' . $keySubFolder . PHP_EOL;

            if ($imageMap["chapterPrevious"] && !$imageMap["chapterNext"]) {
                $imageMap["chapterNext"] = $this->getLinkHash($this->valueAction, $this->valueFolder, $keySubFolder, "0");
            }

            //$previousChapterHash = $this->getLinkHash($this->valueAction, $this->valueFolder, $keySubFolder, "0");

            foreach ($subFolder as $subSubKey => $subSubFolder) {

                // create previous chapter here
                if ($this->valueSubFolder == $keySubFolder && !$imageMap["chapterPrevious"]) {
                    $imageMap["chapterPrevious"] = $previousChapterHash;
                }

                if ($this->valueSubFolder == $keySubFolder && !$imageMap["firstPageHash"]) {
                    $imageMap["firstPageHash"] = $this->getLinkHash($this->valueAction, $this->valueFolder, $keySubFolder, $subSubKey);
                }

                if ($imageMap["currentImage"] !== "" && $imageMap["nextHash"] == "") {
                    $imageMap["nextHash"] = $this->getLinkHash($this->valueAction, $this->valueFolder, $keySubFolder, $subSubKey);
                }

                if ($this->valueSubFolder == $keySubFolder && $this->valueIndex == $subSubKey) {
                    $imageMap["currentImage"] = str_replace("+", "%20", urlencode($subSubFolder));
                    $imageMap["chapterCount"] = count($subFolder);
                }

                if ($imageMap["currentImage"] !== "" && $imageMap["previousHash"] == "") {
                    $imageMap["previousHash"] = $previousHash;
                }

                if ($this->valueSubFolder == $keySubFolder) {
                    $imageMap["lastPageHash"] = $this->getLinkHash($this->valueAction, $this->valueFolder, $keySubFolder, $subSubKey);
                }

                $previousHash        = $this->getLinkHash($this->valueAction, $this->valueFolder, $keySubFolder, $subSubKey);
                $previousChapterHash = $this->getLinkHash($this->valueAction, $this->valueFolder, $keySubFolder, "0");

                // create next chapter here
                // if previous chapter has been set, then set next
            }
        }

        $this->fetchedImage = $imageMap;
    }

    public function getLinkHash($action, $rootFolder, $childFolder, $index) {
        return "#$action|$rootFolder|$childFolder|$index";
    }

    public function getFetchedImage() {
        return $this->fetchedImage;
    }

}
