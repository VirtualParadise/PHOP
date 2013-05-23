<?php
/**
 * PHOP - Error page
 */
require_once 'phop/PHOP.php';

if ( !defined('PHOP') )
   exit;
?>

<div class="container">
   <h1>
      Error <strong><?php echo $Data['code'] ?></strong> - <?php echo $Data['type'] ?>
   </h1>

   <?php

   switch ($Data['type'])
   {
      /**
       * Unhandled pages (e.g. plugins)
       */
      default:
         ?>
            <p>
               <?php echo getOrBlank($Data['extra'], 'desc'); ?>
            </p>
         <?php
         break;

      /**
       * General bad request error
       */
      case Errors::BadRequest:
         ?>
         <p>
            The given asset request was of invalid format.
            Ensure the query string follows this format:
         </p>

         <blockquote class="syntax">
            /?q=/<abbr class="param">directory</abbr>/<abbr class="param">file.ext</abbr>
         </blockquote>

         <p>
            To access the index of a directory,
            use the menu on top or the following query format:
         </p>

         <blockquote class="syntax">
            /?q=/<abbr class="param">directory</abbr>
         </blockquote>
         <?php
         break;

      /**
       * Bad directory error
       */
      case Errors::BadDirectory:
         ?>
         <p>
            This object path does not serve or index assets from
            that directory, or the directory itself is missing.
         </p>

         <p>
            Please try the following directories instead:
         </p>

         <?php
         foreach (Config::$AssetDirectories as $dir)
         {
            if ( !is_dir($dir) )
               continue;

            ?>
            <blockquote class="syntax">
               /?q=/<abbr class="param"><?php echo $dir ?></abbr>
            </blockquote>
            <?php
         }

         ?>
         <?php
         break;

      /**
       * Asset not found
       */
      case Errors::NotFound:
         ?>
         <p>
            Sorry, the requested asset could not be found
            in local storage or on the remote paths.
         </p>

         <p>
            You may wish to try the indexes via the menu at
            the top of this page.
         </p>
         <?php
         break;

      /**
       * Unknown route
       */
      case Errors::BadAction:
         ?>
         <p>
            The requested action <code><?php echo $_GET['action'] ?></code>
            was unrecognized. Please try an option on the top menu.
         </p>
         <?php
         break;
   }

   ?>
</div>