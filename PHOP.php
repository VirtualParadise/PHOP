<?php
/**
 * PHOP : Main application entry point
 */

namespace PHOP;

// Autoloader for Composer modules such as ReactPHP
require 'vendor/autoload.php';

use PHOP\Utility\Log;
use React\EventLoop\Factory;
use React\Socket\Server as SocketServer;
use React\Http\Server as HttpServer;

/**
 * PHOP source layout
 */
require_once 'Source\PHOP.php';
require_once 'Source\Indexing.php';
require_once 'Source\LocalStorage.php';
require_once 'Source\RemoteStorage.php';
require_once 'Source\Routes.php';
require_once 'Source\Views.php';
require_once 'Source\Plugins.php';

require_once 'Source\Utility\Errors.php';
require_once 'Source\Utility\Logger.php';

require_once 'PHOP.Config.php';

/**
 * PHOP version constant
 */
define('PHOP', "1.0.0");

/**
 * Main entry point of PHOP server
 */



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


?>