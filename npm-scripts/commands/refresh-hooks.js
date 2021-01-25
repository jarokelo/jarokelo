'use strict';

var hooks = require('npm-scripts/modules/hooks');

var RefreshHooksCommand = {};

RefreshHooksCommand.run = function() {
    hooks.createHooks();
};

module.exports = RefreshHooksCommand;
