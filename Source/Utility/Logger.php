<?php
/**
 * PHOP : Container class for logging functions and setup
 */
namespace PHOP\Utility;

use PHOP\Config as Config;

/**
 * Bitwise-flag enumeration class of all logging levels available
 *
 * @package PHOP\Utility
 */
final class LogLevels
{
   /**
    * Combines all logging level flags
    */
   const All        = 255;
   /**
    * Combines all logging flags except for Fine and Finer
    */
   const Debugging  = 63;
   /**
    * Combines only the Critical, Error, Warning and Notice level flags
    */
   const Production = 15;

   const Critical = 1;
   const Error    = 2;
   const Warning  = 4;
   const Notice   = 8;
   const Info     = 16;
   const Debug    = 32;
   const Fine     = 64;
   const Finer    = 128;

   private function __construct() { }
}

/**
 * Static class with logging functions for all of VWAS
 *
 * @package PHOP\Utility
 */
class Log
{
   /**
    * Bit-wise flag of the current logging levels in use
    * @var int
    */
   public static $Level = LogLevels::All;

   public static function Configure()
   {
      Log::$Level = Config::$Log['Level'];
   }

   /**
    * Emits a log message to console if allowed by the currently set LogLevel
    * @param int    $level   Intended level of the message
    * @param string $tag     Common tag or category for source of message
    * @param string $message Message content
    */
   public static function Emit($level, $tag, $message)
   {
      if ( (Log::$Level & $level) === 0 )
         return;

      echo "$level | [$tag] $message";
   }

   public static function Critical($tag, $message) { Log::Emit(LogLevels::Critical, $tag, $message); }
   public static function Error($tag, $message)    { Log::Emit(LogLevels::Error, $tag, $message); }
   public static function Warning($tag, $message)  { Log::Emit(LogLevels::Warning, $tag, $message); }
   public static function Notice($tag, $message)   { Log::Emit(LogLevels::Notice, $tag, $message); }
   public static function Info($tag, $message)     { Log::Emit(LogLevels::Info, $tag, $message); }
   public static function Debug($tag, $message)    { Log::Emit(LogLevels::Debug, $tag, $message); }
   public static function Fine($tag, $message)     { Log::Emit(LogLevels::Fine, $tag, $message); }
   public static function Finer($tag, $message)    { Log::Emit(LogLevels::Finer, $tag, $message); }
}