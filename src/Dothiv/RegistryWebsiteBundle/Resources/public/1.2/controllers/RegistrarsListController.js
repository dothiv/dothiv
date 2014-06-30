'use strict';

angular.module('dotHIVApp.controllers').controller('RegistrarsListController', ['$scope', '$http', 'config', function ($scope, $http, config) {
    $scope.registrars = [];

    var shuffle = function (o) {
        for (var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
        return o;
    };

    function success(data) {
        $scope.registrars = shuffle(data);
    }

    $http({method: 'GET', url: '/' + config.locale + '/content/Registrar'}).success(success);
}]);
