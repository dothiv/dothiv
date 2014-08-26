module.exports = function (config) {
    config.set({
        basePath: '../../',
        frameworks: ['jasmine'],
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
            'public/controllers/HowitWorksController.js',
            // CharityWebsiteBundle: tests
            'test/unit/**/*.js'
        ],
        exclude: [],
        autoWatch: false,
        singleRun: true,
        reporters: ['dots', 'junit'],
        browsers: ['Firefox'],
        junitReporter: {
            outputFile: 'test_out/unit.xml',
            suite: 'unit'
        }
    });
};
