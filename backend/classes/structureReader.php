<?php

class structureReader {
    
    private $mapFile = null;
    
    public function __construct($mapFile) {
        $this->setMapFile($mapFile);
    }

    public function setMapFile($mapFile) {
        
        if(!file_exists($mapFile)) {
            throw new Exception("Could not find map file");            
        }
        
        $this->mapFile = $mapFile;
    }
    
    public function getMapFile() {
        return $this->mapFile;
    }
    
}