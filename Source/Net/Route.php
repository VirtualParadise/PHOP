<?php
/**
 * PHOP : Type definition for a HTTP server route; can be mounted or unmounted by the
 * static HTTPServer class
 */

namespace PHOP\Net;

/**
 * A route that can be (un)mounted by the static HTTP server
 */
class Route
{
   /**
    * PCRE regex pattern to match for this route
    * @var string
    */
   public $Pattern;

   /**
    * Callback to call when this route's pattern matches an incoming request. Should
    * accept a request and response object
    * @var callback
    */
   public $Callback;

   /**
    * Sets up a route with a given pattern and callback
    * @param string   $pattern
    * @param callback $callback
    */
   public function __construct($pattern, $callback)
   {
      $this->Pattern  = $pattern;
      $this->Callback = $callback;
   }
}