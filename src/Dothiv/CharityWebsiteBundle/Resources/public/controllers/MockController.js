'use strict';

angular.module('dotHIVApp.controllers').controller('MockController', ['$scope', '$http', '$dialog',
        function($scope, $http, $dialog) {
            function result(data, status, headers, config) {
                $dialog.dialog({
                    keyboard: true, // TODO make these values default
                    backdropClick: true, // TODO make these values default
                    dialogFade: true, // TODO make these values default
                    backdropFade: true, // TODO make these values default
                    template: '<h1>' + ((status >= 200 && status <= 299) ? 'success ' : 'failure ') + status + '</h1><p>' + (angular.isObject(data) ? JSON.stringify(data) : data).replace(/\[\[/g, ' [ [ ') + '</p>'
                }).open();
            }

            $scope.registerdomain = function(data) {
                $http({method: 'POST', url: 'api/domains', data: data}).success(result).error(result);
            }
        }
    ]);
