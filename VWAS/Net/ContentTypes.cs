using System.Collections.Generic;
using System.IO;
using System.Linq;

namespace VWAS
{
    struct ContentType
    {
        public static ContentType Default = new ContentType(null, "text/plain");

        static ContentType[] types = new[]
        {
            new ContentType( new[] { ".css" }, "text/css"),
            new ContentType( new[] { ".js" }, "text/javascript"),
            new ContentType( new[] { ".zip", ".seq", ".obj", ".rwx", ".3ds" }, "application/octet-stream"),
            new ContentType( new[] { ".jpg", ".jpeg", ".jpe", ".jif", ".jfif", ".jfi" }, "image/jpeg"),
            new ContentType( new[] { ".gif" }, "image/gif"),
            new ContentType( new[] { ".png" }, "image/png"),
            new ContentType( new[] { ".tga", ".targa" }, "image/x-targa"),
        };

        public string[] Extensions;
        public string   MIMEType;

        public static ContentType Of(string path)
        {
            var info  = new FileInfo(path);
            var ext   = info.Extension;
            var query = from   t in types
                        where  t.Extensions.IContains(ext)
                        select t;

            if ( query.Count() == 0 )
                return Default;
            else
                return query.First();
        }

        ContentType(string[] exts, string mime, bool download = false)
        {
            this.Extensions = exts;
            this.MIMEType   = mime;
        }
    }
}
