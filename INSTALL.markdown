Create and chmod +0777 the following directories:
models/
textures/
prims/

Open phop.lib.php in a text editor and change the following:

define("SOURCE", "http://objects.activeworlds.com");
to object path you wish to combine and auto-mirror from

define("LOCAL", "http://objectpath.org/op/");
to the internet-facing URL of this script's directory (or location of local storage)

Finally, set your worlds object path to the above url in the following style:
http://objectpath.org/op/?q=