'use strict';

/* http://docs.angularjs.org/guide/dev_guide.e2e-testing */

describe('my app', function() {

  beforeEach(function() {
    browser().navigateTo('/');
  });

  describe('view1', function() {

    beforeEach(function() {
        browser().navigateTo('/');
    });

    it('should show a h1 heading saying "DotHIV index page"', function() {
      expect(element('#title').text()).
        toMatch("DotHIV index page");
    });

  });

  describe('view2', function() {

    beforeEach(function() {
      browser().navigateTo('#/view2');
    });

    it('should render view2 when user navigates to /view2', function() {
      expect(element('[ng-view] p:first').text()).
        toMatch(/partial for view 2/);
    });
  });
});
