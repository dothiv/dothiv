'use strict';

describe('the dhVoteItem directive', function() {

    var element, scope;

    beforeEach(module('dotHIVApp.directives'));

    beforeEach(inject(function($rootScope, $compile) {
        element = angular.element(
            '<dh-vote-item vote="v"></dh-vote-item>'
        );

        scope = $rootScope;
        scope.v =
            {
            };
        $compile(element)(scope);
        scope.$digest();
    }));

    /** specs for the dh-vote-item directive go here */

});
