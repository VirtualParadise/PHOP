using PowerArgs;
using System;
using System.Collections.Generic;
using System.Net;
using System.Reflection;
using System.Threading;

namespace VWAS
{
    partial class VWAS
    {
        const  string tag   = "VWAS";
        static object mutex = new object();

        static bool exiting;
        public static bool Exiting
        {
            get { return exiting; }
        }

        static uint processing;
        public static uint Processing
        {
            get
            {
                lock (mutex)
                    return processing;
            }

            set
            {
                lock (mutex)
                    processing = value;
            }
        }

        static ulong served;
        public static ulong Served
        {
            get
            {
                lock (mutex)
                    return served;
            }

            set
            {
                lock (mutex)
                    served = value;
            }
        }

        public static Version      Version;
        public static AppConfig    Config;
        public static Router       Router;
        public static HttpListener Server;

        public static List<IProvider> Providers;

        static void Main(string[] args)
        {
            Version = Assembly.GetExecutingAssembly().GetName().Version;
            TConsole.WriteLineColored(ConsoleColor.White, "### Virtual World Asset Server, {0}", Version);

            if ( !parseArgs(args) )
                return;

            setup();

            Log.Debug(tag, "Entering main loop...");
            while (!Exiting)
                ThreadPool.QueueUserWorkItem( Serve, Server.GetContext() );
            Log.Debug(tag, "Exiting main loop...");

            takedown();
            TConsole.WriteLineColored(ConsoleColor.White, "### Exiting after serving {0} assets", Served);
        }

        public static void Serve(object state)
        {
            Processing++;
            var context = state as HttpListenerContext;

            Router.Incoming(context);
            Processing--;
        }

        public static void Exit()
        {
            lock (mutex)
            {
                if (exiting)
                    return;
            
                exiting = true;
                Log.Info(tag, "Waiting for server to exit...");
            }
        }

        #region Arguments handling
        static bool parseArgs(string[] args)
        {
            try
            {
                Config = Args.Parse<AppConfig>(args);

                if ( Config.Help )
                {
                    printArgsHelp();
                    return false;
                }
                else
                    return true;
            }
            catch ( ArgException e )
            {
                TConsole.WriteLineColored(ConsoleColor.Red, "{0}\n", e.Message);
                printArgsHelp();

                return false;
            }
        }

        static void printArgsHelp()
        {
            var usage = ArgUsage.GetUsage<AppConfig>();
            TConsole.WriteColored(ConsoleColor.Cyan, Strings.ArgsHelpTitle);
            Console.WriteLine(Strings.ArgsHelp);
            Console.Write(usage);
        } 
        #endregion
    }

}
