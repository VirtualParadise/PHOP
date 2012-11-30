PHOP - A PHP asset serving script for object path-type VR applications, such as Active Worlds and Virtal Paradise.

Coded by Roy Curtis, with prim generation adapted from code by Epsilion.

# Plugins
All plugins begin with a letter and colon, e.g. "p:"

## Prim generation
All prims are models that begin with "p:"
### Walls

    p:wall####p,s/p/###,####,####
    
#### Parts
* wall/panel: Can be "w", "wll" or "wall" for a double sided wall, or "p", "pan" or "panel" for single sided
* `####`: Size in millimeters. This can be a single number for a square:

    `p:w0100 for 1 meter sq. wall`
    `p:w5.5 for 5.5 millimeter sq. wall`

* or in the style of ####x#### for non-square:

    `p:w100x500 for 1 meter by 5 meter wall`
    
* a 'p' can be added after the number to enable phantom mode (no collision; not yet supported in VP)

    `p:w100p for 1 meter sq. phantom wall`
    
* a custom tag parameter can be defined to make the object accept picture (default), sign or custom number
    
    `p:w0500,s for 5 meter sq. sign wall`
    `p:w0500,100 for 5 meter sq. sign wall`

* a custom UV scale parameter can be defined to make textures repeat more or less. this can be one value, or two for custom Y scale

    `p:w500,s,10 for 5 meter sq. sign wall with 10 textures per sq. meter`
    `p:w500,s,10,20 for 5 meter sq.`

## Imgur alias
Instead of...

    create texture http://i.imgur.com/ViPP1.jpg
    
... try this:

    create texture i:ViPP1