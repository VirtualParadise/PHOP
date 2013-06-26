/*
 * VWAS : Main module and application entry point
 */
"use strict";

/**
 * Core application state
 * @type {object}
 */
var VWAS = {
    Log    : require('./VWAS/Logging'),
    Config : require('./VWAS.Config.js'),
};

/**
 * Main entry block of VWAS
 */
VWAS.Log.Configure(VWAS.Config);
