using System.Net;
using System;
using System.IO;
using System.Text.RegularExpressions;
using RazorEngine;

namespace VWAS
{
    class Request
    {
        public HttpListenerRequest  NativeRequest;
        public HttpListenerResponse NativeResponse;

        public Request(HttpListenerContext ctx)
        {
            this.NativeRequest  = ctx.Request;
            this.NativeResponse = ctx.Response;
        }

        public bool IsVRClient()
        {
            var useragent = NativeRequest.UserAgent;

            return useragent.Contains("VirtualParadise");
        }

        public void ApplyDefaults()
        {
            NativeResponse.AddHeader("Server", "VWAS, " + VWAS.Version);
        }

        public void Redirect(string url)
        {
            NativeResponse.StatusCode        = 301;
            NativeResponse.StatusDescription = "Permanent Redirect";
            NativeResponse.RedirectLocation  = url;

            NativeResponse.Close();
        }

        public void ServeView(string path)
        {
            Razor.ParseMany(
        }

        public void ServeFile(byte[] data, string filename, bool download = false)
        {
            NativeResponse.StatusCode      = 302;
            NativeResponse.KeepAlive       = true;
            NativeResponse.ContentType     = ContentType.Of(filename).MIMEType;
            NativeResponse.ContentLength64 = data.Length;

            if (download)
            {
                var disp = "attachment; filename=\"{0}\"".LFormat(filename);
                NativeResponse.AddHeader("Content-Disposition", disp);
            }

            NativeResponse.Close(data, false);
        }

        public void ServeFile(string path, bool download = false)
        {
            var filename = new FileInfo(path).Name;
            var data     = File.ReadAllBytes(path);

            ServeFile(data, filename, download);
        }

        public void ServeError(int code, string error, string message)
        {
            NativeResponse.StatusCode        = code;
            NativeResponse.StatusDescription = error;
            NativeResponse.AddHeader("Reason", message);

            NativeResponse.Close();
        }
    }
}
