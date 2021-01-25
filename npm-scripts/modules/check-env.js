'use strict';

var path = require('path');
var existsSync = require('npm-scripts/modules/exists-sync');

module.exports = function() {
    return existsSync(path.join('config', 'ENV'));
};
