<?php
/**
 * PHOP - Routing functions
 */

if ( !defined('PHOP') )
   exit;

/**
 * Determines if incoming client is VR software. Currently only detects Virtual Paradise
 *
 * @return bool true if VR client, else false (e.g. if browser)
 */
function isVRClient()
{
   $ua = $_SERVER['HTTP_USER_AGENT'];

   if ( strpos($ua, 'VirtualParadise') !== false )
      return true;
   else
      return false;
}

/**
 * Feeds a file to the requesting client
 *
 * @param  string $dir  Target directory
 * @param  string $file Target file
 * @return void   Exits execution
 */
function gotoFile($dir, $file)
{
   debug('Response', "Returning file $file");

   $path   = pathJoin([$dir, $file]);
   $length = filesize ($path);

   header("Connection: keep-alive", true);
   header('Content-Type: application/octet-stream', true);
   header("Content-Length: $length", true);
   header("Content-Disposition: attachment; filename=\"$file\"", true);

   ob_clean();
   flush();
   readfile($path);
   exit;
}

/**
 * Redirects the client to a given URL with a permanent redirect
 *
 * @param  string $url Target URL
 * @return void   Exits execution
 */
function gotoUrl($url)
{
   debug('Response', "Redirecting to $url");
   header("HTTP/1.1 301 Moved Permanently", true, 301);
   header("Location: $url", true);
   exit;
}

/**
 * Displays a page for the client
 *
 * @param  string $view View to load, with any given data
 * @return [type]       [description]
 */
function gotoView($view, array $data = [])
{
   debug('Response', "Generating view '$view'");

   $view = new View($view);
   $view->Generate($data);
   exit;
}

/**
 * Redirects incoming client to an error. If client is a VR, just sends a HTTP error code,
 * else displays an error view.
 *
 * @param int    $code  HTTP code to use
 * @param string $type  Type of error; use Errors enumeration
 * @param array  $extra Extra data to provide the view
 */
function gotoError($code, $type, $extra = [])
{
   debug('Response', "Failing with $code, type: $type");
   header("Status: $type", true, $code);

   if ( isVRClient() )
      exit;

   $data = [
      'code'  => $code,
      'type'  => $type,
      'extra' => $extra,
   ];

   $view = new View(Views::Error);
   $view->Title = "Error: $type - PHOP";
   $view->Generate($data);
   exit;
}

?>