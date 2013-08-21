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
        static void setup()
        {
            setupLogger();
            setupServer();
            setupProviders();

            Log.Debug(tag, "Setup complete");
        }

        static void setupLogger()
        {
            var clogger = new ConsoleLogger
            {
                GroupSimilar = false,
                TagPadding   = 12
            };

            Log.Loggers.Add(clogger);
            Log.Level = Config.LogLevel;
            Log.Debug(tag, "Log level set to {0}", Log.Level);
        }

        static void setupServer()
        {
            Router = new Router();
            Server = new HttpListener();
            Server.Prefixes.Add(Config.URL);
            Server.Start();

            Log.Debug(tag, "HTTP listener set up at {0}", Config.URL);
        }

        static void setupProviders()
        {
            Providers = new List<IProvider>();
            Providers.Add( new StaticProvider() );

            foreach (var provider in Providers)
                provider.Setup(Router);

            Log.Debug(tag, "{0} providers setup", Providers.Count);
        }
    }
}
