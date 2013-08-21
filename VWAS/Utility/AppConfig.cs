using PowerArgs;
using System;

namespace VWAS
{
    [ArgExample("VWAS -p 80", "Runs VWAS on the standard HTTP port 80, mimicking a web server")]
    [ArgExample("VWAS -h localhost -p 80", "Runs VWAS on the standard HTTP port 80 but only serving localhost, for testing")]
    [ArgExample("VWAS -h example.com", "Makes VWAS only serve requests to 'example.com'")]
    public class AppConfig
    {
        [ArgDescription("Shows the command-line help")]
        [ArgShortcut("?")]
        public bool Help { get; set; }

        [ArgDescription("Defines the hostname for VWAS to serve. Use '*' for all")]
        [ArgShortcut("h")]
        [DefaultValue(Defaults.Host)]
        public string Host { get; set; }

        [ArgDescription("Defines the port for VWAS to serve")]
        [DefaultValue(Defaults.Port)]
        public int Port { get; set; }

        [ArgDescription("Sets the logging level used by VWAS")]
        [DefaultValue(Defaults.LogLevel)]
        public LogLevels LogLevel { get; set; }

        [ArgIgnore]
        public string URL
        {
            get
            {
                return new UriBuilder()
                {
                    Scheme = "http",
                    Host   = Host,
                    Port   = Port,
                    Path   = "/", 
                }.ToString();
            }
        }
    }
}
