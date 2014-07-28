'use strict';

angular.module('dotHIVApp.controllers').controller('HeaderController', ['$scope', '$rootScope', 'config',
    function ($scope, $rootScope, config) {
        $scope.loading = false;
        $scope.domain = config.domain;

        $rootScope.$on('http.on', function () {
            $scope.loading = true;
        });

        $rootScope.$on('http.off', function () {
            $scope.loading = false;
        });
    }
]);
