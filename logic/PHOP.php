<?php
/**
 * PHOP - Common functions and data
 */
error_reporting(E_ALL);

/**
 * PHOP's configuration, kept in a container object
 */
class Config
{
   const Caching = true;
   const Logging = true;
   const LogFile = 'log.txt';

   const PublicUrl = 'http://localhost:8888/';

   static $RemotePaths = [
      "http://awcommunity.org/romperroom/",
      "http://aw.platform3d.com/multipath/"
   ];

   static $AssetDirectories = [
      "models",
      "textures",
   ];
}


/**
 * Enumerations
 */

/**
 * Enumeration of common directories for PHOP
 */
class Directories
{
   const Templates = 'templates';
   const Prims     = 'prims';
   const Views     = 'views';
   const Stats     = 'stats';
}

/**
 * Enumeration of common error types used by PHOP
 */
class Errors
{
   const BadRequest   = 'Bad request';
   const BadDirectory = 'Invalid directory';
   const NotFound     = 'Asset not found';
   const NotKnown     = 'Unknown route';
   const Unknown      = 'Unknown server error';
}

/**
 * Enumeration of common regexes for use within PHOP
 */
class Regexes
{
   const AssetRequest = '{^/?(?<dir>[a-z]+)(/(?<file>.+))?$}i';
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

/**
 * Helper function for .NET style of regex match arrays
 *
 * @param  array  $matches Regex matches
 * @param  string $key     Key to fetch in the array
 * @return string A string of the given key in the matches, else blank string
 */
function matchOrBlank(array $matches, $key)
{
   if ( !isset($matches[$key]) )
      return '';
   else
      return $matches[$key];
}

?>