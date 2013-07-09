<?php
/**
 * PHOP - Asset plugin architecture
 */


abstract class Plugin
{
   /**
    * @var Plugin[] All plugins loaded by PHOP
    */
   static $Loaded = [];

   /**
    * Gets the plugin's full name
    *
    * @return string Plugin name
    */
   abstract function getName();

   /**
    * Asks the plugin to handle an asset request
    *
    * @param  string $dir  Target directory to handle
    * @param  string $file Target file to handle
    * @return bool   Return true if handled, else false
    */
   abstract function handleRequest($dir, $file);

   final function __construct()
   {
      if (Plugin::$Loaded === false)
         Plugin::$Loaded = [];

      $name = $this->GetName();
      debug('Plugins', "Adding '$name' to loaded plugins");
      array_push(Plugin::$Loaded, $this);
   }
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