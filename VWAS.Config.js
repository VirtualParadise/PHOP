/*
 * VWAS : Configuration file for this instance of VWAS
 */

var Log = require('winston');

/**
 * Core configuration object for all of VWAS
 * @type {object}
 */
var VWASConfig = {
    /**
     * Logging
     */
    Log : {
        Enabled   : true,
        // Minimum log level
        Level     : Log.config.syslog.levels.debug,
        // Should logging be written to disk?
        ToDisk    : true,
        // Path to log file to write/append
        DiskPath  : "vwas.log",
        // Should logging be written to console?
        ToConsole : true,
    },

    /**
     * Asset serving
     */
    Assets : {
        // If asset serving (e.g. http://server.com/models/model.zip) is enabled.
        // Should always be enabled, except for emergencies or servicing
        Enabled     : true,
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