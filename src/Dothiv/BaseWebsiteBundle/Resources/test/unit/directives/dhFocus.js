'use strict';

describe('the dhFocus directive', function() {

    var element, first, second, scope, timeout;

    beforeEach(module('dotHIVApp.directives'));

    beforeEach(inject(function($rootScope, $compile, $timeout) {
        timeout = $timeout;

        element = angular.element(
            '<div>' +
                '<input type="text" name="first"  id="first"  dh-focus="t.first" />' +
                '<input type="text" name="second" id="second" dh-focus="t.second" />' +
            '</div>'
        );

        scope = $rootScope;
        scope.t = {};
        $compile(element)(scope);
        scope.$digest();

        expect(element.find('input').length).toBe(2);
        expect(element.find('input')[0]).toBeDefined();
        expect(element.find('input')[1]).toBeDefined();

        first = element.find('input')[0];
        second = element.find('input')[1];

        spyOn(first, 'focus').andCallThrough();
        spyOn(second, 'focus').andCallThrough();
    }));

    it('should set not the focus initially', function() {
        expect(first.focus).not.toHaveBeenCalled();
        expect(second.focus).not.toHaveBeenCalled();
    });

    describe('should set the focus when requested', function() {

        it('on the first input field', function() {
            scope.t.first = 0;
            scope.$digest();
            timeout.flush();
            expect(first.focus).toHaveBeenCalled();
            expect(second.focus).not.toHaveBeenCalled();
        });

        it('on the second input field', function() {
            scope.t.second = 1;
            scope.$digest();
            timeout.flush();
            expect(first.focus).not.toHaveBeenCalled();
            expect(second.focus).toHaveBeenCalled();
        });

    });

});
