<?php

class factory {

    /**
     * 
     * @param type $targetFolder
     * @param type $mapFile
     * @return type
     */
    static function loadView($targetFolder, $mapFile) {

        $structureHandler = new structureHandler($targetFolder, $mapFile);
        $image            = $structureHandler->getFetchedImage();
        return json_encode(array($image));
    }

    static function loadChapterMap($mapFile) {

        $mapContents = json_decode(file_get_contents($mapFile));
        $currentBook = "";
        $bookMap     = array();

        foreach ($mapContents as $key => $row) {

            if ($currentBook != $key) {
                $currentBook = $key;

                foreach ($row as $subKey => $subRow) {
                    $bookMap[$currentBook] = "#view|$currentBook|$subKey|0";
                    break;
                }
            }
        }
        return json_encode($bookMap);
    }

    static function synchroniseFiles($targetFolder, $mapFile) {
        $structureHandler = new structureHandler($targetFolder, $mapFile);
        $syncStatus = ($structureHandler->synchronise($targetFolder)) ? true : false;
        
        return json_encode(array("synchroniseSuccessful" => $syncStatus));
    }

    static function previewFiles($mapFile, $coverFile) {
        $mapContents   = json_decode(file_get_contents($mapFile));
        $currentBook   = "";
        $previewObject = array();

        foreach ($mapContents as $mapKey => $mapRow) {

            if ($currentBook !== $mapKey) {
                $currentBook                 = $mapKey;
                $previewObject[$currentBook] = array();
            }

            $mapRow;
            foreach ($mapRow as $subKey => $subMapRow) {
                $previewObject[$currentBook]["chapters"][] = $subKey;
            }
            $previewObject[$currentBook]["settings"]["cover"] = $coverFile;
        }

        return json_encode($previewObject);
    }

}
