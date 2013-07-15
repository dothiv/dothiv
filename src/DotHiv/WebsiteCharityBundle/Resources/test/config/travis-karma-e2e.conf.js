basePath = '../..';

files = [
  ANGULAR_SCENARIO,
  ANGULAR_SCENARIO_ADAPTER,
  'test/e2e/**/*.js'
];

autoWatch = false;

browsers = ['Firefox'];

singleRun = true;

urlRoot = '/karma/';

proxies = {
    '/': 'http://localhost/'
}

junitReporter = {
  outputFile: 'test_out/e2e.xml',
  suite: 'e2e'
};
