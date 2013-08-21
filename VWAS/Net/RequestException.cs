using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace VWAS
{
    class RequestException : Exception
    {
        public RequestException(int code, string msg, params object[] parts)
            : base( msg.LFormat(parts) )
        {

        }
    }
}
