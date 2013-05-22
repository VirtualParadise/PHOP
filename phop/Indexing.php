<?php
/**
 * PHOP - Indexing manager
 */

if ( !defined('PHOP') )
   exit;

function generateIndex(string $dir)
{
   $files = [];

   if ( $handle = opendir($dir) )
   {
      while ( true )
      {
         $file = readdir($handle);

         // Finished iterating directory
         if      ($file === false)
            break;

         // Process file entry
         if ($file != "." && $file != "..")
         {
            $entry = generateEntry($dir, $file);
            array_push($files, $file);
         }
      }

      closedir($handle);
   }

   natsort($files);
   return $files;
}

function generateEntry(string $dir, string $file)
{
   $filepath = "./$dir/$file";
   $path     = pathinfo($filepath);
   $file     = $path['basename'];
   $modified = filemtime($filepath);
   $contents = [];

   // Explore contents of zip files
   if ($path['extension'] == 'zip')
   {
      $zip = new ZipArchive();
      $zip->open($filepath);

      for ($i = 0; $i < $zip->numFiles; $i++)
         array_push($contents, $zip->getNameIndex($i));

      $zip->close();
   }

   return [
      'File'     => $file,
      'Modified' => $modified,
      'Contents' => $contents
   ];
}

?>