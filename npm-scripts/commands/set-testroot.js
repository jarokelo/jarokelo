'use strict';

var setup = require('npm-scripts/modules/setup');

var SetTestrootCommand = {};

SetTestrootCommand.run = function() {
    setup.setTestroot();
};

module.exports = SetTestrootCommand;
