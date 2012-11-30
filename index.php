<?php
// PHOP - Roy Curtis
//
// COMMENT LINE BELOW WHEN DONE TESTING
require_once('phop.lib.php');

function main() {
   debug('PHOP', 'v.005');
   $Q = $_GET['q'];
   $parts = explode("/", $Q, 2);
   $dir = $parts[0];
   $file = $parts[1];

   debug('Incoming', "Query: $q, Dir: $dir, File: $file");
   // Reject empty directories
   if (empty($dir))
      return fail("Invalid directory", 400);

   // Redirect directory requests
   if (empty($file))
      return gotoUrl($dir);

   // If plugin token found, redirect to plugins
   if (strpos($file, ':') !== false)
      return getPlugin($dir, $file);

   // Check for cached file
   if (getLocalFile($dir, $file) === true)
      return gotoFile($dir, $file);

   // No success? Fetch remotely and cache...
   $fileData;
   if (getRemoteFile($Q, $fileData) === true) {
      cacheFile($dir, $file, $fileData);
      gotoFile($dir, $file);
   } else {
      fail("Not found", 404);
   }
}

/*
 * FETCH FUNCTIONS
 */

function getPlugin($dir, $file) {
   $parts = explode(":", $file, 2);
   $plugin = $parts[0];
   $data = $parts[1];

   debug('Plugin', "Requested: $plugin, Data: $data");
   switch ($plugin) {
      case "i":
         return gotoUrl("http://imgur.com/$data");
      case "p":
         $data = str_replace('.zip','',$data);
         return gotoUrl("prim.php?q=$data");
   }

   fail("Invalid plugin specified: $plugin", 400);
}

function getRemoteFile($req, &$fileData) {
   debug('Remote', "Fetching $req from ".SOURCE);
   $c = curl_init(SOURCE . $req);
   curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

   $fileData = curl_exec($c);
   $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
   curl_close($c);

   return ($code != 403 && $code != 404);
}

main();
?>