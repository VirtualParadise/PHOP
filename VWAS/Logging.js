/*
 * VWAS : Container object for logging functions and configuration loading. Uses the
 * Winston external module.
 */
"use strict";

var Util       = require('util');
var Log        = {};
module.exports = Log;

/**
 * Bitwise logging levels available
 * @type {object.<string, number>}
 */
Log.Levels = {
    Critical : 1,
    Error    : 2,
    Warning  : 4,
    Notice   : 8,
    Info     : 16,
    Debug    : 32,
    Fine     : 64,
    Finer    : 128,

    All        : 255,
    Production : 15,
    Debugging  : 63
};

/**
 * Bitwise flags of logging levels that are enabled
 * @type {number}
 */
Log.Level = Log.Levels.All;

/**
 * Configures the logger with a given VWAS {@link Config}
 * @param {Config} config
 */
Log.Configure = function(config)
{
    Log.Level = config.Log.Level;
    Log.Debug('Logging', 'Configured and setup', {Level : Log.Level});
};

/**
 * Emits a log message to console if allowed by the currently set {@link Log.Level}
 * @param {string} level   Intended level of the message as key of {@link Log.Levels}
 * @param {string} tag     Common tag or category for source of message
 * @param {string} message Message content itself
 * @param {object} objects Objects to be inspected by {@link console.log}
 */
Log.Emit = function(level, tag, message, objects)
{
    if ( (Log.Level & Log.Levels[level]) === 0 )
        return;

    var msg = Util.format("%s | [%s] %s", level, tag, message);
    console.log(msg, objects);
};

/**
 * Public logging methods
 * Not dynamically generated to allow for IDE intellisense
 */

Log.Critical = function(tag, message, objects) { Log.Emit('Critical', tag, message, objects); };
Log.Error    = function(tag, message, objects) { Log.Emit('Error', tag, message, objects); };
Log.Warning  = function(tag, message, objects) { Log.Emit('Warning', tag, message, objects); };
Log.Notice   = function(tag, message, objects) { Log.Emit('Notice', tag, message, objects); };
Log.Info     = function(tag, message, objects) { Log.Emit('Info', tag, message, objects); };
Log.Debug    = function(tag, message, objects) { Log.Emit('Debug', tag, message, objects); };
Log.Fine     = function(tag, message, objects) { Log.Emit('Fine', tag, message, objects); };
Log.Finer    = function(tag, message, objects) { Log.Emit('Finer', tag, message, objects); };