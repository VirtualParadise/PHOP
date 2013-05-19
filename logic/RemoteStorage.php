<?php
/**
 * PHOP - Remote retrival and caching functions
 */

if ( !defined('PHOP') )
   exit;

/**
 * Saves given data to a file in a specified directory
 *
 * @param  string $dir  Target directory name
 * @param  string $file Target file name
 * @param  mixed  $data File data to save
 * @return void
 */
function cacheFile($dir, $file, $data)
{
   debug('Cache', "Saving $file to $dir cache");

   $path = pathJoin([$dir, $file]);
   file_put_contents($path, $data);
   chmod($path, 0666);
}

?>