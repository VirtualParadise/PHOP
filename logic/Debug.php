<?php
/**
 * PHOP - Debugging functions
 */

if ( !defined('PHOP') )
   exit;
//
/**
 * Writes message to PHP CLI console for debugging
 */
function debug($tag, $msg)
{
   error_log("[$tag] $msg");

   if (LOGGING)
      file_put_contents(LOGFILE, "[$_SERVER[REMOTE_ADDR], $tag] $msg\n", FILE_APPEND);
}

?>