<?php
/**
 * PHOP - Example plugin
 */

if ( !defined('PHOP') )
   exit;

class PluginTest extends Plugin
{
   /**
    * Gets the plugin's full name
    *
    * @return string Plugin name
    */
   function getName()
   {
      return "Example plugin";
   }

   /**
    * Asks the plugin to handle an asset request
    *
    * @param  string $dir  Target directory to handle
    * @param  string $file Target file to handle
    * @return bool   Return true if handled, else false
    */
   function handleRequest($dir, $file)
   {
      debug('TestPlugin', "Been asked to handle $dir/$file");
      return false;
   }
}

new PluginTest();
?>