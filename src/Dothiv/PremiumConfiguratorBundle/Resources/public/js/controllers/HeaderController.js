'use strict';

angular.module('dotHIVApp.controllers').controller('HeaderController', ['$scope', '$rootScope',
    function ($scope, $rootScope) {
        $scope.loading = false;

        $rootScope.$on('http.on', function () {
            $scope.loading = true;
        });

        $rootScope.$on('http.off', function () {
            $scope.loading = false;
        });
    }
]);
