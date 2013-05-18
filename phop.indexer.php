<?php
/**
 * PHOP - Directory indexer
 */
require_once('phop.lib.php');
require_once('phop.views.php');

function main()
{
   $query = $_GET['q'];
   $dir   = str_replace('/','', $query);
   $index;

   // Reject invalid directories
   if ( !is_dir($dir) )
      return indexerFail("Invalid directory", 400);

   $index = generateIndex($dir);
   if ($index === false)
   {
      debug('Indexer', 'Generating index for the first time...');
      $index = generateIndex($dir, $idxFile);
   }

   echo viewCore("Listing for $dir",
      viewListing($index, $dir, $messages),
      viewFooter($dir, $idxFile)
      );
   exit;
}

/*
 * VIEWS
 */

function viewCore($title, $body, $footer) {
   $bg = CSS_PATTERN;
   return <<<EOD
<head>
   <title>$title</title>
</head>
<body style='font-family: sans-serif;'>
   $body</pre>
   <hr />
   $footer
</body>
EOD;
}

function viewListing($data, $dir, $messages = null) {
   $html = "<h1>Listing for $dir:</h1><pre>";

   foreach($data as $row)
   {
      $row  = explode(',',$row);
      $file = "<a href='$dir/$row[0]'>$row[0]</a>";
      $mod  = "modified: $row[1]";
      $zip  =
         empty($row[2])
         ? ''
         : ", has: $row[2]";

      $html .= "$file, $mod$zip\n";
   }

   return $html."</pre>";
}

function viewFooter($dir, $idx) {
   $index = filemtime($idx);
   return "<a href='phop.indexer.php?rebuild&q=$dir'>Rebuild index</a> - Last rebuilt at $index";
}


/*
 * INDEX
 */

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

/*
 * REDIRECTION
 */

function indexerFail($msg, $code)
{
   debug('Response', "Failing with $code, $msg");
   header("Status: $msg", true, $code);
   echo viewCore("Error $error",$msg,"~");
   exit;
}

main();
?>