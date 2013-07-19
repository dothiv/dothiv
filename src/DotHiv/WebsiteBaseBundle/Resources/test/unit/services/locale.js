'use strict';

describe('locale service', function() {

    var locale, httpBackend, rootScope, translate;

    beforeEach(module('dotHIVApp.services'));
    beforeEach(module(function($provide) {
            $provide.factory('$translate', function() {
                return {
                    uses: angular.noop
                };
            });
        })
    );
    beforeEach(function () {
        inject(function($httpBackend) {
            httpBackend = $httpBackend;
        });
        inject(function($rootScope) {
            rootScope = $rootScope;
            spyOn(rootScope, '$broadcast').andCallThrough();
        });
        inject(function($translate) {
            translate = $translate;
            spyOn(translate, 'uses');
        });
        inject(function($injector) {
            httpBackend.expectGET(/^.*api\/locale$/).respond(200, '{"locale":"en_US"}');
            locale = $injector.get('locale');
        });
    });

    describe('language/territory', function() {

        it('should set the language and territory correctly when using a locale like de_DE (language and territory part)', function() {
            httpBackend.expectPUT(/^.*api\/locale$/, '{"locale":"de_DE"}').respond(204);
            locale.set('de_DE');
            expect(locale.language).toBe('de');
            expect(locale.territory).toBe('DE');
            httpBackend.expectPUT(/^.*api\/locale$/, '{"locale":"en_AU"}').respond(204);
            locale.set('en_AU');
            expect(locale.language).toBe('en');
            expect(locale.territory).toBe('AU');
        });

        it('should set the language and territory correctly when using a locale like de (no territory part)', function() {
            httpBackend.expectPUT(/^.*api\/locale$/, '{"locale":"de"}').respond(204);
            locale.set('de');
            expect(locale.language).toBe('de');
            expect(locale.territory).toBe('');
            httpBackend.expectPUT(/^.*api\/locale$/, '{"locale":"en"}').respond(204);
            locale.set('en');
            expect(locale.language).toBe('en');
            expect(locale.territory).toBe('');
        });

        it('should set language and territory to empty string when the locale is undefined', function() {
            httpBackend.expectPUT(/^.*api\/locale$/, '{"locale":""}').respond(204);
            locale.set(undefined);
            expect(locale.language).toBe('');
            expect(locale.territory).toBe('');
        });

    });

    describe('set(), locale', function() {

        it('should call the locale api endpoint when settings a locale', function() {
            httpBackend.expectPUT(/^.*api\/locale$/, '{"locale":"fr_FR"}').respond(204);
            locale.set('fr_FR');
            rootScope.$digest();
            httpBackend.flush();
        });

        it('should set the _locale property correctly', function() {
            httpBackend.expectPUT(/^.*api\/locale$/, '{"locale":"de_DE"}').respond(204);
            locale.set('de_DE');
            expect(locale._locale.locale).toBe('de_DE');
        });

        it('can clear the locale', function() {
            httpBackend.expectPUT(/^.*api\/locale$/, '{"locale":""}').respond(204);
            locale.set('');
            rootScope.$digest();
            httpBackend.flush();
        });

        it('should call $translate.uses', function() {
            httpBackend.expectPUT(/^.*api\/locale$/, '{"locale":"de_DE"}').respond(204);
            locale.set('de_DE');
            expect(translate.uses).toHaveBeenCalledWith('de');
        });

    });

    describe('initialize', function() {

        it('should broadcast the "localeInitialized" event when received the locale from the server', function() {
            rootScope.$digest();
            httpBackend.flush();
            expect(rootScope.$broadcast).toHaveBeenCalledWith('localeInitialized');
        });

    })

});
