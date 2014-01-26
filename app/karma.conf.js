module.exports = function (config) {
    config.set({

        // base path, that will be used to resolve files and exclude
        basePath: '../src/DotHiv',

        // frameworks to use
        frameworks: ['jasmine', 'ng-scenario'],


        // list of files / patterns to load in the browser
        files: [
            // WebsiteBaseBundle
            'WebsiteBaseBundle/Resources/public/vendor/angular/angular.js',
            'WebsiteBaseBundle/Resources/public/vendor/angular/angular-*.js',
            'WebsiteBaseBundle/Resources/test/lib/angular/angular-mocks.js',
            'WebsiteBaseBundle/Resources/public/vendor/angular-translate.js',
            'WebsiteBaseBundle/Resources/public/vendor/**/*.js',
            'WebsiteBaseBundle/Resources/test/test.js',
            'WebsiteBaseBundle/Resources/public/**/*.js',
            'WebsiteBaseBundle/Resources/test/unit/**/*.js',
            // WebsiteCharityBundle
            'WebsiteCharityBundle/Resources/public/app.js',
            'WebsiteCharityBundle/Resources/public/**/*.js',
            'WebsiteCharityBundle/Resources/test/unit/**/*.js',
            'WebsiteCharityBundle/Resources/test/e2e/**/*.js',
            // WebsiteCompanyBundle
            'WebsiteCompanyBundle/Resources/public/app.js',
            'WebsiteCompanyBundle/Resources/public/**/*.js',
            'WebsiteCompanyBundle/Resources/test/unit/**/*.js',
            'WebsiteCompanyBundle/Resources/test/e2e/**/*.js'
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
            outputFile: '../../build/logs/karma-results.xml'
        }
    });
};
