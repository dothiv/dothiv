'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileSummaryController', ['$scope', 'security', 'dothivUserResource',
    function($scope, security, dothivUserResource) {
        // get personal list of domains from server
        $scope.domains = dothivUserResource.getDomains(
            {"username": security.state.user.username}
        );
    }
]);
