module.exports = function (config) {
    config.set({
        basePath: '../../../../../',
        frameworks: ['jasmine'],
        files: [
            // Angular and other vendor stuff
            'AngularJsBundle/Resources/angular/angular.js',
            'AngularJsBundle/Resources/angular/angular-mocks.js',
            // CharityWebsiteBundle
            'CharityWebsiteBundle/Resources/public/1.2/app.js',
            'CharityWebsiteBundle/Resources/public/1.2/filters/*.js',
            'CharityWebsiteBundle/Resources/public/1.2/controllers/*.js',
            'CharityWebsiteBundle/Resources/test/1.2/unit/**/*.js',
            'CharityWebsiteBundle/Resources/test/1.2/controllers/*.js'
        ],
        autoWatch: true,
        reporters: ['dots'],
        browsers: ['Chrome', 'Firefox']
    });
};
