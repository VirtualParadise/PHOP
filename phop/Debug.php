<?php
/**
 * PHOP - Debugging functions
 */

if ( !defined('PHOP') )
   exit;

/**
 * Writes message to PHP CLI console for debugging
 */
function debug($tag, $msg)
{
   $tag = str_pad($tag, 10);
   error_log("[$tag] $msg");

   if (Config::Logging)
      file_put_contents(Config::LogFile, "[$_SERVER[REMOTE_ADDR], $tag] $msg\n", FILE_APPEND);
}

?>