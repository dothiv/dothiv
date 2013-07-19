basePath = '../../';

files = [
  JASMINE,
  JASMINE_ADAPTER,
  '../../WebsiteBaseBundle/Resources/public/vendor/angular/angular.js',
  '../../WebsiteBaseBundle/Resources/public/vendor/angular/angular-*.js',
  '../../WebsiteBaseBundle/Resources/test/lib/angular/angular-mocks.js',
  '../../WebsiteBaseBundle/Resources/public/vendor/angular-translate.js',
  'public/app.js',
  '../../WebsiteBaseBundle/Resources/public/**/*.js',
  'public/**/*.js',
  '../../WebsiteBaseBundle/Resources/test/unit/**/*.js',
  'test/unit/**/*.js'
];

autoWatch = false;

browsers = ['Firefox'];

singleRun = true;

junitReporter = {
  outputFile: 'test_out/unit.xml',
  suite: 'unit'
};
