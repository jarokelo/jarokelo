'use strict';

// Requires
var fs = require('fs');
var path = require('path');
var gulp = require('gulp');
var $ = require('gulp-load-plugins')({
    rename: {
        'gulp-clip-empty-files': 'clip'
    }
});
var execSync = require('child_process').execSync;
var es = require('event-stream');
var config = require('./gulp/config.json');
var gp = require('get-packages').init({
    applicationPath: '.',
    yiiPackagesCommand: 'yii packages'
});
var lazypipe = require('lazypipe');
var autoprefixer = require('autoprefixer');
var runSequence = require('run-sequence');
var inquirer = require('inquirer');
var Promise = require('pinkie-promise');
var argv = require('minimist')(process.argv.slice(2));

/**
 * List of supported browsers, used by Autoprefixer.
 * Change this to suit your project.
 * See https://github.com/ai/browserslist for documentation of queries.
 */
var AUTOPREFIXER_BROWSERS = [
    'ie >= 9',
    'ff >= 30',
    'chrome >= 34',
    'safari >= 7',
    'opera >= 23',
    'ie_mob >= 10',
    'ios >= 7',
    'android >= 4.4',
    'bb >= 10'
];

/**
 * List of PostCSS processors.
 * See https://www.npmjs.com/browse/keyword/postcss-plugin
 * for a list of plugins.
 */
var PROCESSORS = [
    autoprefixer({
        browsers: AUTOPREFIXER_BROWSERS,
        cascade: false
    })
];

var getAppStyles = function() {
    var stylesPaths = gp.getStylesPaths().filter(function(module) {
        return module.module === '_app';
    }).shift();

    return stylesPaths.sources + '/**/*.{sass,scss}';
};

/**
 * styles builds css files from sass or scss files.
 * @param {boolean} isProd whether to build production css files.
 * @param {string} changedFile if not undefined, only build this file
 * @return {Object} stream
 */
var styles = function(isProd, changedFile) {
    var stylesPaths = gp.getStylesPathsByFilepath(changedFile);
    var streams = stylesPaths.map(function(stylePaths) {
        var dest = isProd ? stylePaths.dist : stylePaths.dev;

        return gulp.src(path.join(stylePaths.sources, '*.s+(a|c)ss'))
            .pipe($.if(!isProd, $.sourcemaps.init()))
            .pipe($.sass({
                precision: 10,
                errLogToConsole: true,
                includePaths: [
                    'vendor/bower'
                ]
            }))
            .pipe($.postcss(PROCESSORS))
            .pipe($.if(isProd, $.csso()))
            .pipe($.rename({dirname: '.'}))
            .pipe($.if(!isProd, $.sourcemaps.write()))
            .pipe(gulp.dest(dest))
            .pipe($.if(!isProd, $.livereload(config.port)));
    });

    return es.merge.apply(null, streams);
};

/**
 * jslintDiff lints changed javascript files.
 * @param {boolean} cached only use git index, don't check the actual file. This is used in the pre-commit hook, since only the index would be committed anyway.
 * @return {Object} stream
 */
var jslintDiff = function(cached) {
    var eslintChannel = lazypipe()
        .pipe($.eslint)
        .pipe($.eslint.format)
        .pipe($.eslint.failAfterError);

    var command = 'git diff --name-only --diff-filter=ACMR HEAD';

    if (cached) {
        command += ' --cached';
    }

    var fileList = execSync(command, {
        'encoding': 'utf8'
    });

    fileList = fileList.split('\n');
    fileList = fileList.map(function(file) {
        return path.resolve(file);
    });

    var scriptsToBuild = gp.getScriptsToBuild();
    var streams = scriptsToBuild.map(function(scriptPaths) {
        var extraParams = gp.getExtraParamsByModule(scriptPaths.package);
        var list = gp.util.match(fileList, scriptPaths.sources);

        return gulp.src(list, {base: process.cwd()})
            .pipe($.if(!extraParams.ignoreErrors, eslintChannel()));
    });

    return es.merge.apply(null, streams);
};

/**
 * phpcsdiff runs phpcs on changed php files.
 * @param {boolean} cached only use git index, don't check the actual file. This is used in the pre-commit hook, since only the index would be committed anyway.
 * @return {Object} stream
 */
var phpcsdiff = function(cached) {
    function streamify(dir, standard, fileList) {
        var filtered = gp.util.match(fileList, dir);

        return gulp.src(filtered)
            .pipe($.phpcs({
                bin: config.phpcs.options.bin,
                standard: standard,
                colors: true,
                showSniffCode: true
            }))
            .pipe($.phpcs.reporter('log'));
    }

    var command = 'git diff --name-only --diff-filter=ACMR HEAD';

    if (cached) {
        command += ' --cached';
    }

    var fileList = execSync(command, {
        'encoding': 'utf8'
    });

    fileList = fileList.split('\n');

    return es.merge.apply(null, [
        streamify(config.phpcs.application.dir, config.phpcs.application.standard, fileList),
        streamify(config.phpcs.views.dir, config.phpcs.views.standard, fileList),
        streamify(config.phpcs.others.dir, config.phpcs.others.standard, fileList)
    ]).pipe($.phpcs.reporter('fail'));
};

gulp.task('svg-sprite', function() {
    var imagePaths = gp.getImagesPaths().map(function(obj) {
        return obj.sources;
    });

    return es.merge(imagePaths.map(function(value) {
        return gulp.src(value.replace('images', 'svg') + '/*.svg')
            .pipe($.rename({
                prefix: 'icon-'
            }))
            // .pipe($.svgmin(function(file) {
            //     var prefix = path.basename(file.relative, path.extname(file.relative));

            //     return {
            //         plugins: [{
            //             cleanupIDs: {
            //                 prefix: prefix + '-',
            //                 minify: false
            //             }
            //         }]
            //     };
            // }))
            .pipe($.svgstore())
            .pipe($.rename('icons.svg'))
            .pipe(gulp.dest(value));
    }));
});

/**
 * makeRelative makes file paths relative.
 * If they are already relative, it first resolves them
 * relative to process.env.INIT_CWD (the original working
 * directory the gulp command was invoked from).
 * @param {mixed} files files, string or array
 * @return {mixed} relative file paths, string or array
 */
var makeRelative = function(files) {
    var resolve = function(file) {
        return path.relative(process.cwd(), path.resolve(process.env.INIT_CWD, file));
    };

    if (!Array.isArray(files)) {
        return resolve(files);
    }
    return files.map(function(file) {
        return resolve(file);
    });
}

/**
 * phpcs runs phpcs on the specified globs with the specified standard.
 * @param {mixed} globs globs to check with phpcs
 * @param {string} standard the standard to use
 * @return {Object} stream
 */
var phpcs = function(globs, standard) {
    if (typeof argv.file !== 'undefined') {
        globs = gp.util.match(makeRelative(argv.file), globs);
    }

    return gulp.src(globs)
        .pipe($.phpcs({
            bin: config.phpcs.options.bin,
            standard: standard,
            colors: true,
            showSniffCode: true
        }))
        .pipe($.phpcs.reporter('log'));
};

/**
 * phpcbf runs phpcbf on the specified globs with the specified standard.
 * @param {mixed} globs globs to check with phpcs
 * @param {string} standard the standard to use
 * @return {Object} stream
 */
var phpcbf = function(globs, standard) {
    if (typeof argv.file !== 'undefined') {
        globs = gp.util.match(makeRelative(argv.file), globs);
    }
    return gulp.src(globs, {base: './'})
        .pipe($.clip())
        .pipe($.phpcbf({
            bin: config.phpcs.options.fixerbin,
            standard: standard
        }))
        .on('error', function(e) {
            throw e;
        })
        .pipe(gulp.dest('./'))
        .pipe($.phpcs({
            bin: config.phpcs.options.bin,
            standard: standard,
            colors: true,
            showSniffCode: true
        }))
        .pipe($.phpcs.reporter('log'));
};

/**
 * cleanDiff removes different files between sources and destination
 * @param {Array} sources sources path
 * @param {Array} dest destination path with glob
 * @return {Object} stream
 */
var cleanDiff = function(sources, dest) {
    return gulp.src(dest)
        .pipe($.changed(sources, {
            hasChanged: function(stream, cb, sourceFile, targetPath) {
                try {
                    fs.lstatSync(targetPath);
                } catch (e) {
                    stream.push(sourceFile);
                }

                cb();
            }
        }))
        .pipe((function() {
            return es.map(function(file, cb) {
                if (!fs.statSync(file.path).isDirectory()) {
                    fs.unlinkSync(file.path);
                }

                cb(null);
            });
        })());
};

/**
 * styles:findport runs findport first to find a port for
 * LiveReload, then builds styles in development mode.
 */
gulp.task('styles:findport', ['findport'], function() {
    return styles(false);
});

/**
 * styles:dist builds styles in production mode.
 */
gulp.task('styles:dist', function() {
    return styles(true);
});

/**
 * jslint checks all the javascript files.
 */
gulp.task('jslint', function() {
    var eslintChannel = lazypipe()
        .pipe($.eslint)
        .pipe($.eslint.format)
        .pipe($.eslint.failAfterError);

    var scriptsToBuild = gp.getScriptsToBuild();

    var streams = scriptsToBuild.map(function(scriptPaths) {
        var extraParams = gp.getExtraParamsByModule(scriptPaths.package);

        return gulp.src(scriptPaths.sources, {base: process.cwd()})
            .pipe($.if(!extraParams.ignoreErrors, eslintChannel()));
    });

    return es.merge.apply(null, streams);
});

/**
 * jslintdiffwithcached checks staged javascript files.
 */
gulp.task('jslintdiffwithcached', function() {
    return jslintDiff(true);
});

/**
 * jslintdiff checks modified and staged javascript files.
 */
gulp.task('jslintdiff', function() {
    return jslintDiff(false);
});

/**
 * scripts builds production javascript files.
 */
gulp.task('scripts', ['jslint'], function() {
    var scriptsToBuild = gp.getScriptsToBuild();
    var streams = scriptsToBuild.map(function(scriptPaths) {
        return gulp.src(scriptPaths.sources)
            .pipe($.concat(scriptPaths.concatFilename))
             // .pipe($.ngAnnotate()) // ng-annotate (npm install --save gulp-ng-annotate)
            .pipe($.uglify({
                compress: {
                    drop_console: true
                }
            }))
            .pipe(gulp.dest(scriptPaths.dest));
    });

    return es.merge.apply(null, streams);
});

/**
 * images optimizes images and saves them to the production image directories.
 */
gulp.task('images', function(cb) {
    var imagesPaths = gp.getImagesPaths();

    if (imagesPaths.length === 0) {
        return cb();
    }

    var streams = imagesPaths.map(function(imagePaths) {
        return gulp.src(path.join(imagePaths.sources, '**', '*.{png,jpg,jpeg,gif}'))
            .pipe($.changed(imagePaths.dest))
            .pipe($.imagemin({
                optimizationLevel: 3,
                progressive: true,
                interlaced: true
            }))
            .pipe(gulp.dest(imagePaths.dest));
    });

    return es.merge.apply(null, streams);
});

/**
 * fonts copies fonts to the production font directories.
 */
gulp.task('fonts', function(cb) {
    var fontsPaths = gp.getFontsPaths();

    if (fontsPaths.length === 0) {
        return cb();
    }

    var streams = fontsPaths.map(function(fontPaths) {
        return gulp.src(path.join(fontPaths.sources, '**', '*.{woff,woff2,otf,ttf,svg,eot}'))
            .pipe(gulp.dest(fontPaths.dest));
    });

    return es.merge.apply(null, streams);
});

/**
 * clean:without-images cleans the production directories except the image directories.
 * @internal
 */
gulp.task('clean:without-images', function() {
    var del = require('del');

    return del(gp.getPackagesDistPathWithoutImageDir());
});

/**
 * clean removes old files from the production directories.
 */
gulp.task('clean', ['clean:without-images'], function() {
    var imagesPaths = gp.getImagesPaths();

    // images are only removed if the source file no longer exists or has changed,
    // to avoid optimizing unchanged images every time.
    imagesPaths.map(function(imagePaths) {
        return cleanDiff(imagePaths.sources, path.join(imagePaths.dest, '**', '*'));
    });
});

/**
 * compile builds production assets.
 */
gulp.task('compile', function(cb) {
    runSequence('clean', [
        'styles:dist',
        'scripts',
        // 'svg-sprite',
        'images',
        'fonts',
        'copy-non-images',
        'copy-other-paths'
    ], cb);
});

/**
 * build builds production assets.
 * build is an alias for compile.
 */
gulp.task('build', ['compile']);

/**
 * copy-non-images copies files that cannot be optimized to the production image directories.
 * These files cannot be handled by the images task, and would therefore be missing from
 * the production directories.
 * @internal
 */
gulp.task('copy-non-images', function(cb) {
    var imagesPaths = gp.getImagesPaths();

    if (imagesPaths.length === 0) {
        return cb();
    }

    var streams = imagesPaths.map(function(imagePaths) {
        return gulp.src(path.join(imagePaths.sources, '**', '!(*.png|*.jpg|*.jpeg|*.gif)'))
            .pipe(gulp.dest(imagePaths.dest));
    });

    return es.merge.apply(null, streams);
});

/**
 * copy-other-paths copies from development other directory to production directory on build
 */
gulp.task('copy-other-paths', function(cb) {
    var otherPaths = gp.getOtherPaths();

    if (otherPaths.length === 0) {
        return cb();
    }

    var streams = otherPaths.map(function(otherPath) {
        return gulp.src(path.join(otherPath.sources, '**', '*.*'))
            .pipe(gulp.dest(otherPath.dest));
    });

    return es.merge.apply(null, streams);
});

/**
 * phplint runs the php linter on staged files.
 * It is used by the pre-commit hooks.
 * @internal
 */
gulp.task('phplint', function(cb) {
    var phplint = require('phplint').lint;

    var fileList = execSync('git diff --cached --name-only --diff-filter=ACMR HEAD', {
        'encoding': 'utf8'
    });

    fileList = fileList.split('\n');
    fileList = fileList.filter(function(file) {
        return path.extname(file) === '.php';
    });

    phplint(fileList, {limit: 100}, function(err) {
        if (err) {
            cb(err);
            process.exit(1);
        }

        cb();
    });
});

/**
 * phpcs runs PHP CodeSniffer on all php files.
 * The configuration for this task is in gulp/config.json.
 */
gulp.task('phpcs', function() {
    return es.merge.apply(null, [
        phpcs(config.phpcs.application.dir, config.phpcs.application.standard),
        phpcs(config.phpcs.views.dir, config.phpcs.views.standard),
        phpcs(config.phpcs.others.dir, config.phpcs.others.standard)
    ]).pipe($.phpcs.reporter('fail'));
});

/**
 * phpcs:application runs PHP CodeSniffer on application files.
 */
gulp.task('phpcs:application', function() {
    return phpcs(config.phpcs.application.dir, config.phpcs.application.standard)
        .pipe($.phpcs.reporter('fail'));
});

/**
 * phpcs:views runs PHP CodeSniffer on view files.
 */
gulp.task('phpcs:views', function() {
    return phpcs(config.phpcs.views.dir, config.phpcs.views.standard)
        .pipe($.phpcs.reporter('fail'));
});

/**
 * phpcs:others runs PHP CodeSniffer on other php files.
 */
gulp.task('phpcs:others', function() {
    return phpcs(config.phpcs.others.dir, config.phpcs.others.standard)
        .pipe($.phpcs.reporter('fail'));
});

/**
 * phpcbf runs PHP CodeSniffer Fixer on all php files.
 * The configuration for this task is in gulp/config.json.
 */
gulp.task('phpcbf', function() {
    return es.merge.apply(null, [
        phpcbf(config.phpcs.application.dir, config.phpcs.application.standard),
        phpcbf(config.phpcs.views.dir, config.phpcs.views.standard),
        phpcbf(config.phpcs.others.dir, config.phpcs.others.standard)
    ]).pipe($.phpcs.reporter('fail'));
});

/**
 * phpcbf:application runs PHP CodeSniffer Fixer on application files.
 */
gulp.task('phpcbf:application', function() {
    return phpcbf(config.phpcs.application.dir, config.phpcs.application.standard)
        .pipe($.phpcs.reporter('fail'));
});

/**
 * phpcbf:views runs PHP CodeSniffer Fixer on view files.
 */
gulp.task('phpcbf:views', function() {
    return phpcbf(config.phpcs.views.dir, config.phpcs.views.standard)
        .pipe($.phpcs.reporter('fail'));
});

/**
 * phpcbf:others runs PHP CodeSniffer Fixer on other php files.
 */
gulp.task('phpcbf:others', function() {
    return phpcbf(config.phpcs.others.dir, config.phpcs.others.standard)
        .pipe($.phpcs.reporter('fail'));
});

/**
 * phpcsdiffwithcached runs PHP CodeSniffer on staged php files.
 * The configuration for this task is in gulp/config.json.
 * This is used by the pre-commit hook.
 * @internal
 */
gulp.task('phpcsdiffwithcached', function() {
    return phpcsdiff(true);
});

/**
 * phpcsdiff runs PHP CodeSniffer on modified and staged php files.
 * The configuration for this task is in gulp/config.json.
 */
gulp.task('phpcsdiff', function() {
    return phpcsdiff(false);
});

/**
 * findport finds a free port for LiveReload.
 * @internal
 */
gulp.task('findport', function(cb) {
    var lrPortFilepath = '.lrport';
    var chalk = require('chalk');
    var portfinder = require('portfinder');
    var basePort = null;

    try {
        fs.lstatSync(lrPortFilepath);
        basePort = parseInt(fs.readFileSync(lrPortFilepath), 10);
    } catch (e) {} // eslint-disable-line no-empty

    if (basePort === null) {
        basePort = 35729 + Math.floor((Math.random() * 10000) + 1);
    }

    portfinder.basePort = basePort;

    portfinder.getPort(function(err, port) {
        if (err === null) {
            config.port = port;
            console.log(chalk.gray('----------------------------------------'));
            console.log('Found port: ' + chalk.green(port));
            console.log('Command: ');
            console.log(chalk.yellow('ssh -L 35729:localhost:' + port + ' ' + require('os').hostname()));
            console.log(chalk.gray('----------------------------------------'));
            fs.writeFileSync(lrPortFilepath, port);
        } else {
            console.log(chalk.red('Failed to find port.'));
        }

        cb();
    });
});

/**
 * testdb creates the test database.
 * @internal
 */
gulp.task('testdb', $.shell.task([
    './db > /dev/null || :',
    'test -d ../migrations && ./yii migrate --interactive=0'
], {
    cwd: 'tests'
}));

/**
 * codeceptbuild runs codecept build.
 * @internal
 */
gulp.task('codeceptbuild', $.shell.task('../vendor/bin/codecept build', {
    cwd: 'tests'
}));

/**
 * codeception runs codeception tests.
 */
gulp.task('codeception', ['testdb', 'codeceptbuild'], $.shell.task('../vendor/bin/codecept run --html', {
    cwd: 'tests'
}));

// Mocha
// npm install --save gulp-mocha-phantomjs
gulp.task('mocha', function() {
    var mochaConfig = require('./tests/config.json');
    var stream = $.mochaPhantomjs();

    stream.write({
        path: mochaConfig.host + mochaConfig.mochaPath
    });
    stream.end();

    return stream;
});

/**
 * test runs all tests.
 */
gulp.task('test', [
    'codeception',
    // 'mocha',
    // 'protractor'
]);

/**
 * ci is the task executed by the CI server.
 * @internal
 */
gulp.task('ci', ['jslint', 'phpcs', 'test']);

/**
 * commit is the task executed by the pre-commit hook.
 * @internal
 */
gulp.task('commit', ['phplint', 'jslintdiffwithcached', 'phpcsdiffwithcached']);

// Protractor
// gulp.task('protractor', ['testdb'], function() {
//     return gulp.src('./tests/protractor/tests/*.js')
//         .pipe($.protractor.protractor({
//             configFile: 'tests/protractor/config.js',
//         }))
//         .on('error', function(e) {
//             throw e;
//         });
// });

/**
 * default task. Shows a menu of available tasks.
 */
gulp.task('default', function() {
    var promise = function(resolve) {
        var prompt = inquirer.prompt([{
            type: 'list',
            name: 'task',
            message: 'Select task from list',
            choices: [
                'watch',
                'phpcs',
                'jslint',
                'compile',
                new inquirer.Separator(),
                'other'
            ]
        }, {
            type: 'input',
            name: 'task',
            message: 'Type custom task name',
            when: function(res) {
                return res.task === 'other';
            }
        }]);

        prompt.then(function(res) {
            gulp.start(res.task);
            resolve();
        });
    };

    return new Promise(promise);
});

/**
 * watch watches sass and scss files, runs a LiveReload server on a random port,
 * and updates and reloads css files when their source changes.
 */
gulp.task('watch', ['styles:findport'], function() {
    // Start live reload server immediately, don't wait for change
    $.livereload.listen(config.port);

    // Sane watch options
    var saneWatchOptions = {
        debounce: 300
    };

    // Check polling arguments from cli
    if (argv.polling || argv.poll || argv.p) {
        saneWatchOptions.saneOptions = {
            poll: true
        };
    }

    // Watch .js files (causes page reload)
    // $.saneWatch(gp.getScriptsSourcePathWithGlob(), saneWatchOptions, function(filename, filepath) {
    //     $.livereload.changed(path.join(filepath, filename), config.port);
    // });

    $.saneWatch('assets/main/src/svg/*.svg', saneWatchOptions, function(filename, filepath) {
        gulp.start('svg-sprite');
    });

    // Watch .sass files
    $.saneWatch(gp.getStylesSourcePathWithGlob(), saneWatchOptions, function(filename, filepath) {
        styles(false, path.join(filepath, filename));
    });

    $.saneWatch(getAppStyles(), {
        debounce: 300,
        saneOptions: {
            poll: true
        }
    }, function(filename, filepath) {
        lintStyles(filepath);
    });

    // Watch images
    $.saneWatch(gp.getImagesSourcePathWithGlob(), saneWatchOptions, function(filename, filepath) {
        $.livereload.changed(path.join(filepath, filename), config.port);
    });
});
