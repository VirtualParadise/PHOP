<?php
/**
 * PHOP - Routing functions
 */

if ( !defined('PHOP') )
   exit;


/**
 * Displays a page for the client
 *
 * @param  string $view View to load, with any given data
 * @return [type]       [description]
 */
function gotoView($view, array $data = [])
{
   debug('Response', "Generating view '$view'");

   $view = new View($view);
   $view->Generate($data);
   exit;
}

/**
 * Redirects incoming client to an error. If client is a VR, just sends a HTTP error code,
 * else displays an error view.
 *
 * @param int    $code  HTTP code to use
 * @param string $type  Type of error; use Errors enumeration
 * @param array  $extra Extra data to provide the view
 */
function gotoError($code, $type, $extra = [])
{
   debug('Response', "Failing with $code, type: $type");
   header("Status: $code $type", true, $code);

   if ( isVRClient() )
      exit;

   $data = [
      'code'  => $code,
      'type'  => $type,
      'extra' => $extra,
   ];

   $view = new View(Views::Error);
   $view->Title = "Error: $type - PHOP";
   $view->Generate($data);
   exit;
}

?>