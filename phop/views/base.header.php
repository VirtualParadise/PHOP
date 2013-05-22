<?php
/**
 * PHOP - Base header for all views
 */

if ( !defined('PHOP') )
   exit;

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
            <li class="active"><a href="">Home</a></li>
            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  Indexes
                  <b class="caret"></b>
               </a>
               <ul class="dropdown-menu">
                  <li><a tabindex="-1" href="#">Action</a></li>
                  <li><a tabindex="-1" href="#">Another action</a></li>
                  <li><a tabindex="-1" href="#">Something else here</a></li>
               </ul>
            </li>
         </ul>
      </div>
   </div>
</header>
<!-- End base.header -->