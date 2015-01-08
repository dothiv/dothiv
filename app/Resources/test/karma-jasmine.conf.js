module.exports = function (config) {
    config.set({
        basePath: '../../../',
        frameworks: ['jasmine'],
        files: [
            'vendor/components/angular.js/angular.js',
            'vendor/components/angular.js/angular-resource.js',
            'vendor/components/angular.js/angular-route.js',
            'vendor/components/angular.js/angular-cookies.js',
            'vendor/components/angular.js/angular-mocks.js',
            'app/Resources/test/app.js',
            // BaseWebsiteBundle
            'vendor/components/bestiejs-punycode/punycode.js',
            'vendor/component/angular-ui-router/release/angular-ui-router.js',
            'vendor/components/bootstrap/ui-bootstrap-tpls.js',
            'vendor/components/nervgh-angular-file-upload/angular-file-upload.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/public/js/services/*.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/public/js/services/models/*.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/public/js/filters/*.js',
            'src/Dothiv/BaseWebsiteBundle/Resources/test/unit/**/*.js',
            // CharityWebsiteBundle
            'src/Dothiv/CharityWebsiteBundle/Resources/public/js/filters/*.js',
            'src/Dothiv/CharityWebsiteBundle/Resources/public/js/controllers/**/*.js',
            'src/Dothiv/CharityWebsiteBundle/Resources/test/unit/**/*.js',
            // RegistryWebsiteBundle
            'src/Dothiv/RegistryWebsiteBundle/Resources/public/js/controllers/**/*.js',
            'src/Dothiv/RegistryWebsiteBundle/Resources/test/unit/**/*.js'
        ],
        exclude: [],
        reporters: ['dots'],
        logLevel: config.LOG_WARN,
        browsers: ['Chrome'],
        autoWatch: false,
        captureTimeout: 3000
    });
};
