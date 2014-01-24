// Karma configuration
// Generated on Fri Jan 24 2014 15:02:14 GMT+0100 (CET)

module.exports = function (config) {
    config.set({

        // base path, that will be used to resolve files and exclude
        basePath: '../..',

        // frameworks to use
        frameworks: ['jasmine'],


        // list of files / patterns to load in the browser
        files: [
            'public/vendor/angular/angular.js',
            'public/vendor/angular/angular-*.js',
            'test/lib/angular/angular-mocks.js',
            'public/vendor/angular-translate.js',
            'public/vendor/**/*.js',
            'test/test.js',
            'public/**/*.js',
            'test/unit/**/*.js'
        ],


        // list of files to exclude
        exclude: [

        ],


        // test results reporter to use
        // possible values: 'dots', 'progress', 'junit', 'growl', 'coverage'
        reporters: ['dots', 'junit'],


        // web server port
        port: 9876,


        // enable / disable colors in the output (reporters and logs)
        colors: true,


        // level of logging
        // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
        logLevel: config.LOG_INFO,


        // enable / disable watching file and executing tests whenever any file changes
        autoWatch: false,


        // Start these browsers, currently available:
        // - Chrome
        // - ChromeCanary
        // - Firefox
        // - Opera
        // - Safari (only Mac)
        // - PhantomJS
        // - IE (only Windows)
        browsers: ['PhantomJS'],


        // If browser does not capture in given timeout [ms], kill it
        captureTimeout: 1000,


        // Continuous Integration mode
        // if true, it capture browsers, run tests and exit
        singleRun: true,
        junitReporter: {
            outputFile: 'test-results.xml'
        }
    });
};
