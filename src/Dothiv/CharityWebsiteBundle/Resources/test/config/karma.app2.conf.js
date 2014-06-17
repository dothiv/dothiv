module.exports = function (config) {
    config.set({

        // base path, that will be used to resolve files and exclude
        basePath: '../../',

        // frameworks to use
        frameworks: ['jasmine'],


        // list of files / patterns to load in the browser
        files: [
            // Angular and other vendor stuff
            '../../BaseWebsiteBundle/Resources/public/vendor/angular/angular.js',
            '../../BaseWebsiteBundle/Resources/public/vendor/angular/angular-*.js',
            '../../BaseWebsiteBundle/Resources/test/lib/angular/angular-mocks.js',
            '../../BaseWebsiteBundle/Resources/public/vendor/angular-translate.js',
            // CharityWebsiteBundle: init
            'public/app2.js',
            // BaseWebsiteBundle
            '../../BaseWebsiteBundle/Resources/public/filters/*.js',
            // CharityWebsiteBundle: controllers
            'public/controllers/QuoteController.js',
            'public/controllers/HeaderController.js',
            'public/controllers/BlockController.js',
            'public/controllers/PinkbarControllerClicks.js',
            'public/controllers/PinkbarControllerCountdown.js',
            'public/controllers/HowitWorksController.js',
            // CharityWebsiteBundle: tests
            'test/unit/**/*.js'
        ],

        // list of files to exclude
        exclude: [
            '../../BaseWebsiteBundle/Resources/public/vendor/angular/angular-loader.js',
            '../../BaseWebsiteBundle/Resources/public/vendor/angular/*.min.js',
            '../../BaseWebsiteBundle/Resources/public/vendor/angular/angular-scenario.js'
        ],

        autoWatch: true,

        // test results reporter to use
        // possible values: 'dots', 'progress', 'junit', 'growl', 'coverage'
        reporters: ['dots'],


        // Start these browsers, currently available:
        // - Chrome
        // - ChromeCanary
        // - Firefox
        // - Opera
        // - Safari (only Mac)
        // - PhantomJS
        // - IE (only Windows)
        browsers: ['Firefox']
    });
};
