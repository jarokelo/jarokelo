'use strict';

var envExists = require('npm-scripts/modules/check-env');
var setup = require('npm-scripts/modules/setup');
var hooks = require('npm-scripts/modules/hooks');

var PostInstallCommand = {};

PostInstallCommand.run = function() {
    if (envExists()) {
        process.exit(0);
    } else {
        hooks.createHooks()
            .then(setup.setEnvironment)
            .then(setup.setEmail)
            .then(setup.setTestroot)
            .then(setup.setSentry);
    }
};

module.exports = PostInstallCommand;
