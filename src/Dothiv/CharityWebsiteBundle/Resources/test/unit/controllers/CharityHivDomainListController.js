'use strict';

describe('CharityHivDomainListController', function () {
    var ctrl, scope, config, $httpBackend;

    beforeEach(module('dotHIVApp.controllers'));
    beforeEach(inject(function ($controller, $rootScope, $injector) {

        config = {
            locale: 'en'
        };
        scope = $rootScope.$new();
        ctrl = $controller('CharityHivDomainListController', {'$scope': scope, 'config': config});

        $httpBackend = $injector.get('$httpBackend');
        $httpBackend.when('GET', '/en/content/hivDomain').respond([
            {name: "abc", show: true},
            {name: "xyz"},
            {name: "def", show: true}
        ]);
    }));


    afterEach(function () {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });


    it('initially it does not show a domain', inject(function () {
        expect(scope.domain).toBe(false);
        $httpBackend.flush();
    }));

    it('loads domains via API', inject(function () {
        $httpBackend.expectGET('/en/content/hivDomain');
        $httpBackend.flush();
        expect(scope.domain.name).toMatch(/(abc|def)\.hiv/);

    }));

    it('should selecte next domain', inject(function () {
        $httpBackend.expectGET('/en/content/hivDomain');
        $httpBackend.flush();
        var prevDomain = scope.domain.name;
        scope.next();
        expect(scope.domain.name).not.toBe(prevDomain);
        scope.next();
        expect(scope.domain.name).toBe(prevDomain);
    }));

    it('should selecte previous domain', inject(function () {
        $httpBackend.expectGET('/en/content/hivDomain');
        $httpBackend.flush();
        var prevDomain = scope.domain.name;
        scope.prev();
        expect(scope.domain.name).not.toBe(prevDomain);
        scope.prev();
        expect(scope.domain.name).toBe(prevDomain);
    }));

});
