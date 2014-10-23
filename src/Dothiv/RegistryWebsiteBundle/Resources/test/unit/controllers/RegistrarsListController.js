'use strict';

describe('RegistrarsListController', function () {
    var ctrl, scope, config, $httpBackend;

    beforeEach(module('dotHIVApp.controllers'));
    beforeEach(module('dotHIVApp.services'));
    beforeEach(inject(function ($controller, $rootScope, $injector) {

        config = {
            locale: 'en',
            eur_to_usd: 0.5
        };
        scope = $rootScope.$new();
        ctrl = $controller('RegistrarsListController', {'$scope': scope, 'config': config});

        $httpBackend = $injector.get('$httpBackend');
        $httpBackend.when('GET', '/en/content/Registrar?markdown=promotion:inline').respond([
            {
                name: 'Example US Registrar',
                image: 'http://image.com/example',
                country: 'USA',
                pricePerYearUsd: 200,
                url: 'http://example.com/'
            },
            {
                name: 'Example EU Registrar',
                image: 'http://image.eu/example',
                country: 'Germany',
                pricePerYearEur: 100,
                url: 'http://example.de/'
            }
        ]);
    }));


    afterEach(function () {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });


    it('initially it does not show registrars', inject(function () {
        expect(scope.registrars.length).toBe(0);
        $httpBackend.flush();
    }));

    it('loads registrars via API', inject(function () {
        $httpBackend.expectGET('/en/content/Registrar?markdown=promotion:inline');
        $httpBackend.flush();
        describe('money conversion', function () {
            it('calculates an EUR price for an USD price', function () {
                expect(scope.registrars[0].name).toBe("Example US Registrar");
                expect(scope.registrars[0].priceUSD).toBe(200);
                expect(scope.registrars[0].priceUSDLabel[0]).not.toBe("*");
                expect(scope.registrars[0].priceEUR).toBe(100);
                expect(scope.registrars[0].priceEURLabel[0]).toBe("*");

            });
            it('calculates an USD price for an EUR price', function () {
                expect(scope.registrars[1].name).toBe("Example EU Registrar");
                expect(scope.registrars[1].priceEUR).toBe(100);
                expect(scope.registrars[1].priceEURLabel[0]).not.toBe("*");
                expect(scope.registrars[1].priceUSD).toBe(200);
                expect(scope.registrars[1].priceUSDLabel[0]).toBe("*");
            });
        });
    }));

});
