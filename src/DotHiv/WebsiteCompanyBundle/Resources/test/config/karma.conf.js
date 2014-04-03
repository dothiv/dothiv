module.exports = function (config) {
    config.set({

        // base path, that will be used to resolve files and exclude
        basePath: '../../',

        // frameworks to use
        frameworks: ['jasmine'],


        // list of files / patterns to load in the browser
        files: [
            // Angular and other vendor stuff
            '../../WebsiteBaseBundle/Resources/public/vendor/angular/angular.js',
            '../../WebsiteBaseBundle/Resources/public/vendor/angular/angular-*.js',
            '../../WebsiteBaseBundle/Resources/test/lib/angular/angular-mocks.js',
            '../../WebsiteBaseBundle/Resources/public/vendor/angular-translate.js',
            // WebsiteCompanyBundle
            'public/app.js',
            '../../WebsiteBaseBundle/Resources/public/**/*.js',
            'public/**/*.js',
            '../../WebsiteBaseBundle/Resources/test/unit/**/*.js',
            'test/unit/**/*.js'
        ],

        // list of files to exclude
        exclude: [

        ],

        autoWatch: true,

        // test results reporter to use
        // possible values: 'dots', 'progress', 'junit', 'growl', 'coverage'
        reporters: ['dots', 'junit'],


        // Start these browsers, currently available:
        // - Chrome
        // - ChromeCanary
        // - Firefox
        // - Opera
        // - Safari (only Mac)
        // - PhantomJS
        // - IE (only Windows)
        browsers: ['Chrome','Firefox'],

        junitReporter: {
            outputFile: 'test_out/unit.xml',
            suite: 'unit'
        }
    });
};
