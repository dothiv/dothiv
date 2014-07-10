'use strict';

angular.module('dotHIVApp.controllers').controller('AccountProfileDomainController', ['$scope', '$rootScope', '$state', 'dothivUserResource', 'User',
    function ($scope, $rootScope, $state, dothivUserResource, User) {

        // get personal list of domains from server
        $scope.domains = null;

        function _refreshDomains() {
            $scope.domains = dothivUserResource.getDomains({handle: User.getHandle()});
        }
        _refreshDomains();
        $rootScope.$on('domain.claimed', _refreshDomains);

        // set initial page length to 5 entries
        $scope.pageLength = 5;

        // switch to editor choice page for this domain
        $scope.edit = function (domain) {
            $state.transitionTo('profile.editors', { name: domain.name });
        };
    }
]);
