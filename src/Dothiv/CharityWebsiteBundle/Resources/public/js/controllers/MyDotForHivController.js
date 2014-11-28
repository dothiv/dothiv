'use strict';

angular.module('dotHIVApp.controllers').controller('MyDotForHivController', ['$scope',
    function ($scope) {
        $scope.flipped = false;
        $scope.hidden = document.referrer.match('mydotfor.hiv') === null && document.location.hash !== '#mydotforhiv';

        function ellipse(context, cx, cy, rx, ry, strokeStyle, fillStyle) {
            context.save(); // save state

            context.beginPath();
            context.translate(cx - rx, cy - ry);
            context.scale(rx, ry);
            context.arc(1, 1, 1, 0, 2 * Math.PI, false);
            context.restore(); // restore to original state
            context.strokeStyle = strokeStyle;
            context.fillStyle = fillStyle;
            context.lineWidth = 6;
            context.stroke();
            context.fill();
        }

        $(function () {
            var dot = $("#mydotforhiv .dot").each(function (index, dot) {
                ellipse(dot.getContext('2d'), 150, 151, 143, 143, 'rgba(0,0,0,0.1)', 'rgba(0,0,0,0.1)');
                ellipse(dot.getContext('2d'), 150, 152, 143, 143, 'rgba(0,0,0,0.1)', 'rgba(0,0,0,0.1)');
                ellipse(dot.getContext('2d'), 150, 153, 143, 143, 'rgba(0,0,0,0.1)', 'rgba(0,0,0,0.1)');
                ellipse(dot.getContext('2d'), 150, 154, 143, 143, 'rgba(0,0,0,0.1)', 'rgba(0,0,0,0.1)');
                ellipse(dot.getContext('2d'), 150, 150, 143, 143, 'white', '#e00073');
            });
        });

        $scope.flip = function () {
            $scope.flipped = !$scope.flipped;
        }
    }
]);
