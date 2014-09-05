module.exports = function (config) {
    config.set({
        basePath: '../../../../../',
        frameworks: ['jasmine'],
        files: [
            // Angular and other vendor stuff
            'AngularJsBundle/Resources/public/angular/angular.js',
            'AngularJsBundle/Resources/public/angular/angular-resource.js',
            'AngularJsBundle/Resources/public/angular/angular-route.js',
            'AngularJsBundle/Resources/public/angular/angular-cookies.js',
            'AngularJsBundle/Resources/public/extra/angular-ui.github.io/ui-router/angular-ui-router.js',
            'AngularJsBundle/Resources/public/extra/angular-ui.github.io/ui-bootstrap/2/angular-ui-bootstrap.js',
            'AngularJsBundle/Resources/public/angular/angular-mocks.js',
            // *WebsiteBundle
            'CharityWebsiteBundle/Resources/public/1.2/app.js',
            'CharityWebsiteBundle/Resources/public/1.2/controllers/*.js',
            'BaseWebsiteBundle/Resources/public/1.2/services/*.js',
            'BaseWebsiteBundle/Resources/public/1.2/services/models/*.js',
            'CharityWebsiteBundle/Resources/test/1.2/unit/**/*.js',
            'CharityWebsiteBundle/Resources/test/1.2/controllers/*.js'
        ],
        autoWatch: false,
        singleRun: true,
        reporters: ['dots', 'junit'],
        browsers: ['Firefox'],
        junitReporter: {
            outputFile: '../../build/logs/karma_unit.xml',
            suite: 'unit'
        }
    });
};
