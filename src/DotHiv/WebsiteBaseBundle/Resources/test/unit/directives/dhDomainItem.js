'use strict';

describe('the dhDomainItem directive', function() {

    var element, scope, state;

    beforeEach(module(function($provide) {
        $provide.factory('$state', function() {
            return {
                transitionTo: angular.noop
            };
        });
    }))

    beforeEach(module('dotHIVApp.directives'));

    beforeEach(inject(function($rootScope, $compile, $state) {
        element = angular.element(
            '<dh-domain-item domain="d"></dh-domain-item>'
        );

        scope = $rootScope;
        scope.d = 
            {
                id: 1337,
                name: "test.hiv"
            };
        $compile(element)(scope);
        scope.$digest();

        state = $state;
        expect(state.transitionTo).toBeDefined();
        spyOn(state, 'transitionTo');
    }));

    it('should show the domain name', function() {
        expect(element.html()).toMatch(/test\.hiv/);
    });

    it('should transition to the domain edit page when the edit link was clicked', function() {
        expect(element.attr('ng-click', 'edit(d.id)'));
        scope.$$childHead.edit();
        expect(state.transitionTo).toHaveBeenCalledWith('=.profile.domainedit', { domainId: 1337 });
    })

});
