<?php
/**
 * PHOP - Indexer page
 */
require_once 'phop/PHOP.php';
require_once 'phop/Indexing.php';

if ( !defined('PHOP') )
   exit;

/**
 * Prints out each entry in the given data in a <pre> formatted block
 *
 * @param string $dir  Directory this data is for
 * @param array  $data Array of IndexEntry objects for each entry
 */
function printEntries($dir, array $data)
{
   foreach ($data as $entry)
   {
      $name     = $entry->Name;
      $modified = date('d/m/Y H:i', $entry->Modified);
      $contents = $entry->ZipContents;

      echo "<a href='$dir/$name'>$name</a>";
      echo " - Last modified: $modified\n";

      if ( is_int($contents) )
      {
         $error = getZipError($contents);
         echo "<span class='text-error'> &#8627; <strong>Error:</strong> There is an issue with this zip file: $error</span>\n";
      }

      if ( is_array($contents) )
         printZipContents($contents);
   }
}

/**
 * Prints out the contents of zip files, with warnings for any issues
 *
 * @param array $contents String array of zip archive entries
 */
function printZipContents(array $contents)
{
   $count = count($contents);
   $list  = join(', ', $contents);
   echo " &#8627; Zip contents: $list\n";

   if ($count > 1 || $count < 1)
      echo "<span class='text-warning'> &#8627; <strong>Warning:</strong> This zip should only contain one entry</span>\n";
}
?>

<div class="container">
   <h1>
      Index of <strong>/<?php echo $Data['dir'] ?></strong> with
      <strong><?php echo count($Data['files'])  ?></strong> files:
   </h1>

   <br />

   <pre>
<?php printEntries($Data['dir'], $Data['files']); ?>
   </pre>
</div>