'use strict';

describe('the dhCommentItem directive', function() {

    var element, scope;

    beforeEach(module('dotHIVApp.directives'));

    beforeEach(inject(function($rootScope, $compile) {
        element = angular.element(
            '<dh-comment-item comment="c"></dh-comment-item>'
        );

        scope = $rootScope;
        scope.c =
            {
                project: "My Project",
                text: "This is a very stupid comment. It hasn't got any content."
            };
        $compile(element)(scope);
        scope.$digest();
    }));

    it('should show the comment project name', function() {
        expect(element.html()).toMatch(/My Project/);
    });

    it('should show the comment text', function() {
        expect(element.html()).toMatch(/This is a very stupid comment\. It hasn't got any content\./);
    });

});
