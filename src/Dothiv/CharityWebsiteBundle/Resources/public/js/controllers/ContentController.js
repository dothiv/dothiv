'use strict';

/**
 * This controller is used for elements which need to access arbitrary content.
 */
angular.module('dotHIVApp.controllers').controller('ContentController', ['$scope', '$http', '$sce', 'config',
    function ($scope, $http, $sce, config) {

        $scope.items = [];
        $scope.loaded = false;
        $scope.markdownFields = [];

        // Fetch items
        $scope.fetch = function (contentType, markdownFields) {
            $scope.type = contentType;
            if (markdownFields instanceof Array) {
                $scope.markdownFields = markdownFields;
            }
            var url = '/' + config.locale + '/content/' + contentType;
            if ($scope.markdownFields.length > 0) {
                url += '?markdown=' + markdownFields.join(',');
            }
            $http({method: 'GET', url: url}).success(success);
        };

        // Process response
        function success(data) {
            var items = [];
            for (var i = 0; i < data.length; i++) {
                var item = data[i];
                for (var j = 0; j < $scope.markdownFields.length; j++) {
                    item[$scope.markdownFields[j]] = $sce.trustAsHtml(item[$scope.markdownFields[j]]);
                }
                items[i] = item;
            }
            $scope.items = items;
            $scope.loaded = true;
        }
    }
]);
