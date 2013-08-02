using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using Nancy;
using Nancy.Hosting.Self;

namespace VWAS
{
    class VWAS
    {
        static void Main(string[] args)
        {
            var nancy = new HelloModule();
            var host  = new NancyHost( new Uri("http://localhost:8181") );

           
            Log.Loggers.Add( new ConsoleLogger() );
            Log.Level = LogLevels.All;

            host.Start();
            Console.ReadLine();
            host.Stop();
        }
    }

    public class HelloModule : NancyModule
    {
        public HelloModule()
        {
            Get["/"] = async parameters =>
            {
                Log.Fine("Server", "Beginning request...");
                await Task.Delay(1000);
                Log.Fine("Server", "Ending request...");
                return "Hello World";
            };
        }
    }
}
