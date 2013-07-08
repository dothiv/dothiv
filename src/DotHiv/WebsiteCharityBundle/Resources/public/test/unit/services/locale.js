'use strict';

describe('locale service', function() {

    var locale, httpBackend, rootScope;

    beforeEach(module('dotHIVApp.services'));
    beforeEach(function () {
        inject(function($httpBackend) {
            httpBackend = $httpBackend;
        });
        inject(function($rootScope) {
            rootScope = $rootScope;
        });
        inject(function($injector) {
            httpBackend.expectGET(/^.*\/api\/locale$/).respond(200, '{"locale":"en_US"}');
            locale = $injector.get('locale');
        });
    });

    describe('set(), locale', function() {

        it('should call the locale api endpoint when settings a locale', function() {
            httpBackend.expectPUT(/^.*\/api\/locale$/, '{"locale":"fr_FR"}').respond(204);
            locale.set('fr_FR');
            rootScope.$digest();
            httpBackend.flush();
        });

        it('should set the locale property correctly', function() {
            locale.set('de_DE');
            expect(locale.locale.locale).toBe('de_DE');
        });

        it('can clear the locale', function() {
            httpBackend.expectPUT(/^.*\/api\/locale$/, '{"locale":""}').respond(204);
            locale.set('');
            rootScope.$digest();
            httpBackend.flush();
        })

    });

});
