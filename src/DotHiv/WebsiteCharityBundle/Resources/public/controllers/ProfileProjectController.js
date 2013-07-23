'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileProjectController', ['$scope',
    function($scope, security, dothivUserResource) {
        // TODO get personal list of projects from server
        $scope.projects = [ {name: 'Awesome project', votes: 3268}, {name: 'Lame project', votes: 12}, {name: 'Nils\'s project', votes: 122626} ];
    }
]);
