'use strict';

angular.module('dotHIVApp.controllers').controller('ArticleController', ['$scope',
    function ($scope) {
        $scope.block = 1;
        $scope.blocks = 0;

        function _hasNext() {
            return $scope.block < $scope.blocks;
        }

        function _hasPrev() {
            return $scope.block > 1;
        }

        function _next() {
            if (_hasNext()) {
                $scope.block += 1;
            }
        }

        function _prev() {
            if (_hasPrev()) {
                $scope.block -= 1;
            }
        }

        function _isLast()
        {
            return $scope.block == $scope.blocks;
        }

        function _isFirst()
        {
            return $scope.block == 1;
        }

        $scope.hasNext = _hasNext;
        $scope.next = _next;
        $scope.hasPrev = _hasPrev;
        $scope.prev = _prev;
        $scope.isLast = _isLast;
        $scope.isFirst = _isFirst;
    }
]);
