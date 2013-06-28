<?php
/**
 * PHOP : Main application entry point
 */

// Autoloader for Composer modules such as ReactPHP
require 'vendor/autoload.php';

// PHOP source layout
require_once 'Source\PHOP.php';

define('PHOP', .008);

function main()
{
   debug('', "\n\n");
   debug('PHOP', PHOP);
   $action = getOrBlank($_GET, KeyAction);
   $query  = getOrBlank($_GET, KeyQuery);

   switch ($action)
   {
      case '':
         if ( empty($query) )
            routeDefault();
         else
            routeRequest($query);
         break;

      case Actions::Index:
         routeIndex($query);
         break;

      default:
         gotoError(500, Errors::BadAction);
         break;
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
 *
 * @param string $query Query string to work on
 */
function routeRequest($query)
{
   $query = trim($query);

   // Break down request into parts
   $match = preg_match(Regexes::AssetRequest, $query, $matches);
   $dir   = getOrBlank($matches, 'dir');
   $file  = getOrBlank($matches, 'file');

   debug('Requests', "Query: $query, Dir: $dir, File: $file...");

   // Reject invalid requests
   if ( $match === 0 || $match === false )
   {
      debug('Requests', 'Bad request; rejecting with 500' );
      gotoError(500, Errors::BadRequest);
   }

   // Reject invalid directories
   if ( !isDirectory($dir) )
   {
      debug('Requests', 'Bad directory request; rejecting with 500' );
      gotoError(500, Errors::BadDirectory);
   }

   // Check for cached or locally available file
   if ( isLocalFile($dir, $file) )
   {
      debug('Requests', 'Request is in local storage; sending to client' );
      gotoFile($dir, $file);
   }

   // Check for plugins which handle request
   loadPlugins();
   foreach (Plugin::$Loaded as $plugin)
      if ( $plugin->handleRequest($dir, $file) )
      {
         $pluginName = $plugin->getName();
         debug('Requests', "Plugin '$pluginName' was able to handle the request; exiting" );
         exit;
      }

   // Check for remotely available file
   if ( getRemoteFile($dir, $file, $data, $size) )
   {
      debug('Requests', 'Request was available remotely; sending to client' );

      if (Config::Caching)
      {
         cacheFile($dir, $file, $data);
         gotoFile($dir, $file);
      }
      else
         gotoData($file, $data, $size);
   }

   // Finally, fail with 404
   gotoError(404, Errors::NotFound);
}

function routeIndex($query)
{
   $query = trim($query);

   // Break down request into parts
   $match = preg_match(Regexes::DirRequest, $query, $matches);
   $dir   = getOrBlank($matches, 'dir');

   // Reject invalid requests
   if ( $match === 0 || $match === false )
   {
      debug('Indexing', 'Bad request; rejecting with 500' );
      gotoError(500, Errors::BadRequest);
   }

   // Reject invalid directories
   if ( !isDirectory($dir) )
   {
      debug('Indexing', 'Bad directory request; rejecting with 500' );
      gotoError(500, Errors::BadDirectory);
   }

   gotoIndex($dir);
}

main();
?>