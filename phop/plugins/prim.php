<?php
/**
 * PHOP - Prim Generator
 * Adapted from original design by Epsilion
 */

if ( !defined('PHOP') )
   exit;

class PluginPrim extends Plugin
{
   const RegexPrim       = '{^(p:)?(?<filename>(?<name>(?<type>[a-z]+)(?<params>.+?))\.zip)$}i';
   const RegexFlatParams = '{^(?<x>[0-9]+\.?[0-9]*)(x(?<y>[0-9]+\.?[0-9]*))?(?<p>p)?$}i';
   const Cache           = 'prims';
   const Templates       = 'phop/primTemplates';

   // Geometry consts
   const MMX      = 1000.0;
   const MMY      = 1000.0;
   const MMUV     = 100.0;
   const MinValue = 0.001;
   const MaxValue = 32.0;

   // Flat types
   const FlatWall     = 'wall';
   const FlatPanel    = 'panel';
   const FlatFloor    = 'floor';
   const FlatFlat     = 'flat';
   const FlatFacer    = 'facer';
   const FlatTriangle = 'triangle';
   const FlatTrifloor = 'trifloor';

   function getName()
   {
      return "Prim generator";
   }

   function handleRequest($dir, $file)
   {
      debug('Prims', "Checking if I handle $dir/$file");

      // Only handle models request
      if ($dir !== 'models')
         return false;

      // Validate requests
      if ( !preg_match(PluginPrim::RegexPrim, $file, $matches) )
         return false;
      else
         debug('Prims', "Handling request for $file ($matches[filename])");

      $file = $matches['filename'];
      $path = pathJoin([PluginPrim::Cache, $file]);

      // First, check for local file...
      if ( is_file($path) )
         gotoFile(PluginPrim::Cache, $file);

      $type   = $matches['type'];
      $params = explode(',', $matches['params']);
      debug('Prim', "Trying to generate a $type with $matches[params]");

      switch ($type)
      {
         case 'w':
         case 'wll':
         case 'wall':
            $prim = PluginPrim::makeFlat($params, PluginPrim::FlatWall);
            break;

         case 'p':
         case 'pan':
         case 'panel':
            $prim = PluginPrim::makeFlat($params, PluginPrim::FlatPanel);
            break;

         case 'f':
         case 'flr':
         case 'floor':
            $prim = PluginPrim::makeFlat($params, PluginPrim::FlatFloor);
            break;

         case 'flt':
         case 'flat':
            $prim = PluginPrim::makeFlat($params, PluginPrim::FlatFlat);
            break;

         case 'fac':
         case 'facer':
            $prim = PluginPrim::makeFlat($params, PluginPrim::FlatFacer);
            break;

         case 'tri':
         case 'triangle':
            $prim = PluginPrim::makeFlat($params, PluginPrim::FlatTriangle);
            break;

         case 'triflr':
         case 'trifloor':
            $prim = PluginPrim::makeFlat($params, PluginPrim::FlatTrifloor);
            break;

         default:
            return false;
      }

      if ( empty($prim) )
         gotoError(400, 'Unknown prim generator error');

      debug('Prim', "Saving prim '$file'");

      if ( !is_dir(PluginPrim::Cache) )
         mkdir(PluginPrim::Cache);

      $zip = new ZipArchive();
      $zip->open($path, ZIPARCHIVE::CREATE);
      $zip->addFromString("$matches[name].rwx", $prim);
      $zip->close();
      gotoFile(PluginPrim::Cache, $file);
   }

   static function makeFlat($values, $type)
   {
      if ( !preg_match(PluginPrim::RegexFlatParams, $values[0], $dim) )
         gotoError(400, 'Invalid prim syntax');

      // Dimension checking (reads values as mm)
      $rawX = (float) $dim['x'];
      $rawY = isset($dim['y']) && !empty($dim['y'])
         ? (float) $dim['y']
         : $dim['x'];
      debug('Flat prim', "Raw X $rawX by Raw Y $rawY");

      $dimXFactor = 2;
      $dimYFactor = ($type === PluginPrim::FlatFlat || $type === PluginPrim::FlatFloor || $type === PluginPrim::FlatTrifloor)
         ? 2
         : 1;

      $dimX = $rawX / (PluginPrim::MMX * $dimXFactor);
      $dimY = $rawY / (PluginPrim::MMY * $dimYFactor);
      debug('Flat prim', "X $dimX by Y $dimY");

      if ($dimX < PluginPrim::MinValue || $dimY < PluginPrim::MinValue)
         gotoError(400, 'Flat primitive is too small');

      if ($dimX > PluginPrim::MaxValue || $dimY > PluginPrim::MaxValue)
         gotoError(400, 'Flat primitive is too large');

      // Phantom parameter
      $phantom = isset($dim['p']) && !empty($dim['p'])
         ? 'off'
         : 'on';

      // Tags
      $tag = isset($values[1]) && !empty($values[1])
         ? PluginPrim::parseTagNumber( $values[1] )
         : 200;

      // UV coordinates
      $uvX = isset($values[2]) ? $values[2] : false;
      $uvY = isset($values[3]) ? $values[3] : false;
      $uv  = ($type === PluginPrim::FlatFlat || $type === PluginPrim::FlatPanel || $type === PluginPrim::FlatFacer)
         ? PluginPrim::uvFill($uvX, $uvY)
         : PluginPrim::uvPlanar($uvX, $uvY, $rawX, $rawY);

      // Generate using template
      debug('Flat', "Type: $type");
      debug('Flat', "Dimensions: $dimX by $dimY, tag: $tag, uvX scale: $uv[0], uvY scale: $uv[1], collision: $phantom");
      $template = PluginPrim::getPrimTemplate($type);
      $prim     = sprintf($template, $dimX, $dimY, $tag, $uv[0], $uv[1], $phantom);

      debug('Flat', "Generated: $prim");
      return $prim;
   }

   /*
    * MATH / RWX
    */

   static function uvFill($x, $y)
   {
      if ( empty($x) )
      {
         $uvX = 1;
         $uvY = 1;
      }
      else if ( is_numeric($x) && empty($y) )
      {
         $uvX = (float) $x;
         $uvY = (float) $x;
      }
      else if ( is_numeric($y) )
      {
         $uvX = (float) $x;
         $uvY = (float) $y;
      } else
         gotoError(400, 'Invalid UV parameters');

      return [$uvX, $uvY];
   }

   static function uvPlanar($x, $y, $rawX, $rawY)
   {
      if ( empty($x) )
      {
         $uvX = $rawX / (PluginPrim::MMUV / 1);
         $uvY = $rawY / (PluginPrim::MMUV / 1);
      }
      else if ( is_numeric($x) && empty($y) )
      {
         $uvX = $rawX / (PluginPrim::MMUV / (float)$x);
         $uvY = $rawY / (PluginPrim::MMUV / (float)$x);
      }
      else if ( is_numeric($y) )
      {
         $uvX = $rawX / (PluginPrim::MMUV / (float)$x);
         $uvY = $rawY / (PluginPrim::MMUV / (float)$y);
      } else
         gotoError(400, 'Invalid UV parameters');

      return [$uvX, $uvY];
   }

   static function parseTagNumber($val)
   {
      switch($val)
      {
         case 'p':
            return 200;
         case 's':
            return 100;
         default:
            if ( !is_numeric($val) )
               gotoError(400, 'Invalid tag parameter');
            else
               return $val;
      }
   }

   /*
    * FILES
    */

   static function getPrimTemplate($template)
   {
      $path = pathJoin([PluginPrim::Templates, $template.'.txt']);
      return file_get_contents($path);
   }
}

new PluginPrim();