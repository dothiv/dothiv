'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileSummaryController', ['$scope', 'security', 'dothivUserResource',
    function($scope, security, dothivUserResource) {
        // get personal list of domains from server
        $scope.domains = dothivUserResource.getDomains(
            {"username": security.state.user.username}
        );

        // // TODO get personal list of projects from server
        // $scope.projects = [ {name: 'Awesome project', votes: 3268}, {name: 'Lame project', votes: 12}, {name: 'Nils\'s project', votes: 122626} ];
        // $scope.projects.$resolved = true; // workaround for the loader
 
        // // TODO get personal list of votes from server
        // $scope.votes = [ {}, {}, {}, {}, {} ];
        // $scope.votes.$resolved = true; // workaround for the loader
 
        // // TODO get personal list of comments from server
        // $scope.comments = [ {project: 'Some project', text: 'Your project is really awesome!'}, {project: 'Some other project', text: 'This one really sucks.'} ];
        // $scope.comments.$resolved = true; // workaround for the loader
    }
]);
