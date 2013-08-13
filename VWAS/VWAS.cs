using System;
using System.Threading;
using System.Reflection;
using Anna;
using Anna.Request;
using PowerArgs;

namespace VWAS
{
    class VWAS
    {
        const string tag = "VWAS";

        static bool exiting;
        public static bool Exiting
        {
            get { return exiting; }
        }

        public static Version    Version;
        public static ArgsConfig Arguments;
        public static HttpServer Server;

        // Vanity value; assets served over life-span
        public static ulong Served = 0;

        static void Main(string[] args)
        {
            Version = Assembly.GetExecutingAssembly().GetName().Version;
            TConsole.WriteLineColored(ConsoleColor.White, "### Virtual World Asset Server, {0}", Version);

            if ( !parseArgs(args) )
                return;

            setup();

            Log.Debug(tag, "Entering main loop...");
            while (!Exiting)
            {
                Thread.Sleep(100);
            }
            Log.Debug(tag, "Exiting main loop...");

            takedown();
            TConsole.WriteLineColored(ConsoleColor.White, "### Exiting after serving {0} assets", Served);
        }

        public static void Exit()
        {
            if (exiting)
                return;
            else
                exiting = true;
        }

        #region Arguments handling
        static bool parseArgs(string[] args)
        {
            try
            {
                Arguments = Args.Parse<ArgsConfig>(args);

                if ( Arguments.Help )
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
            var usage = ArgUsage.GetUsage<ArgsConfig>();
            TConsole.WriteColored(ConsoleColor.Cyan, Strings.ArgsHelpTitle);
            Console.WriteLine(Strings.ArgsHelp);
            Console.Write(usage);
        } 
        #endregion

        #region Application setup
        static void setup()
        {
            setupLogger();

            Log.Debug(tag, "Setup complete");
        }

        static void setupLogger()
        {
            var clogger = new ConsoleLogger
            {
                GroupSimilar = false,
                TagPadding = 12
            };

            Log.Loggers.Add(clogger);
            Log.Level = Arguments.LogLevel;
            Log.Debug(tag, "Log level set to {0}", Log.Level);
        } 
        #endregion

        #region Application takedown
        static void takedown()
        {
            Log.Debug(tag, "Takedown complete");
        } 
        #endregion
    }

}
