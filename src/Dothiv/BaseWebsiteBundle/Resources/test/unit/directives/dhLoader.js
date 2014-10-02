'use strict';

describe('the dhLoader directive', function() {

    var element, scope;

    beforeEach(module(function($provide) {
        $provide.factory('translateFilter', function() {
            return angular.noop;
        });
    }))

    beforeEach(module('dotHIVApp.directives'));

    beforeEach(inject(function($rootScope, $compile) {
        element = angular.element(
            '<dh-loader></dh-loader>'
        );

        scope = $rootScope;
        $compile(element)(scope);
        scope.$digest();
    }));

    it('should show an image tag', function() {
        expect(element.parent().html()).toMatch(/<img /);
    });

});
