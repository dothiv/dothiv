module.exports = function (config) {
    config.set({
        basePath: '../../../../../',
        frameworks: ['jasmine'],
        files: [
            // Angular and other vendor stuff
            'AngularJsBundle/Resources/angular/angular.js',
            'AngularJsBundle/Resources/angular/angular-resource.js',
            'AngularJsBundle/Resources/angular/angular-route.js',
            'AngularJsBundle/Resources/angular/angular-cookies.js',
            'AngularJsBundle/Resources/extra/angular-ui.github.io/ui-router/angular-ui-router.js',
            'AngularJsBundle/Resources/angular/angular-mocks.js',
            // CharityWebsiteBundle
            'CharityWebsiteBundle/Resources/public/1.2/app.js',
            'CharityWebsiteBundle/Resources/public/1.2/filters/*.js',
            'CharityWebsiteBundle/Resources/public/1.2/controllers/*.js',
            'CharityWebsiteBundle/Resources/public/1.2/services/*.js',
            'CharityWebsiteBundle/Resources/public/1.2/services/models/*.js',
            'CharityWebsiteBundle/Resources/test/1.2/unit/**/*.js',
            'CharityWebsiteBundle/Resources/test/1.2/controllers/*.js'
        ],
        autoWatch: true,
        reporters: ['dots'],
        browsers: ['Chrome', 'Firefox']
    });
};
