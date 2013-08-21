using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace VWAS
{
    interface IProvider
    {
        /// <summary>
        /// Should set up any I/O and check any settings to ready self for providing
        /// resources. Passes the router in use to mount any routes.
        /// </summary>
        /// <param name="router"></param>
        void Setup(Router router);

        /// <summary>
        /// Should release any resources
        /// </summary>
        void Close();
    }
}
