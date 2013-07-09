<?php
/**
 * PHOP : Application-wide HTTP server using react/http; acts as a router that matches
 * pattern routes
 */

namespace PHOP\Net;

use React\EventLoop\Factory as ReactEventLoop;
use React\EventLoop\LoopInterface;
use React\Socket\Server as ReactSocket;
use React\Http\Server as ReactHTTP;
use PHOP\Config as Config;

/**
 * Static class that acts as the core HTTP request router/server for PHOP
 */
class HTTPServer
{
   /** @var LoopInterface */
   private static $loop;
   /** @var ReactSocket */
   private static $socket;
   /** @var HTTPServer */
   private static $server;
   /** @var Route[] */
   private static $routes;

   public static $Enabled = false;
   public static $Host    = '0.0.0.0';
   public static $Port    = 45537;

   /**
    * Configures the server using the global Config static class
    */
   public static function Configure()
   {
      HTTPServer::$Enabled = Config::$Assets['Enabled'];
      HTTPServer::$Host    = Config::$Assets['Host'];
      HTTPServer::$Port    = Config::$Assets['Port'];

      HTTPServer::$loop   = ReactEventLoop::create();
      HTTPServer::$socket = new ReactSocket(HTTPServer::$loop);
      HTTPServer::$server = new HTTPServer(HTTPServer::$socket, HTTPServer::$loop);
   }

   public static function Begin()
   {

   }

   public static function Mount(Route $route)
   {
      if in_array()
      {
         
      }
   }
}