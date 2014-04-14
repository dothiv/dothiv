module.exports = function (config) {
    config.set({
        basePath: '../../',
        frameworks: ['jasmine'],
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
