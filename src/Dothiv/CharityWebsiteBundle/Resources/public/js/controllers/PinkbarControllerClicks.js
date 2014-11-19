'use strict';

angular.module('dotHIVApp.controllers').controller('PinkbarControllerClicks', ['$scope', '$http', 'config',
    function ($scope, $http, config) {

        $scope.bar = null;

        /**
         * Animates the pink bar.
         * Makes it at least 5% of the screen wide to make it visible.
         */
        function animate() {
            var windowWidth = $(window).width();
            var minWidth = windowWidth * 0.05;
            var targetWidth = Math.max(minWidth, windowWidth * $scope.bar.percent);
            var bar = $('#pinkbar .pinkbar-progress');
            if (bar) {
                bar.animate({width: targetWidth}, 500);
            }
        }

        $http({method: 'GET', url: '/' + config.locale + '/pinkbar'}).success(function (data) {
            $scope.bar = data;
            animate();
        });
    }
]);
