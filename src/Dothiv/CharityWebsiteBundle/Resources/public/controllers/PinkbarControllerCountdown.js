'use strict';

angular.module('dotHIVApp.controllers').controller('PinkbarControllerCountdown', ['$scope', '$rootScope', 'config',
    function ($scope, $rootScope, config) {

        var startCountDown = new Date(config.countdown_start);
        var endCountDown = new Date(config.general_availability);

        function leadingZero(v) {
            if (("" + v).length > 1) {
                return v;
            }
            return "0" + v;
        }

        function updateCountdown() {
            var now = new Date();
            var start = (now - startCountDown);
            var end = (endCountDown - now);
            var percent = start / end;
            var diffDays = Math.floor((endCountDown.getTime() - startCountDown.getTime()) / 1000 / 60 / 60 / 24);

            var diffTime = 24 - endCountDown.getHours() - now.getHours();
            diffTime += ':' + leadingZero(60 - endCountDown.getMinutes() - now.getMinutes());
            diffTime += ':' + leadingZero(60 - endCountDown.getSeconds() - now.getSeconds());

            $scope.percent = Math.min(Math.max(percent, 0.01), 1);
            $scope.countdown = {
                diffDays: diffDays,
                diffTime: diffTime
            }
            $scope.$apply();
        }

        window.setTimeout(updateCountdown, 1);
        var updateInterval = window.setInterval(updateCountdown, 25);

        $scope.$on('$destroy', function () {
            window.clearInterval(updateInterval);
        });

        $scope.expanded = false;

        $scope.toggle = function () {
            $scope.expanded = !$scope.expanded;
        }
        $rootScope.$on('pinkbar.toggle', $scope.toggle);
    }
]);
