'use strict';

describe('LookupResultController', function () {
    var ctrl, scope, config, state, stateParams, $httpBackend;

    beforeEach(module('dotHIVApp'));

    beforeEach(module(function ($provide) {
        $provide.value('config', {
            locale: 'en',
            shop: {
                price: {
                    eur: 15600,
                    usd: 18000
                },
                promo: {
                    name4life: {
                        eur: -13000,
                        usd: -16100
                    }
                }
            },
            vat: {
                de: 19
            }
        });
    }));

    beforeEach(inject(function ($controller, $rootScope, $injector) {
        scope = $rootScope.$new();
        stateParams = {
            domain: 'twisty4life.hiv'
        };
        var deps = {
            '$scope': scope,
            '$state': state,
            '$stateParams': stateParams
        };
        ctrl = $controller('LookupResultController', deps);

        $httpBackend = $injector.get('$httpBackend');
        $httpBackend.when('GET', '/api/shop/lookup?q=twisty4life.hiv').respond({
            "name": "twisty4life.hiv",
            "registered": true,
            "premium": false,
            "trademark": false,
            "blocked": false,
            "available": false,
            "@context": "http://jsonld.click4life.hiv/DomainInfo",
            "@id": "https://tld.hiv/api/shop/info/twisty4life.hiv"
        });
    }));

    afterEach(function () {
        $httpBackend.verifyNoOutstandingExpectation();
        $httpBackend.verifyNoOutstandingRequest();
    });

    it('initially it does not have a lookup result', inject(function () {
        expect(scope.lookup).toBe(null);
        $httpBackend.flush();
    }));

    it('looks up domain via API', inject(function () {
        $httpBackend.expectGET('/api/shop/lookup?q=twisty4life.hiv');
        $httpBackend.flush();
        expect(scope.lookup).toMatch("registered");
    }));

    it('suggestions should not contain 4life', inject(function () {
        $httpBackend.expectGET('/api/shop/lookup?q=twisty4life.hiv');
        $httpBackend.flush();
        expect(scope.lookup).toMatch("registered");
        expect(scope.alternatives).toMatch([
            'twistysupports.hiv',
            'twistyfortheendofaids.hiv',
            'twistyforhope.hiv',
        ]);
    }));
});

