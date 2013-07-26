'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainEditorsController',
    function($scope, $state, $stateParams, dothivDomainResource, dothivUserResource, security) {
        var domainId = $stateParams.domainId;
        $scope.domain = dothivDomainResource.get({"id": domainId});

        // get personal list of domains from server
        $scope.domains = dothivUserResource.getDomains(
            {"username": security.state.user.username}
        );

        $scope.editBasic = function() {
            $state.transitionTo('=.profile.domain.editbasic', { domainId: $scope.domain.id });
        };
        $scope.editPremium = function() {
            console.log("not yet implemented");
        };
        $scope.copySettings = function() {
            console.log("not yet implemented");
        };
    }
);
