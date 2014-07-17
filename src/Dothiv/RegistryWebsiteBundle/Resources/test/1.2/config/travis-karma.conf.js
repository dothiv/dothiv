module.exports = function (config) {
    config.set({
        basePath: '../../../../../',
        frameworks: ['jasmine'],
        files: [
            'AngularJsBundle/Resources/angular/angular.js',
            'BaseWebsiteBundle/Resources/public/vendor/github.com/bestiejs/punycode.js/punycode.js',
            'AngularJsBundle/Resources/angular/angular-resource.js',
            'AngularJsBundle/Resources/angular/angular-route.js',
            'AngularJsBundle/Resources/angular/angular-cookies.js',
            'AngularJsBundle/Resources/extra/angular-ui.github.io/ui-router/angular-ui-router.js',
            'AngularJsBundle/Resources/extra/angular-ui.github.io/ui-bootstrap/3/angular-ui-bootstrap-tpls.js',
            'AngularJsBundle/Resources/extra/github.com/nervgh/angular-file-upload/angular-file-upload.js',
            'AngularJsBundle/Resources/angular/angular-mocks.js',
            'RegistryWebsiteBundle/Resources/public/1.2/nonProfitRegisterApp.js',
            'BaseWebsiteBundle/Resources/public/1.2/services/*.js',
            'BaseWebsiteBundle/Resources/public/1.2/services/models/*.js',
            'RegistryWebsiteBundle/Resources/public/1.2/services/*.js',
            'RegistryWebsiteBundle/Resources/public/1.2/controllers/NonProfitRegister/*.js',
            'RegistryWebsiteBundle/Resources/test/1.2/unit/**/*.js'
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
