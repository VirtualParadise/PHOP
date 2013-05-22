<?php
/**
 * PHOP - Asset plugin architecture
 */

if ( !defined('PHOP') )
   exit;

abstract class Plugin
{
   static $Loaded = [];

   abstract function getName();
   abstract function handleRequest($dir, $file);

   function __construct()
   {
      if (Plugin::$Loaded === false)
         Plugin::$Loaded = [];

      $name = $this->GetName();
      debug('Plugins', "Adding '$name' to loaded plugins");
      array_push(Plugin::$Loaded, $this);
   }
}

/**
 * Returns all plugins that handle the given directory and file request
 *
 * @param  string $dir  Requested directory name to handle
 * @param  string $file Requested file name to handle
 * @return array  Array of Plugin-class objects that handle the given request
 */
function checkPlugins($dir, $file)
{
   loadPlugins();

   foreach (Plugin::$Loaded as $plugin)
      $plugin->handleRequest($dir, $file);
}

function loadPlugins()
{
   if ( !empty(Plugin::$Loaded) )
      return;

   foreach (Config::$Plugins as $plugin)
   {
      $path = pathJoin([Directories::Plugins, "$plugin.php"]);

      if ( !is_file($path) )
      {
         debug('Plugins', "Plugin '$plugin' is in configuration, but file is missing");
         continue;
      }
      else
         include_once $path;
   }
}

?>