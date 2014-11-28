'use strict';

describe('ContentController', function () {
    var ctrl, scope, config, $httpBackend;

    beforeEach(module('dotHIVApp.controllers'));
    beforeEach(inject(function ($controller, $rootScope, $injector) {

        config = {
            locale: 'en'
        };
        scope = $rootScope.$new();
        ctrl = $controller('ContentController', {'$scope': scope, 'config': config});

        $httpBackend = $injector.get('$httpBackend');
        $httpBackend.when('GET', '/en/content/SomeType').respond([
            {name: "abc"},
            {name: "xyz"}
        ]);
        $httpBackend.when('GET', '/en/content/SomeType?markdown=someField,someOtherField').respond([
            {name: "abc", someField: "Markdown Value 1", someOtherField: "Markdown Value 2"},
            {name: "xyz", someField: "Markdown Value 3", someOtherField: "Markdown Value 4"}
        ]);
    }));


    afterEach(function () {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });


    it('initially it has not items', inject(function () {
        expect(scope.items.length).toBe(0);
        expect(scope.loaded).toBe(false);
        expect(scope.type).toBeUndefined();
    }));

    it('should fetch items', inject(function () {
        scope.fetch('SomeType');
        expect(scope.type).toBe('SomeType');
        $httpBackend.flush();
        expect(scope.loaded).toBe(true);
        expect(scope.items[0].name).toBe("abc");
        expect(scope.items[1].name).toBe("xyz");
    }));

    it('should fetch items with markdown fields', inject(function () {
        scope.fetch('SomeType', ['someField', 'someOtherField']);
        expect(scope.type).toBe('SomeType');
        $httpBackend.flush();
        expect(scope.loaded).toBe(true);
        expect(scope.items[0].name).toBe("abc");
        expect(scope.items[0].someField.$$unwrapTrustedValue()).toBe("Markdown Value 1");
        expect(scope.items[0].someOtherField.$$unwrapTrustedValue()).toBe("Markdown Value 2");
        expect(scope.items[1].name).toBe("xyz");
        expect(scope.items[1].someField.$$unwrapTrustedValue()).toBe("Markdown Value 3");
        expect(scope.items[1].someOtherField.$$unwrapTrustedValue()).toBe("Markdown Value 4");
    }));

});
