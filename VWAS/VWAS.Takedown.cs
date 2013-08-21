using System;
using System.Threading;

namespace VWAS
{
    partial class VWAS
    {
        static void takedown()
        {
            takedownProviders();
            takedownServer();

            Log.Debug(tag, "Takedown complete");
        }

        static void takedownProviders()
        {
            foreach (var provider in Providers)
                provider.Close();

            Log.Debug(tag, "All providers closed");
        }

        static void takedownServer()
        {
            while (Processing != 0)
                Thread.Sleep(100);
             
            if (Server != null)
                Server.Stop();

            if (Router != null)
                Router.DismountAll();

            Log.Debug(tag, "Server and router taken down");
        }
    }
}
