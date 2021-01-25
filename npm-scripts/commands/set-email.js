'use strict';

var setup = require('npm-scripts/modules/setup');

var SetEmailCommand = {};

SetEmailCommand.run = function() {
    setup.setEmail();
};

module.exports = SetEmailCommand;
