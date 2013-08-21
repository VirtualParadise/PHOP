using System;
using System.Collections.Generic;
using System.Net;
using System.IO;
using System.Text.RegularExpressions;

namespace VWAS
{
    class StaticProvider : IProvider
    {
        const string tag = "Statics";

        public const string RemotePath = "/_statics/";
        public const string LocalPath  = "Statics";

        public void Setup(Router router)
        {
            if ( !Directory.Exists(LocalPath) )
                throw new DirectoryNotFoundException("Statics directory is missing from VWAS root folder");

            router.Mount( new Route
            {
                Id      = tag,
                Pattern = Path.Combine(RemotePath, "(.+)$"),
                Handler = onRequest,
                Method  = Methods.GET
            } );
        }
        
        public void Close()
        {
        }

        public string GetStaticFile(string request)
        {
            return Path.Combine(LocalPath, request);
        }

        bool onRequest(Request ctx, string[] matches)
        {
            var request = matches[1];
            var path    = GetStaticFile(request);
            
            if ( !File.Exists(path) )
            {
                ctx.NativeResponse.StatusCode        = (int) HttpStatusCode.NotFound;
                ctx.NativeResponse.StatusDescription = "Static resource could not be found";
                ctx.NativeResponse.Close();
            }
            else
            {
                var data = File.ReadAllBytes(path);

                ctx.NativeResponse.StatusCode = (int) HttpStatusCode.Found;
                ctx.NativeResponse.Close(data, false);
            }

            return true;
        }
    }
}
