module.exports = function (config) {
    config.set({
        basePath: '../../../../../',
        frameworks: ['jasmine'],
        files: [
            'AngularJsBundle/Resources/public/angular/angular.js',
            'BaseWebsiteBundle/Resources/public/vendor/github.com/bestiejs/punycode.js/punycode.js',
            'AngularJsBundle/Resources/public/angular/angular-resource.js',
            'AngularJsBundle/Resources/public/angular/angular-route.js',
            'AngularJsBundle/Resources/public/angular/angular-cookies.js',
            'AngularJsBundle/Resources/public/extra/angular-ui.github.io/ui-router/angular-ui-router.js',
            'AngularJsBundle/Resources/public/extra/angular-ui.github.io/ui-bootstrap/3/angular-ui-bootstrap-tpls.js',
            'AngularJsBundle/Resources/public/extra/github.com/nervgh/angular-file-upload/angular-file-upload.js',
            'AngularJsBundle/Resources/public/angular/angular-mocks.js',
            'BaseWebsiteBundle/Resources/test/1.2/app.js',
            'BaseWebsiteBundle/Resources/public/1.2/services/*.js',
            'BaseWebsiteBundle/Resources/public/1.2/services/models/*.js',
            'BaseWebsiteBundle/Resources/public/1.2/filters/*.js',
            'BaseWebsiteBundle/Resources/test/1.2/unit/**/*.js'
        ],
        autoWatch: false,
        singleRun: true,
        reporters: ['dots', 'junit'],
        browsers: ['Firefox'],
        junitReporter: {
            outputFile: '../../build/logs/karma_unit_registry.xml',
            suite: 'unit'
        }
    });
};
