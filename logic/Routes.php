<?php
/**
 * PHOP - Routing functions
 */

if ( !defined('PHOP') )
   exit;

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

function fail($msg, $code)
{
   debug('Response', "Failing with $code, $msg");
   header("Status: $msg", true, $code);

   if ($print) echo "Error $code: $msg";
   exit(1);
}

?>