module.exports = function (config) {
    config.set({

        // base path, that will be used to resolve files and exclude
        basePath: '../',

        // frameworks to use
        frameworks: ['jasmine'],


        // list of files / patterns to load in the browser
        files: [
            // Angular and other vendor stuff
            'src/Dothiv/BaseWebsiteBundle/Resources/public/vendor/angular/angular.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/public/vendor/angular/angular-*.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/test/lib/angular/angular-mocks.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/public/vendor/angular-translate.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/public/vendor/**/*.js',
            // BaseWebsiteBundle
            'src/Dothiv/BaseWebsiteBundle/Resources/test/test.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/public/**/*.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/test/unit/**/*.js'
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
        captureTimeout: 10000,


        // Continuous Integration mode
        // if true, it capture browsers, run tests and exit
        singleRun: true,
        junitReporter: {
            outputFile: 'build/logs/karma-results.xml'
        }
    });
};
