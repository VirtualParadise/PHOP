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

   /**
    * Local
    */
   public static $Local = [
      'Enabled' => true,
      'Sources' => [
         'Storage/Local'
      ],
   ];

   /**
    * Remote
    */
   public static $Remote = [
      'Enabled'   => true,
      'Cache'     => true,
      'CachePath' => 'Storage/Cache',
      'Sources' => [
         [
             'Location' => 'http://objectpath.com',
             // Directories : ['models', 'textures', 'avatars'],
             // Cache       : true,
         ],
      ],
   ];

   /**
    * Public uploads
    */
   public static $Upload = [
      'Enabled' => true,
      'Path'    => 'Storage/Upload'
   ];
}
?>