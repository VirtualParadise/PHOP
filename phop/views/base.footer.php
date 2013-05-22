<?php
/**
 * PHOP - Base footer for all views
 */

if ( !defined('PHOP') )
   exit;
?>

<!-- Begin base.footer -->
<footer class="row">
   <div class="container">
      Serving the object path at <?php echo $_SERVER['HTTP_HOST'] ?> for user <?php echo $_SERVER['REMOTE_ADDR'] ?>
   </div>
</footer>
</body>

<scripts>
   <script src="//code.jquery.com/jquery-2.0.0.min.js"></script>
   <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
</scripts>
</html>