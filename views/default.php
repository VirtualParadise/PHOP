<?php
/**
 * PHOP - Default page
 */
require_once 'logic/LocalStorage.php';

if ( !defined('PHOP') )
   exit;

$Data['models']   = getDirectoryCount('models');
$Data['textures'] = getDirectoryCount('textures');
?>

<div class="container">
   <h1>
      This object path is serving <strong><?php echo $Data['models'] ?></strong> models
      and <strong><?php echo $Data['textures'] ?></strong> textures.
   </h1>
</div>