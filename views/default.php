<?php
/**
 * PHOP - Default page
 */
require_once 'logic/PHOP.php';
require_once 'logic/LocalStorage.php';

if ( !defined('PHOP') )
   exit;

$Data['models']   = getDirectoryCount('models');
$Data['textures'] = getDirectoryCount('textures');
$Data['url']      = PUBLIC_URL.'?q=';
?>

<div class="container">
   <h1>
      This object path is serving <strong><?php echo $Data['models'] ?></strong> models
      and <strong><?php echo $Data['textures'] ?></strong> textures.
   </h1>

   <label>
      To use this object path, mouseover and copy the following URL:

      <input
         readonly
         type        = "text"
         id          = "default-opurl"
         onmouseover = "this.select()"
         value       = "<?php echo $Data['url'] ?>" />
   </label>
</div>