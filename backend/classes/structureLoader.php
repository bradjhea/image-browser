<?php

include './structureHandler.php';
include './errorHandler.php';

$folderTarget     = "D:/xampp.5.4.27/htdocs/workbench/imageBrowser/images/";
$structureHandler = new structureHandler($folderTarget);
$structure        = $structureHandler->getDirectoryStructure();
$mapFile          = "map.json";

touch("map.json");
file_put_contents("map.json", json_encode(array($structure)));

