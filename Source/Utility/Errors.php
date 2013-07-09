<?php
/**
* PHOP : A better error and exception handling mechanism than built-in PHP
*/
namespace PHOP\Utility;

use PHOP\Utility\Log;

/**
 * Static class with methods for (de)registering error and exception handlers. Handles
 * PHP errors by throwing all of them as exceptions
 *
 * @package PHOP\Utility
 */
class ErrorHandler
{
   const tag = "ErrorHandler";

   private static $prevLevel;

   /**
    * Registers the error and exception handlers, disabling error reporting
    */
   public static function Register()
   {
      set_error_handler(function($c, $m, $f, $l, $v) {
         ErrorHandler::handleError($c, $m, $f, $l, $v);
      });

      set_exception_handler(function($e) {
         ErrorHandler::handleException($e);
      });

      ErrorHandler::$prevLevel = error_reporting(0);

      Log::Debug(ErrorHandler::tag, 'Handlers have been registered');
   }

   /**
    * Deregisters the error and exception handlers and restores error reporting
    */
   public static function Deregister()
   {
      restore_error_handler();
      restore_exception_handler();
      error_reporting(ErrorHandler::$prevLevel);

      Log::Debug(ErrorHandler::tag, 'Handlers have been deregistered');
   }

   private static function handleError($code, $msg, $file, $line, $vars)
   {
      throw new \ErrorException($msg, $code, 0, $file, $line );
   }

   private static function handleException(\Exception $ex)
   {
      $msg   = "PANIC: {$ex->getMessage()} \n";
      $trace = $ex->getTrace();
      $trace = array_slice($trace, 1);

      foreach ($trace as $key => $entry)
         $msg .= "\t#$key : $entry[file] @ $entry[function]:$entry[line]\n";

      Log::Critical($ex->getFile(), $msg);
      exit(1);
   }
}