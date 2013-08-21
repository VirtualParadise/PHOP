using System;
using System.Collections.Generic;
using System.Net;
using System.Linq;
using System.Text.RegularExpressions;

namespace VWAS
{
    class Router
    {
        const string tag = "Router";

        List<Route> routes = new List<Route>();

        public void Incoming(HttpListenerContext context)
        {
            string[] matches;
            var method  = context.Request.HttpMethod;
            var path    = context.Request.Url.AbsolutePath;
            var request = new Request(context);
            request.ApplyDefaults();

            // Exit 1: Route handled request
            foreach (var route in routes)
            {
                if      ( !route.Method.IEquals(method) )
                    continue;
                else if ( !TRegex.TryMatch(path, route.Pattern, out matches) )
                    continue;
                else if ( route.Handler(request, matches) )
                    return;
            }

            // Exit 2: No routes handle request
            request.NativeResponse.StatusCode        = (int) HttpStatusCode.NotImplemented;
            request.NativeResponse.StatusDescription = "Unimplemented route";
            request.NativeResponse.Close();
        }

        public void Mount(Route route)
        {
            if ( routes.Contains(route) )
                return;

            routes.Add(route);
            Log.Debug(tag, "Mounted route '{0}'", route.Id);
        }

        public void Dismount(Route route)
        {
            routes.Remove(route);
            Log.Debug(tag, "Dismounted route '{0}'", route.Id);
        }

        public void DismountAll()
        {
            routes.Clear();
            Log.Debug(tag, "All routes dismounted");
        }
    }
}
