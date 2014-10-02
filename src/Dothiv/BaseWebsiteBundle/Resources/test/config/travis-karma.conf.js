module.exports = function (config) {
    config.set({
        basePath: '../../',
        frameworks: ['jasmine'],
        files: [
            // Angular and other vendor stuff
            'public/vendor/angular/angular.js',
            'public/vendor/angular/angular-*.js',
            'test/lib/angular/angular-mocks.js',
            'public/vendor/angular-translate.js',
            'public/vendor/**/*.js',
            // BaseWebsiteBundle
            'test/test.js',
            'public/**/*.js',
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
