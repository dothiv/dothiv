'use strict';

angular.module('dotHIVApp.controllers').controller('HowitWorksController', ['$scope', '$rootScope',
    function ($scope, $rootScope) {

        $scope.isClosed = false;

        $scope.toggleHeader = function () {
            $rootScope.$emit('pinkbar.toggle');
        }
    }
]);
