basePath = '../../';

files = [
  JASMINE,
  JASMINE_ADAPTER,
  'public/vendor/angular/angular.js',
  'public/vendor/angular/angular-*.js',
  'test/lib/angular/angular-mocks.js',
  'public/vendor/angular-translate.js',
  'public/**/*.js',
  'test/unit/**/*.js'
];

autoWatch = true;

browsers = ['Chrome','Firefox'];

junitReporter = {
  outputFile: 'test_out/unit.xml',
  suite: 'unit'
};
