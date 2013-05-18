<?php
/**
 * PHOP - Views manager
 */
require_once 'phop.lib.php';

if ( !defined('PHOP') )
   exit;

class Views
{
   const Main  = 'default';
   const Error = 'error';
}

class View
{
   public $Page;
   public $Title = "PHOP";

   function __construct($page)
   {
      $this->Page = $page;
   }

   function Generate(array $data = [])
   {
      $View = $this;
      $Data = $data;

      require pathJoin([Directories::Views, 'base.header.php'  ]);
      require pathJoin([Directories::Views, "{$this->Page}.php"]);
      require pathJoin([Directories::Views, 'base.footer.php'  ]);
      exit;
   }
}
?>