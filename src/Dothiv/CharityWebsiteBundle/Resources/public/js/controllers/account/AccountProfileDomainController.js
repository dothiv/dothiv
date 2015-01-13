'use strict';

angular.module('dotHIVApp.controllers').controller('AccountProfileDomainController', ['$scope', '$rootScope', '$state', '$timeout', 'dothivUserResource', 'User', 'idn',
    function ($scope, $rootScope, $state, $timeout, dothivUserResource, User, idn) {

        // get personal list of domains from server
        $scope.domains = null;

        function _refreshDomains() {
            $scope.domains = dothivUserResource.getDomains({handle: User.getHandle()});
        }
        _refreshDomains();
        $rootScope.$on('domain.claimed', function() {
            $timeout(_refreshDomains, 1000);
        });

        // set initial page length to 5 entries
        $scope.itemsPerPage = 5;
        $scope.pageLength = $scope.itemsPerPage;

        // switch to editor choice page for this domain
        $scope.edit = function (domain) {
            $state.transitionTo('profile.editors', { name: domain.name });
        };

        $scope.idnToUnicode = function(name) {
            return idn.toUnicode(name);
        };
    }
]);
