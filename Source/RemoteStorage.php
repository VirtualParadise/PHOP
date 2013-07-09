<?php
/**
 * PHOP - Remote retrival and caching functions
 */

/**
 * Attempts a cURL fetch for the given directory and file from the configured remote
 * sources
 *
 * @param  string $dir  Directory of the request
 * @param  string $file File name of the request
 * @param  mixed  $data Pointer to remotely fetched data if successful
 * @param  int    $size Pointer to length of data retrieved
 * @return bool   True if successful, otherwise false
 */
function getRemoteFile($dir, $file, &$data, &$size)
{
   debug('Remote', "Iterating through remote paths for $dir/$file");

   foreach (Config::$RemotePaths as $path)
   {
      $url  = trim($path, '/\\')."/$dir/$file";
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

      $data  = curl_exec($curl);
      $code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      $size  = curl_getinfo($curl, CURLINFO_SIZE_DOWNLOAD);
      $errNo = curl_errno($curl);
      $err   = curl_error($curl);
      curl_close($curl);

      debug('Remote', "Fetch: $url [$code]");

      if ($errNo !== 0 || $err !== '')
      {
         debug('Remote', "Curl error retrieving $url : $err (code $errNo)");
         continue;
      }

      if ($code >= 400 && $code <= 600)
      {
         debug('Remote', "HTTP error retrieving $url : $code");
         continue;
      }

      if ($size == 0)
      {
         debug('Remote', "0 bytes downloaded from $url; rejecting");
         continue;
      }

      if ($data !== false)
      {
         debug('Remote', "Successful fetch from $url");
         return true;
      }
   }

   debug('Remote', "No remote sources could fulfil request $dir/$file");
   return false;
}

/**
 * Saves given data to a file in a specified directory, if caching is enabled
 *
 * @param  string $dir  Target directory name
 * @param  string $file Target file name
 * @param  mixed  $data File data to save
 * @return void
 */
function cacheFile($dir, $file, $data)
{
   if (!Config::Caching)
      return;

   debug('Cache', "Saving $file to $dir local storage");

   $path = pathJoin([$dir, $file]);
   file_put_contents($path, $data);
   chmod($path, 0666);
}

?>