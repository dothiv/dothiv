'use strict';

angular.module('dotHIVApp.controllers').controller('PartnerController', ['$scope', '$http', 'config',
    function ($scope, $http, config) {

        $scope.visible = false;

        $scope.partners = [];

        var shuffle = function (o) {
            for (var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
            return o;
        };

        function success(data) {
            $scope.partners = shuffle(data);
            $scope.visible = true;
        }

        $http({method: 'GET', url: '/' + config.locale + '/content/Partner?markdown=description'}).success(success);

    }
]);
