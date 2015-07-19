<?php

function exceptions_error_handler($severity, $message, $filename, $lineno) {
    echo getStackTrace();
}

function getStackTrace() {
    $returnVar  = "";
    $debugArray = debug_backtrace();

    foreach ($debugArray as $line) {
        if ($line['function'] != "trigger_error") {
            $lineNum  = (isset($line['line'])) ? $line['line'] : "";
            $fileName = (isset($line['file'])) ? $line['file'] . ":" : "unknown";
            $function = $line['function'];
            if ($function == "handleError" || $function == "getStackTrace" || $function == "getDebugOutput")
                continue;
            $returnVar .= $fileName . $lineNum . " " . $function . "(";
            if (isset($line['args']) && is_array($line['args'])) {
                foreach ($line['args'] as $arg) {
                    if (is_string($arg)) {
                        $returnVar .= '"' . $arg . '", ';
                    }
                }
                $returnVar = trim($returnVar, ', ');
            }
            $returnVar .= ") " . PHP_EOL . PHP_EOL . "<br/>";
        }
    }
    if (!empty($returnVar)) {
        $returnVar = "STACK TRACE:<br/>" . PHP_EOL . $returnVar;
        $returnVar .= PHP_EOL . PHP_EOL . "<br/>";
    }

    $returnVar = utf8_encode($returnVar);
    return "<div style='background: #fff; font-family: courier; font-weight: normal'>" . $returnVar . "</div>";
}

set_error_handler('exceptions_error_handler');