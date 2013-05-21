<?php
/**
 * PHOP - Local storage functions
 */

if ( !defined('PHOP') )
   exit;

/**
 * Checks if file is available locally
 *
 * @param  string $dir  Directory to check file in
 * @param  string $file File name to check
 * @return bool   true if exists, false if not
 */
function isLocalFile($dir, $file)
{
   $path = pathJoin([$dir, $file]);
   return realpath($path) !== false;
}

/**
 * Counts number of entries in a given directory
 *
 * @param  string  $dir Target directory to check
 * @return integer Amount of items in directory
 */
function getDirectoryCount($dir)
{
   $count = 0;
   $list  = new DirectoryIterator($dir);

   foreach($list as $file)
      $count++;

   return $count;
}

?>