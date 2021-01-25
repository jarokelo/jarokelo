'use strict';

var setup = require('npm-scripts/modules/setup');

var SetEnvironmentCommand = {};

SetEnvironmentCommand.run = function() {
    setup.setEnvironment();
};

module.exports = SetEnvironmentCommand;
