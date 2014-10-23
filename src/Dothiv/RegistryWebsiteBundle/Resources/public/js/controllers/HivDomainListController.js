'use strict';

angular.module('dotHIVApp.controllers').controller('HivDomainListController', ['$scope', '$http', 'config',
    function ($scope, $http, config) {
        var domains = [];
        var offset = 0;

        $scope.domain = false;

        function next() {
            offset = domains.length > 0 ? (offset + 1) % domains.length : 0;
            setDomain();
        }

        function prev() {
            offset = domains.length > 0 ? (offset - 1) % domains.length : 0;
            setDomain();
        }

        function setDomain() {
            $scope.domain = domains[offset < 0 ? offset + domains.length : offset];
        }

        var shuffle = function (o) {
            for (var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
            return o;
        };

        function success(data) {
            for (var k in data) {
                domains.push({
                    name: data[k].name + '.hiv',
                    link: 'http://' + data[k].name + '.hiv/'
                });
            }
            domains = shuffle(domains);
            next();
        }

        $scope.next = next;
        $scope.prev = prev;

        $http({method: 'GET', url: '/' + config.locale + '/content/hivDomain'}).success(success);
    }]);
