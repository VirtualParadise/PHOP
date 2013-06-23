<?php
/**
 * PHOP - Base header for all views
 */

if ( !defined('PHOP') )
   exit;

/**
 * Prints the "active" CSS class for the given target menu item, if it is suitable for
 * the current specified action
 *
 * @param string $target Target label of the tab to highlight as active
 */
function printActive($target)
{
   switch ( getOrBlank($_GET, KeyAction) )
   {
      case Actions::Index:
         if ($target == Actions::Index)
            goto active;
         break;

      default:
         if ($target == Actions::Nothing)
            goto active;
         break;
   }

   return;

   active:
   echo 'active';
}

/**
 * Prints each menu entry corresponding to each allowed asset directory, if they exist.
 * If none exist or none are configured, prints a disabled menu item instead.
 */
function printIndexEntries()
{
   $validDirs = [];

   foreach (Config::$AssetDirectories as $dir)
      if ( is_dir($dir) )
         array_push($validDirs, $dir);

   if ( empty($validDirs) )
      echo '<li class="disabled"><a tabindex="-1" href="#">No valid directories configured</a></li>';
   else
      foreach ($validDirs as $dir)
      {
         $action = Actions::Index;
         $url    = pathJoin([Config::PublicUrl,"?action=$action&q=/$dir"]);
         echo "<li><a tabindex=\"-1\" href=\"$url\">$dir</a></li>";
      }

}

?>
<!DOCTYPE html>
<html>
<head>
   <title><?php echo $View->Title ?></title>

   <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
   <link href='http://fonts.googleapis.com/css?family=Merriweather+Sans:400,700&subset=latin,latin-ext' rel='stylesheet'>
   <link href="css/phop.css" rel="stylesheet">
</head>

<body>
<header class="row">
   <div class="container">
      <h1>
         P<small>HP</small>
         H<small>andler for</small>
         O<small>bject</small>
         P<small>aths</small>
      </h1>
   </div>

   <div class="container navbar">
      <div class="navbar-inner">
         <ul class="nav">
            <li class="<?php printActive(Actions::Nothing) ?>"><a href=".">Home</a></li>
            <li class="<?php printActive(Actions::Index) ?> dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  Indexes
                  <b class="caret"></b>
               </a>
               <ul class="dropdown-menu">
                  <?php printIndexEntries(); ?>
               </ul>
            </li>
         </ul>
      </div>
   </div>
</header>
<!-- End base.header -->