using System;

namespace VWAS
{
    static class Defaults
    {
        public const string    Host     = "*";
        public const ushort    Port     = 45537;
        public const LogLevels LogLevel = LogLevels.Production;
    }

    static class Strings
    {
        public const string ArgsHelpTitle = "\n### Command-line configuration:";
        public const string ArgsHelp = @"
VWAS is mostly configured via the web admin panel ( e.g. http://localhost:45537/admin )
but application-startup options such as logging level, hostname and port are configured
via command-line arguments, as explained below:
";
    }
}
