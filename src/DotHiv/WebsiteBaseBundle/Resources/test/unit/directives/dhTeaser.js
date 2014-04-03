'use strict';

describe('the dhTeaser directive', function() {

    var rootElement, scope, extraTextElement;

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
        var ps = rootElement.find('p');  // find() - Limited to lookups by tag name
        for(var i = 0; i < ps.length; i++) {
            var p = angular.element(ps[i]);
            if (p.attr('ng-show') == 'show') {
                extraTextElement = p;
            }
        }
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

    // TODO not yet implemented
    it('should have a fallback if there is no translation for the label key', function() {});

    it('should hide the extra text by default', function() {
        expect(extraTextElement.css('display')).toBe('none');
    });

    it('should show the more link by default', function() {
        expect(rootElement.find('a').css('display')).toBe('');
    });

    it('should show the extra text when show is set to true', function() {
        scope.$$childHead.show = true;
        scope.$digest();
        expect(extraTextElement.css('display')).toBe('');
    });

    it('should hide the more button when show is set to true', function() {
        scope.$$childHead.show = true;
        scope.$digest();
        expect(rootElement.find('a').css('display')).toBe('none');
    });
});
