'use strict';

describe('the dhProjectItem directive', function() {

    var element, scope;

    beforeEach(module('dotHIVApp.directives'));

    beforeEach(inject(function($rootScope, $compile) {
        element = angular.element(
            '<dh-project-item project="p"></dh-project-item>'
        );

        scope = $rootScope;
        scope.p = 
            {
                id: 1337,
                name: "My best project",
                votes: 1338
            };
        $compile(element)(scope);
        scope.$digest();
    }));

    it('should show the project name', function() {
        expect(element.html()).toMatch(/My best project/);
    });

    it('should show the project vote count', function() {
        expect(element.html()).toMatch(/1338/);
    });

});
