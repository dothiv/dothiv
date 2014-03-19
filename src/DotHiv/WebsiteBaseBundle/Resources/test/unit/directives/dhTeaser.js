'use strict';

describe('the dhTeaser directive', function() {

    var rootElement, scope;

    beforeEach(module(function($provide) {
        $provide.factory('translateFilter', function() {
            return angular.noop;
        });
    }));

    beforeEach(module('dotHIVApp.directives'));

    beforeEach(inject(function($rootScope, $compile) {
        rootElement = angular.element(
            '<dh-teaser prefix="my.prefix"></dh-teaser>'
        );

        scope = $rootScope;
        $compile(rootElement)(scope);
        scope.$digest();
    }));

    it('should show a span tag', function() {
        expect(rootElement.parent().html()).toMatch(/^<span /);
        expect(rootElement.parent().html()).toMatch(/<\/span>$/);
    });

    it('should set the correct translate keys', function() {
        expect(rootElement.parent().html()).toMatch(/translate=['"]my.prefix.text['"]/);
        expect(rootElement.parent().html()).toMatch(/translate=['"]my.prefix.dropdown['"]/);
        expect(rootElement.parent().html()).toMatch(/translate=['"]my.prefix.expand['"]/);
    });

    // TODO
    it('should have a fallback if there is no translation for the label key', function() {});

    // TODO add unit tests for show / hide behaviour
    it('should hide the extra text by default', function() {});
    it('should show the more link by default', function() {});
    it('should show the extra text after clicking on more', function() {});
    it('should hide the more button after clicking on it', function() {});
});
