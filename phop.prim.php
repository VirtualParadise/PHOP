<?php
// PHOP Prim Generator - Roy Curtis
// Adapted from original design by Epsilion
//
define("RGX_REAL", '[0-9]+\\.?[0-9]*');
define('RGX_PRIMNAME', '/^(?<type>[a-z]+)(?<params>.+?)$/i');
define('RGX_PRIMFLAT', '/(?<x>'.RGX_REAL.')(x(?<y>'.RGX_REAL.'))?(?<p>p)?/i');

// Geometry maths
define('MIN_VALUE', 0.001);
define('MAX_VALUE', 32.0);
define('MM_X', (float)1000);
define('MM_Y', (float)1000);
define('MM_UV', (float)100);

// Flat types
define('TYPE_WALL', 'wall');
define('TYPE_PANEL', 'panel');
define('TYPE_FLOOR', 'floor');
define('TYPE_FLAT', 'flat');
define('TYPE_FACER', 'facer');
define('TYPE_TRIANGLE', 'triangle');
define('TYPE_TRIFLOOR', 'trifloor');

require_once('phop.lib.php');

function main() {
   $Q = $_GET['q'];
   $fileName = $Q.".zip";

   // First, check for local file...
   if (getLocalPrim($fileName) === true)
      return gotoFile(PRIMS, $fileName);

   // Verify valid request
   $matches;
   if (!preg_match(RGX_PRIMNAME,$Q,$matches))
      return fail("Invalid primitive syntax", 400);

   $type = $matches['type'];
   $params = explode(',', $matches['params']);
   $prim;
   debug('Prim', "Trying to generate a $type with $matches[params]");
   switch ($type) {
      case 'w':
      case 'wll':
      case 'wall':
         $prim = primFlat($params, TYPE_WALL);
         break;

      case 'p':
      case 'pan':
      case 'panel':
         $prim = primFlat($params, TYPE_PANEL);
         break;

      case 'f':
      case 'flr':
      case 'floor':
         $prim = primFlat($params, TYPE_FLOOR);
         break;

      case 'flt':
      case 'flat':
         $prim = primFlat($params, TYPE_FLAT);
         break;

      case 'fac':
      case 'facer':
         $prim = primFlat($params, TYPE_FACER);
         break;

      case 'tri':
      case 'triangle':
         $prim = primFlat($params, TYPE_TRIANGLE);
         break;

      case 'triflr':
      case 'trifloor':
         $prim = primFlat($params, TYPE_TRIFLOOR);
         break;

      default:
         return fail("Unknown primitive type: $type", 400);
   }

   if (e($prim))
      return fail("Unknown error", 500);

   $zip = new ZipArchive();
   $zip->open(PRIMS.$fileName, ZIPARCHIVE::CREATE);
   $zip->addFromString("$Q.rwx", $prim);
   $zip->close();

   gotoFile(PRIMS, $Q.".zip");
}

function primFlat($values, $type) {
   $dim;
   if (!preg_match(RGX_PRIMFLAT, $values[0], $dim))
      return fail("Invalid Flat primitive syntax", 400);

   // Dimension checking (reads values as mm)
   $rawX = (float)$dim[x];
   $rawY = (float)pickNum($dim[y], $rawX);

   $dimXFactor = 2;
   $dimYFactor = ($type === TYPE_FLAT || $type === TYPE_FLOOR || $type === TYPE_TRIFLOOR)
      ? 2
      : 1;

   $dimX = $rawX / (MM_X*$dimXFactor);
   $dimY = $rawY / (MM_Y*$dimYFactor);

   if ($dimX < MIN_VALUE || $dimY < MIN_VALUE)
      return fail("Flat primitive too small", 400);

   if ($dimX > MAX_VALUE || $dimY > MAX_VALUE)
      return fail("Flat primitive too large", 400);

   // Optional parameters (tag + UV planar scaling)
   $phantom = e($dim['p']) ? 'on' : 'off';
   $tag = parseTagNumber(pick($values[1], 200));
   $uv = ($type === TYPE_FLAT || $type === TYPE_PANEL || $type === TYPE_FACER)
      ? uvFill($values[2], $values[3])
      : uvPlanar($values[2], $values[3], $rawX, $rawY);

   // Generate using template
   debug('Flat', "Type: $type");
   debug('Flat', "Dimensions: $dimX by $dimY, tag: $tag, uvX scale: $uvX, uvY scale: $uvY, collision: $phantom");
   $template = getPrimTemplate($type);
   $prim = sprintf($template, $dimX, $dimY, $tag, $uv[0], $uv[1], $phantom);

   debug('Flat', "Generated: $prim");
   return $prim;
}

/*
 * MATH / RWX
 */

function uvFill($x, $y) {
   if ( e($x) ) {
      $uvX = 1;
      $uvY = 1;
   } else if ( is_numeric($x) && e($y) ) {
      $uvX = (float)$x;
      $uvY = (float)$x;
   } else if ( is_numeric($y) ) {
      $uvX = (float)$x;
      $uvY = (float)$y;
   } else {
      return fail("Invalid UV parameters", 400);
   }

   return Array($uvX, $uvY);
}

function uvPlanar($x, $y, $rawX, $rawY) {
   if ( e($x) ) {
      $uvX = $rawX / (MM_UV / 1);
      $uvY = $rawY / (MM_UV / 1);
   } else if ( is_numeric($x) && e($y) ) {
      $uvX = $rawX / (MM_UV / (float)$x);
      $uvY = $rawY / (MM_UV / (float)$x);
   } else if ( is_numeric($y) ) {
      $uvX = $rawX / (MM_UV / (float)$x);
      $uvY = $rawY / (MM_UV / (float)$y);
   } else {
      return fail("Invalid UV parameters", 400);
   }

   return Array($uvX, $uvY);
}

function parseTagNumber($val) {
   switch($val)
   {
      case 'p':
         return 200;
      case 's':
         return 100;
      default:
         if (!is_numeric($val))
            return fail("Invalid tag parameter", 400);
         else
            return $val;
   }
}

/*
 * FILES
 */

function getPrimTemplate($template) {
   return file_get_contents(TEMPLATES.$template.'.txt');
}

/**
 * Alias to getLocalFile, for the PRIMS folder
 */
function getLocalPrim($name) {
   $prim = getLocalFile(PRIMS,$name);

   if ($prim === false)
      return false;
   else if (filemtime(__FILE__) > filemtime($prim))
      return false;
   else
      return true;
}

/*
 * REDIRECTION
 */

function failPrim($type) {
   fail("Error generating primitive of type $type", 400);
}

main();
?>