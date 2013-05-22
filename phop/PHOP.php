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
   const Logging = false;
   const LogFile = 'log.txt';

   const PublicUrl = 'http://localhost:8888/';

   static $RemotePaths = [
      "http://awcommunity.org/romperroom",
      "http://aw.platform3d.com/multipath"
   ];

   static $AssetDirectories = [
      "models",
      "textures",
   ];

   static $Plugins = [
      "prim"
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
   const PHOP    = 'phop';
   const Views   = 'phop/views';
   const Plugins = 'phop/plugins';
}

/**
 * Enumeration of actions PHOP can handle
 */
class Actions
{
   const Index = 'index';
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
   const AssetRequest = '{^/?(?<dir>[a-z]+)/(?<file>.+\.[a-z]+)$}i';
   const DirRequest   = '{^/?(?<dir>[a-z]+)\b}i';
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
 * @param  array  $entries Parts of a path, including files and folders
 * @return string Sanitized relative path
 */
function pathJoin(array $entries)
{
   foreach ($entries as &$entry)
      $entry = trim($entry, '/\\' );

   return implode('/', $entries);
}

/**
 * Helper function that returns blank strings rather than generate errors, if accessing
 * a non-existent key in a given array
 *
 * @param  array  $matches Array
 * @param  string $key     Key to fetch in the array
 * @return string A string of the given key in the matches, else blank string
 */
function getOrBlank(array $matches, $key)
{
   if ( !isset($matches[$key]) )
      return '';
   else
      return $matches[$key];
}
?>