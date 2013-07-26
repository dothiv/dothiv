'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileSummaryController', ['$scope', 'security', 'dothivUserResource',
    function($scope, security, dothivUserResource) {
        // get personal list of domains from server
        $scope.domains = dothivUserResource.getDomains(
            {"username": security.state.user.username}
        );

        // TODO get personal list of projects from server
        $scope.projects = [ {name: 'Awesome project', votes: 3268}, {name: 'Lame project', votes: 12}, {name: 'Nils\'s project', votes: 122626} ];
        $scope.projects.$resolved = true; // workaround for the loader
    }
]);
