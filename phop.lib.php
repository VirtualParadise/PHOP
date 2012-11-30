<?php
// PHOP common functions - Roy Curtis
//
// COMMENT LINE BELOW WHEN DONE TESTING
error_reporting(0);
// Paths
define("SOURCE", "http://objects.activeworlds.com");
define("LOCAL", "http://objectpath.org/op/");
// Directories
define("TEMPLATES", "templates/");
define("PRIMS", "prims/");
// Debug
define("SAPI", php_sapi_name());
define("CLI", 'cli-server');

/**
 * Writes message to PHP CLI console, useful for webserver
 */
function debug($tag, $msg) {
   if (SAPI == CLI) error_log("[$tag] $msg");
}

/*
 * CORE FUNCTIONS
 */

function e($var) { return empty($var); }
function pick($a, $b) { return e($a) ? $b : $a; }
function pickNum($a, $b) {
   if (e($a))
      return $b;
   else if (!is_numeric($a))
      return fail("Parameter not a number", 400);
   else
      return $a;
}

/*
 * FETCH FUNCTIONS
 */

/**
 * Checks if file is available locally
 * @return bool       true if exists, false if not
 */
function getLocalFile($dir, $name) {
   $path = realpath("$dir/$name");
   return file_exists($path);
}

/*
 * CACHE FUNCTIONS
 */

function cacheFile($dir, $file, $data) {
   debug('Cache', "Saving $file to $dir cache");
   file_put_contents("$dir/$file", $data);
}

/*
 * RESPONSE FUNCTIONS
 */

function gotoFile($dir, $file) { gotoUrl("$dir/$file"); }
function gotoUrl($url) {
   debug('Response', "Redirecting to $url");
   header("HTTP/1.1 301 Moved Permanently", true, 301);
   header("Location: $url", true);
   exit;
}

function fail($msg, $code) {
   debug('Response', "Failing with $code, $msg");
   header("Status: $msg", true, $code);
   exit(1);
}

main();
?>