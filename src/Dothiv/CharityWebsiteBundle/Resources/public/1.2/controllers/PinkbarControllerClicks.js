'use strict';

angular.module('dotHIVApp.controllers').controller('PinkbarControllerClicks', ['$scope', '$rootScope', '$http', 'config', '$q',
    function ($scope, $rootScope, $http, config, $q) {

        $scope.bar = null;

        $scope.showfunding = false;

        function _toggle() {
            $scope.showfunding = !$scope.showfunding;
        }

        $rootScope.$on('pinkbar.toggle', _toggle);

        function success(data) {
            $scope.bar = data;
        }

        $http({method: 'GET', url: '/' + config.locale + '/pinkbar'}).success(success);
    }
]);
