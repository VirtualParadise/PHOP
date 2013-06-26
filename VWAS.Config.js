/*
 * VWAS : Configuration file for this instance of VWAS
 */

var Log = require("./VWAS/Logging");

/**
 * Core configuration object for all of VWAS
 */
var Config = {
    /**
     * Logging
     */
    Log : {
        // Bitwise flags of enabled logging levels
        Level : Log.Levels.All,
    },

    /**
     * Asset serving
     */
    Assets : {
        // If asset serving (e.g. http://server.com/models/model.zip) is enabled.
        // Should always be enabled, except for emergencies or servicing
        Enabled     : true,
        Host        : "0.0.0.0",
        Port        : 8080,
        // Global list of directories the server will serve from
        Directories : ['models', 'textures', 'avatars'],
    },

    /**
     * Local sources
     */
    Local : {
        // If true, assets can be served from local stores (e.g. base content)
        Enabled : true,
        Sources : [
            {
                Location    : "Storage/Local",
                // Directories : ['models', 'textures', 'avatars'],
            },
        ]
    },

    /**
     * Remote sources
     */
    Remote : {
        Enabled   : true,
        Cache     : true,
        CachePath : "Storage/Cache",
        Sources   : [
            {
                Location    : "http://objectpath.com",
                // Directories : ['models', 'textures', 'avatars'],
                // Cache       : true,
            },
        ]
    },

    /**
     * User uploads
     */
    Upload : {
        Enabled : true,
        Path    : "Storage/Upload",
    }
};

module.exports = Config;