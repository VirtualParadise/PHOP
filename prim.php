<?php
// PHOP Prim Generator - Roy Curtis
// Adapted from original design by Epsilion
//
// COMMENT LINE BELOW WHEN DONE TESTING
define("RGX_REAL", '[0-9]+\\.?[0-9]*');
define('RGX_PRIMNAME', '/^(?<type>[a-z]+)(?<params>.+?)$/i');
define('RGX_PRIMWALL', '/(?<x>'.RGX_REAL.')(x(?<y>'.RGX_REAL.'))?(?<p>p)?/i');
define('MIN_VALUE', 0.001);
define('MM_X', 2000);
define('MM_Y', 1000);
define('MM_UV', 200);
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
         $prim = primWall($params);
         break;

      case 'p':
      case 'pan':
      case 'panel':
         $prim = primWall($params, true);
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

function primWall($values, $isPanel) {
   $dim;
   if (!preg_match(RGX_PRIMWALL, $values[0], $dim))
      return fail("Invalid wall primitive syntax", 400);

   // Dimension checking (reads values as mm)
   $rawX = (float)$dim[x];
   $rawY = (float)pickNum($dim[y], $rawX);
   $dimX = $rawX / MM_X;
   $dimY = $rawY / MM_Y;

   if ($dimX < MIN_VALUE || $dimY < MIN_VALUE)
      return fail("Wall primitive too small", 400);

   // Optional parameters (tag + UV planar scaling)
   $phantom = e($dim['p']) ? 'on' : 'off';
   $tag = parseTagNumber(pick($values[1], 200));
   if (e($values[2])) {
      $uvX = $rawX / (MM_UV / 1);
      $uvY = $rawY / (MM_UV / 1);
   } else if ( e($values[3]) && is_numeric($values[2]) ) {
      $uvX = $rawX / (MM_UV / $values[2]);
      $uvY = $rawY / (MM_UV / $values[2]);
   } else if (is_numeric($values[3])) {
      $uvX = $rawX / (MM_UV / $values[2]);
      $uvY = $rawY / (MM_UV / $values[3]);
   } else {
      fail("Invalid wall parameters", 400);
   }

   // Generate using template
   debug('Wall', "Dimensions: $dimX by $dimY, tag: $tag, uvX scale: $uvX, uvY scale: $uvY, collision: $phantom");
   $template = $isPanel ? getPrimTemplate('panel') : getPrimTemplate('wall');
   $prim = sprintf($template, $dimX, $dimY, $tag, $uvX, $uvY, $phantom);

   debug('Wall', "Generated: $prim");
   return $prim;
}

function getPrimTemplate($template) {
   return file_get_contents(TEMPLATES.$template.'.txt');
}

function failPrim($type) {
   fail("Error generating primitive of type $type", 400);
}

/**
 * Alias to getLocalFile, for the PRIMS folder
 */
function getLocalPrim($name) {
   return getLocalFile(PRIMS,$name);
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

main();
?>