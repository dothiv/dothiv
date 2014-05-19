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
            // RegistryWebsiteBundle
            'public/app.js',
            '../../BaseWebsiteBundle/Resources/public/**/*.js',
            'public/**/*.js',
            '../../BaseWebsiteBundle/Resources/test/unit/**/*.js',
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
