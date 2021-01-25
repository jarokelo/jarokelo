'use strict';

var setup = require('npm-scripts/modules/setup');

var SetSentryCommand = {};

SetSentryCommand.run = function() {
    setup.setSentry();
};

module.exports = SetSentryCommand;
