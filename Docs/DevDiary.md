# Development diary
This is a running commentary on the porting and development of VWAS. I write this in the
hopes that information and critiques added will be of use to other developers, both of
JS applications and those building on VWAS.

*These articles are written in no particular order*

## On confusing Javascript patterns
I am finding difficult in node.js difficult due to strange patterns used in external
modules such as `connect.js`. In Node.js, modules (i.e. files) are written in such a way
that anything they want to make public for other modules must be set or added to the
`modules.exports` object.

For example, if `Logger.js` is a module that provides a logging class:
 ```javascript
 var Log        = {};
 module.exports = Log;

 Log.EmitMessage = function (level, message)
 {
   // do something
 }
 ```

By setting `module.exports` to an object that we then modify, that object is what gets
returns when using the native Node.js `require` function, e.g. in `Main.js`:
 ```javascript
 var Log = require('Logger.js');

 Log.EmitMessage("Info", "Hello");
 ```

Some code examples and module use this strange line to export objects (for example):
 ```javascript
 exports = module.exports = {
   // some object
 }
 ```
I find this to be a confusing and redundant pattern. Simply using "module.exports" should
be sufficient. Furthermore, some modules assign a *constructor* to the export to generate
an object (example from Express):
 ```javascript
 var express = require('express');
 var app = express();
 ```

I think this is a bad pattern. In the IDE, this prevents me from finding out what
`express()` is supposed to return or what I can do with it, unless I use the (sometimes
terse) documentation. Other modules abuse this constructor/factory pattern in other ways:
 ```javascript
 winston.add(winston.transports.File, { filename: 'somefile.log' });
 winston.remove(winston.transports.Console);
 ```

This code makes it look like `winston.transports.*` are static instances, but this is not
the case. They are factory functions and they are called by `.add` and `.remove`. It
would make more sense if they were constructors for classes instead:
 ```javascript
 winston.add(new winston.transports.File({ filename: 'somefile.log' });
 ```

## Connect and Middleware
Replaced `express.js` with `connect.js`. Express'
documentation was too terse and its code was not suitably documented for use with an IDE.
Express is touted as an augment to Connect.js, however its [website](http://expressjs.com)
did not sufficiently explain how.

However, Connect.js' [website](http://www.senchalabs.org/connect/) is just as terse, if
not worse. There is **no** documentation of connect itself, just the "middleware" it uses.

*Middleware* - This term confused me greatly; it appears to be common in node.js dev
circles. It simply just describes a delegate (first-class function) that accepts "`req`uest"
and "`res`ponse" parameters and returns nothing. So when such a framework as
connect describes `.use`ing a "middleware", it literally just means a function such as this:

 ```javascript
// This "middleware" just ends an incoming request with a response
function (req, res)
{
    res.end("Hello world!");
}
 ```

## Logging
Removed `winston`; preferred tag-centric logging design. I use such a design in my C#
applications, which I find is easier to pinpoint logs to specific modules. See
`VWAS/Logging.js` for implementation.

## On Go and arrogance
One language I considered switching to was [Go](http://golang.org/) from Google. General
consensus was that it was fast and had a very well designed and filled
[library](http://golang.org/pkg/).

Whilst reading up on the language, one thing that struck me was the strict style rules
and concepts laid out by Google, to encourage better coding. For example, it is considered
a compiler error if a package is imported, but not used.

There are no compiler warnings; Google argues this just adds to noise and warnings may as
well be errors. I agree; it is a good idea in retrospect.

However, I also thought I agreed with the strict syntax, until I found I could not do my
preferred style of code, which I think is cleaner. For example, in C#, I can do braceless
statements:

 ```csharp
 if (true)
   return;
 ```

Go is inflexible in this regard; it is required to type out the above as so:
 ```go
 if true {
 // <-- Opening brace cannot go on this line, a la C#
   return
 }
 ```

Strange, considering brackets are optional for the `if` condition itself. This arrogance
of style made me drop the language from consideration.

## Moving away from PHP
I did not think I could continue PHOP in PHP. I used it because it was easy to quickly
write a multipath script for it. Now I want to take it further with advanced prim
generators, plugins and asset sourcing. This requires classes/objects and in-memory stores
for cache.

PHP has class support, but the syntax is poor. It also does not make sense, as PHP is a
run-once language by design.*

<sup>* After writing this line, I remembered this was wrong; PHP can execute scripts as
applications. I went on to do some research and found [ReactPHP](http://reactphp.org/).
I will go ahead and port PHOP to that and see where I go from there.