<?php
// PHOP - Roy Curtis
//
// COMMENT LINE BELOW WHEN DONE TESTING
require_once('phop.lib.php');

function main() {
   debug('PHOP', 'v.006');
   $Q = $_GET['q'];
   $parts = explode("/", $Q, 2);
   $dir = $parts[0];
   $file = $parts[1];

   debug('Incoming', "Query: $q, Dir: $dir, File: $file");
   // Reject invalid directories
   if (!is_dir($dir))
      return fail("Invalid directory", 400);

   // Redirect directory requests
   if (empty($file))
      return gotoUrl("phop.indexer.php?q=$dir");

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
         return gotoUrl("phop.prim.php?q=$data");
   }

   fail("Invalid plugin specified: $plugin", 400);
}

function getRemoteFile($req, &$fileData) {
   global $REMOTE_PATHS;

   foreach ($REMOTE_PATHS as $path) {
      debug('Remote', "Fetching $req from ".SOURCE);
      $c = curl_init(SOURCE . $req);
      curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

      $fileData = curl_exec($c);
      curl_close($c);

      if ($fileData !== false)
         return true;
   }

   debug('Remote', "No remote sources had $req");
   return false;
}

main();
?>