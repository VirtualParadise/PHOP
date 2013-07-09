<?php
/**
 * PHOP - Common functions and data
 */
date_default_timezone_set('UTC');


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
   const Nothing = 'nothing';
   const Index   = 'index';
}

/**
 * Enumeration of common error types used by PHOP
 */
class Errors
{
   const BadRequest   = 'Bad request';
   const BadDirectory = 'Invalid directory';
   const BadAction    = 'Unknown route/action';
   const NotFound     = 'Asset not found';
   const Unknown      = 'Unknown server error';
}

/**
 * Enumeration of common regexes for use within PHOP
 */
class Regexes
{
   const AssetRequest = '{^/?(?<dir>[a-z]+)/(?<file>.+\.[a-z]+)$}i';
   const DirRequest   = '{^/?(?<dir>[a-z]+)/?$}i';
}

/*
 * General constants
 */
define('KeyAction', 'action');
define('KeyQuery',  'q');

/*
 * Utility functions
 */

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

/**
 * Helper function that returns false rather than generate errors, if accessing
 * a non-existent key in a given array
 *
 * @param  array  $matches Array
 * @param  string $key     Key to fetch in the array
 * @return string A string of the given key in the matches, else false
 */
function getOrFalse(array $matches, $key)
{
   if ( !isset($matches[$key]) )
      return false;
   else
      return $matches[$key];
}
?>