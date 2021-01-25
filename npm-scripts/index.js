'use strict';

var path = require('path');
var argv = require('minimist')(process.argv.slice(2));

require('app-module-path').addPath(path.resolve(__dirname, '..'));

if (argv.name) {
    try {
        var command = require(path.join('npm-scripts', 'commands', argv.name));
        command.run(argv);
    } catch (e) {
        console.log(e);
    }
} else {
    console.log('Require commands name parameter');
}
