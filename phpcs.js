/* jshint node: true */
'use strict';

var config = require('./gulp/config.json');
var gs = require('glob-stream');
var through = require('through2');
var exec = require('child_process').exec;

var bin = config.phpcs.options.bin || 'phpcs';
var errored = false;
for (var target in config.phpcs) {
    if (target === 'options') {
        continue;
    }

    var dirs = config.phpcs[target].dir;
    var standard = config.phpcs[target].standard;

    var stream = gs.create(dirs);
    (function(bin, standard, stream) {
        stream.pipe(through.obj(function(file, enc, callback) {
            var _this = this;

            // Run code sniffer
            var command = bin + ' --standard="' + standard + '" ' + file.path;
            exec(command, function(error, stdout, stderr) {
                if (error) {
                    console.log(stdout);
                    console.log(stderr);
                    errored = true;
                }

                _this.push(file);
                callback();
            });
        })).pipe(through.obj(function(file, enc, callback) {
            callback();
        }));
    })(bin, standard, stream);
}

process.on('exit', function() {
    if (errored) {
        process.exit(1);
    }
});
