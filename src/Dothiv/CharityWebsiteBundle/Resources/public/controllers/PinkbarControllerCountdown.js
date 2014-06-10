'use strict';

angular.module('dotHIVApp.controllers').controller('PinkbarControllerCountdown', ['$scope', '$rootScope', 'config',
    function ($scope, $rootScope, config) {

        var startCountDown = new Date(config.countdown_start);
        var endCountDown = new Date(config.general_availability);

        $scope.percent = 0;
        $scope.countdown = {
            diffDays: 0,
            diffTime: 0
        }

        function leadingZero(v) {
            if (("" + v).length > 1) {
                return v;
            }
            return "0" + v;
        }

        function updateCountdown() {
            var now;
            if (typeof config.now != 'undefined') {
                now = new Date(config.now);
            } else {
                now = new Date();
            }

            var start = (now - startCountDown);
            var end = (endCountDown - now);
            var percent = start / end;
            var diffDays = Math.max(0, Math.floor((endCountDown.getTime() - now.getTime()) / 1000 / 60 / 60 / 24));

            var endSeconds = endCountDown.getSeconds() + endCountDown.getMinutes() * 60 + endCountDown.getHours() * 60 * 60;
            var nowSeconds = now.getSeconds() + now.getMinutes() * 60 + now.getHours() * 60 * 60;
            var totalDiffSeconds = endSeconds - nowSeconds;
            if (totalDiffSeconds < 0) {
                totalDiffSeconds = 86400 + totalDiffSeconds;
            }

            var diffHours = Math.floor(totalDiffSeconds / 3600);
            var diffMinutes = Math.floor((totalDiffSeconds - (diffHours * 3600)) / 60);
            var diffSeconds = totalDiffSeconds - diffHours * 3600 - diffMinutes * 60;
            var diffTime = '' + diffHours + ':' + leadingZero(diffMinutes) + ':' + leadingZero(diffSeconds);

            $scope.percent = Math.min(Math.max(percent, 0.01), 1);
            $scope.countdown = {
                diffDays: diffDays,
                diffTime: diffTime
            }
            $scope.$apply();
        }

        updateCountdown();
        var updateInterval;
        window.setTimeout(function () {
            updateInterval = window.setInterval(updateCountdown, 1000);
        }, 1000);

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
