<?php
/**
 * PHOP - Common functions and data
 */
error_reporting(E_ALL);

/**
 * Configuration
 */

// PHOP
define('PHOP', .008);

// Debug
define('CACHING', true);
define('LOGGING', false);
define('LOGFILE', 'log.txt');

// Remote paths
$RemotePaths = Array(
   "http://awcommunity.org/romperroom/",
   "http://aw.platform3d.com/multipath/"
);

$AssetDirectories = Array(
   "models",
   "textures"
);

/**
 * Enumerations
 */

// Directories
class Directories
{
   const Templates = 'templates';
   const Prims     = 'prims';
   const Views     = 'views';
   const Stats     = 'stats';
}

// Regexes
class Regexes
{
   const AssetRequest = '{/?(?<dir>[a-z]+)(/(?<file>.+))?}i';
}

/*
 * Utility functions
 */

function pick($a, $b) { return empty($a) ? $b : $a; }
function pickNum($a, $b)
{
   if      ( empty($a) )
      return $b;
   else if ( !is_numeric($a) )
      return fail("Parameter not a number", 400);
   else
      return $a;
}

/**
 * Joins a given array of strings into a relative path
 *
 * @param  array   $entries Parts of a path, including files and folders
 * @return string  Sanitized relative path
 */
function pathJoin(array $entries)
{
   foreach ($entries as &$entry)
      $entry = preg_replace('(\\\\|/)', '', $entry);

   return implode('/', $entries);
}

?>