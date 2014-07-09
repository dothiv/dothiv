'use strict';

angular.module('dotHIVApp.controllers').controller('AccountProfileDomainController', ['$scope', '$state', 'dothivUserResource', 'User',
    function ($scope, $state, dothivUserResource, User) {

        // get personal list of domains from server
        $scope.domains = dothivUserResource.getDomains({handle: User.getHandle()});

        // set initial page length to 5 entries
        $scope.pageLength = 5;

        // switch to editor choice page for this domain
        $scope.edit = function (domain) {
            $state.transitionTo('profile.editors', { name: domain.name });
        };
    }
]);
