'use strict';

describe('HeaderController', function () {
    var ctrl, scope;

    beforeEach(module('dotHIVApp.controllers'));
    beforeEach(inject(function ($controller, $rootScope, $injector) {
        scope = $rootScope.$new();
        ctrl = $controller('HeaderController', {'$scope': scope});
    }));

    afterEach(function () {
    });

    it('initially it does not show the menu', inject(function () {
        expect(scope.expanded).toBe(false);
    }));

    it('should toggle the menu', inject(function () {
        expect(scope.expanded).toBe(false);
        scope.toggle();
        expect(scope.expanded).toBe(true);
        scope.toggle();
        expect(scope.expanded).toBe(false);
    }));

    it('should close the menu', inject(function () {
        expect(scope.expanded).toBe(false);
        scope.close();
        expect(scope.expanded).toBe(false);
        scope.toggle();
        expect(scope.expanded).toBe(true);
        scope.close();
        expect(scope.expanded).toBe(false);
        scope.close();
        expect(scope.expanded).toBe(false);
    }));

});
