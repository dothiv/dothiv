basePath = '../';

files = [
  JASMINE,
  JASMINE_ADAPTER,
  'app/js/vendor/angular/angular.js',
  'app/js/vendor/angular/angular-*.js',
  'test/lib/angular/angular-mocks.js',
  'app/js/**/*.js',
  'test/unit/**/*.js'
];

autoWatch = true;

browsers = ['Chrome'];

junitReporter = {
  outputFile: 'test_out/unit.xml',
  suite: 'unit'
};
