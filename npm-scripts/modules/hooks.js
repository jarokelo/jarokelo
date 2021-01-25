'use strict';

var fs = require('fs');
var path = require('path');
var Promise = require('pinkie-promise');
var config = require('gulp/config.json');
var existsSync = require('npm-scripts/modules/exists-sync');

var hooks = {};

hooks.createHooks = function() {
    var dockerized = process.env.DOCKERIZED === 'true';
    var promise = function(resolve, reject) {
        var i = config.hooks.length;
        var hookDir = path.join('.git', 'hooks');
        if (!existsSync(hookDir)) {
            fs.mkdirSync(hookDir);
        }
        while (i--) {
            var dest = path.join(hookDir, config.hooks[i].dest);

            if (config.hooks[i].docker && dockerized) {
                // hook must run in docker, but git is outside, don't install it
                continue;
            }
            if (existsSync(dest)) {
                fs.unlinkSync(dest);
            }

            var src = path.join('hooks', config.hooks[i].src);
            fs.createReadStream(src).pipe(fs.createWriteStream(dest, {
                mode: 511
            }));
        }

        if (i === -1) {
            resolve();
        } else {
            reject();
        }
    };

    return new Promise(promise);
};

module.exports = hooks;
