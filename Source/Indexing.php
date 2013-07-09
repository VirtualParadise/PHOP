<?php
/**
 * PHOP - Indexing manager
 */

/**
 * Represents an index entry for a file's metadata
 */
class IndexEntry
{
   /**
    * @var string Name of the file entry
    */
   public $Name;

   /**
    * @var int Unix timestamp of the last modified date
    */
   public $Modified;

   /**
    * @var int File size in bytes
    */
   public $Size;

   /**
    * @var mixed String array of zip contents, false if not a zip or integer if error
    */
   public $ZipContents = false;
}

/**
 * Feeds an indexer view to the client
 *
 * @param  string $dir  Target directory
 * @return void   Exits execution with a view
 */
function gotoIndex($dir)
{
   $handle = opendir($dir);
   $data   = [
      'dir'   => $dir,
      'files' => []
   ];

   if ($handle === false)
   {
      debug('Indexer', "Error opening directory $dir for reading");
      gotoError(500, Errors::Unknown);
   }

   while (true)
   {
      $file = readdir($handle);

      // Finished iterating directory
      if ($file === false)
         break;

      // Ignore dots
      if ($file === '.' || $file === '..')
         continue;

      $entry = generateEntry($dir, $file);
      array_push($data['files'], $entry);
   }

   closedir($handle);
   gotoView(Views::Index, $data);
}

/**
 * Generates and return an array of IndexEntries
 *
 * @param  string     $dir  Target directory
 * @param  string     $file Target file
 * @return IndexEntry Metadata object of the specified file
 */
function generateEntry($dir, $file)
{
   $rawPath  = pathJoin([$dir, $file]);
   $entry    = new IndexEntry();

   $entry->Name     = $file;
   $entry->Modified = filemtime($rawPath);
   $entry->Size     = filesize($rawPath);

   if ( strripos($file, '.zip', -4) !== false )
      $entry->ZipContents = getZipContents($rawPath);
   else
      $entry->ZipContents = false;

   return $entry;
}

/**
 * Attempts to get list of contents of a zip file
 *
 * @param  string $path Absolute or relative path to zip file
 * @return mixed  String array of entries, else false on error
 */
function getZipContents($path)
{
   static $ZipReader = false;

   // Init on first use
   if ($ZipReader === false)
      $ZipReader = new ZipArchive();

   $contents = [];
   $result   = $ZipReader->open($path);

   if ($result !== true)
      return $result;

   for ($i = 0; $i < $ZipReader->numFiles; $i++)
      array_push( $contents, $ZipReader->getNameIndex($i) );

   $ZipReader->close();
   return $contents;
}

/**
 * Resolves a ZipArchive error code to string
 *
 * @param  int    $code Error code to resolve
 * @return string Human-readable string of error
 */
function getZipError($code)
{
   switch($code)
   {
      case ZipArchive::ER_INCONS:
         return 'Archive is inconsistent';
      case ZipArchive::ER_INVAL:
         return 'Invalid argument';
      case ZipArchive::ER_MEMORY:
         return 'Memory allocation failure';
      case ZipArchive::ER_NOENT:
         return 'No such file';
      case ZipArchive::ER_NOZIP:
         return 'File is not a zip archive';
      case ZipArchive::ER_OPEN:
         return 'Unable to open archive';
      case ZipArchive::ER_READ:
         return 'Unable to read archive';
      case ZipArchive::ER_SEEK:
         return 'Unable to seek archive';
      default:
         return 'Unknown error';
   }
}
?>