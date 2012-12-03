<?php
// PHOP Indexer - Roy Curtis
// Adapted from original design by Epsilion
//
define("INDEX", ".index.db");
require_once('phop.lib.php');

function main() {
   $dir = str_replace('/','',$_GET['q']);
   $idxFile = $dir.INDEX;
   $messages;
   $index;

   // Check for rebuild query
   if (isset($_GET['rebuild'])) {
      debug('Indexer', 'Rebuilding index...');
      $index = generateIndex($dir, $idxFile);
      $messages = 'Index rebuilt.';
   }

   // Reject invalid directories
   if (!is_dir($dir))
      return failFancy("Invalid directory", 400);

   $index = e($index) ? getIndex($idxFile) : $index;
   if ($index === false) {
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
   foreach($data as $row) {
      $row = explode(',',$row);
      $mod = "modified: $row[1]";
      $zip = e($row[2]) ? '' : ", has: $row[2]";

      $html .= "$row[0], $mod$zip\n";
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

function getIndex($idxFile) {
   if (is_file($idxFile))
      return file($idxFile, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
   else
      return false;
}

function generateIndex($dir, $idxFile) {
   $files = [];

   if ($handle = opendir($dir)) {
       while (false !== ($entry = readdir($handle)))
            if ($entry != "." && $entry != "..")
               array_push($files, generateEntry($dir, $entry));

       closedir($handle);
   }

   natsort($files);
   file_put_contents($idxFile, implode("\n", $files));
   return $files;
}

// Spec: $file, $modified, $zip contents
function generateEntry($dir, $file) {
   $filepath = "./$dir/$file";
   $path = pathinfo($filepath);
   $file = $path['filename'];
   $modified = filemtime($filepath);
   $contents = [];

   if ($path['extension'] == 'zip') {
      $zip = new ZipArchive();
      $zip->open($filepath);

      for ($i=0; $i < $zip->numFiles; $i++)
          array_push($contents, $zip->getNameIndex($i));

      $zip->close();
   }

   return "$file,$modified,".implode(' ', $contents);
}

/*
 * REDIRECTION
 */

function failFancy($msg, $code) {
   debug('Response', "Failing with $code, $msg");
   header("Status: $msg", true, $code);
   echo viewCore("Error $error",$msg,"~");
   exit;
}

main();
?>