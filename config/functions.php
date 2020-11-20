<?php

/**
 * Debug function
 * d($var);
 */
function d($var,$caller=null)
{
    if (!isset($caller)) {
        $debug = debug_backtrace(1);
        $caller = array_shift($debug);
    }
    echo '<code>File: ' . $caller['file'] . ' / Line: ' . $caller['line'] . '</code>';
    echo '<pre>';
    yii\helpers\VarDumper::dump($var, 10, true);
    echo '</pre>';
}

/**
 * Debug function with die() after
 * dd($var);
 */
function dd($var)
{
    $debug = debug_backtrace(1);
    $caller = array_shift($debug);
    d($var, $caller);
    die();
}