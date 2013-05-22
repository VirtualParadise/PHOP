<?php
/**
 * PHOP - Main application and routing script
 */

define('PHOP', .008);
require_once 'logic/PHOP.php';
require_once 'logic/Debug.php';
require_once 'logic/LocalStorage.php';
require_once 'logic/Routes.php';
require_once 'logic/Views.php';

function main()
{
   debug('PHOP', PHOP);

   // Routes: Default and asset requests
   if ( !isset($_GET['action']) )
   {
      if ( !isset($_GET['q']) || empty($_GET['q']) )
         routeDefault();
      else
         routeRequest();
   }

}

/**
 * Routes incoming client to the default page
 */
function routeDefault()
{
   gotoView(Views::Main);
}

/**
 * Routes incoming client to an asset request
 */
function routeRequest()
{
   $query = trim($_GET['q']);

   // Break down request into parts
   $match = preg_match(Regexes::AssetRequest, $query, $matches);
   $dir   = matchOrBlank($matches, 'dir');
   $file  = matchOrBlank($matches, 'file');

   debug('Incoming', "Query: $query, Dir: $dir, File: $file");

   if ( $match === 0 || $match === false )
      gotoError(500, Errors::BadRequest);

   // Reject invalid directories
   if ( !isDirectory($dir) )
      gotoError(500, Errors::BadDirectory);

   // Redirect directory requests
   if ( empty($file) )
      gotoUrl("phop.indexer.php?q=$dir");

   // If plugin token found, redirect to plugins
   if (strpos($file, ':') !== false)
      getPlugin($dir, $file);

   // Check for cached file
   if (CACHING && isLocalFile($dir, $file))
      gotoFile($dir, $file);

   // No success? Fetch remotely and cache...
   $fileData;
   if (getRemoteFile($Q, $fileData) === true)
   {
      cacheFile($dir, $file, $fileData);
      gotoFile($dir, $file);
   }
   else
      fail("Not found", 404);

}

/*
 * FETCH FUNCTIONS
 */

function getPlugin($dir, $file)
{
   $parts  = explode(":", $file, 2);
   $plugin = $parts[0];
   $data   = $parts[1];

   debug('Plugin', "Requested: $plugin, Data: $data");
   switch ($plugin) {
      case "i":
         return gotoUrl("http://imgur.com/$data");
      case "p":
         $data = str_replace('.zip','',$data);
         return gotoUrl("phop.prim.php?q=$data");
   }

   fail("Invalid plugin specified: $plugin", 400);
}

function getRemoteFile($req, &$fileData) {
   global $RemotePaths;

   foreach ($RemotePaths as $path)
   {
      debug('Remote', "Fetching $path$req");
      $c = curl_init($path . $req);
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

      $fileData = curl_exec($c);
      $code     = curl_getinfo($c, CURLINFO_HTTP_CODE);
      $errNo    = curl_errno($c);
      $err      = curl_error($c);
      curl_close($c);

      if ($errNo != 0) {
         debug('Remote', "Curl error retriving $path$req : $err (code $errNo)");
         continue;
      } else if ($code == 404 || $code == 403) {
         debug('Remote', "HTTP error retriving $path$req : $code");
         continue;
      }

      if ($fileData !== false)
         return true;
   }

   debug('Remote', "No remote sources had $req");
   return false;
}

main();
?>