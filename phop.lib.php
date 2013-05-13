<?php
// PHOP common functions - Roy Curtis
//
// COMMENT LINE BELOW WHEN DONE TESTING
error_reporting(0);
// Directories
define("TEMPLATES", "templates/");
define("PRIMS", "prims/");
// Debug
define("SAPI", php_sapi_name());
define("CLI", 'cli-server');
define("CACHING", true);
define("LOGGING", false);
define("LOGFILE", 'log.txt');
// Remote paths
$REMOTE_PATHS = Array(
   "http://awcommunity.org/romperroom/",
   "http://aw.platform3d.com/multipath/");

/**
 * Writes message to PHP CLI console, useful for webserver
 */
function debug($tag, $msg) {
   if (SAPI == CLI)
      error_log("[$tag] $msg");

   if (LOGGING)
      file_put_contents(LOGFILE, "[$_SERVER[REMOTE_ADDR], $tag] $msg\n", FILE_APPEND);
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

function gotoFile($dir, $file) {
   debug('Response', "Returning file $file");

   $path   = "$dir/$file";
   $length = filesize ($path);
   //header("Connection: Keep-alive", true);
   header('Content-Type: application/octet-stream', true);
   header("Content-Length: $length", true);
   header("Content-Disposition: attachment; filename=\"$file\"", true);

   ob_clean();
   flush();
   readfile($path);
   exit;
}

function gotoUrl($url) {
   debug('Response', "Redirecting to $url");
   header("HTTP/1.1 301 Moved Permanently", true, 301);
   header("Location: $url", true);
   exit;
}

function fail($msg, $code, $print = false) {
   debug('Response', "Failing with $code, $msg");
   header("Status: $msg", true, $code);

   if ($print) echo "Error $code: $msg";
   exit(1);
}

main();
?>