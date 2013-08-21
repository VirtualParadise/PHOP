using System.Collections.Generic;
using System.Text.RegularExpressions;
using System.Net;

namespace VWAS
{
    delegate bool RouteHandler(Request context, string[] matches);

    struct Route
    {
        public string       Id;
        public string       Pattern;
        public RouteHandler Handler;
        public string       Method;
    }
}
