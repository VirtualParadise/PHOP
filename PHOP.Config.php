<?php
/**
 * PHOP : User configuration
 */
namespace PHOP;

use PHOP\Utility\LogLevels;

/**
 * Static class that contains all the user-defined settings for PHOP
 */
class Config
{
   /**
    * Logging
    */
   public static $Log = [
      // Bitwise flags of enabled logging levels
      'Level' => LogLevels::All
   ];

   /**
    * Assets
    */
   public static $Assets = [
      'Enabled'     => true,
      'Host'        => '0.0.0.0',
      'Port'        => 80,
      'Directories' => ['models', 'textures', 'avatars'],
   ];

   const Caching = true;
   const Logging = false;
   const LogFile = 'log.txt';

   const PublicUrl = 'http://localhost:8888';

   static $RemotePaths = [
      "http://awcommunity.org/romperroom",
      "http://aw.platform3d.com/multipath"
   ];

   static $AssetDirectories = [
      "models",
      "textures",
      "avatars",
   ];

   static $Plugins = [
      "prim",
      //"example",
   ];
}
?>