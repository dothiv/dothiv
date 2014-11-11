'use strict';

angular.module('dotHIVApp.controllers').controller('UserNotificationController', ['$scope', '$http', 'security', 'User',
    function ($scope, $http, security, User) {
        $scope.notifications = null;

        security.userPromise.then(function() {
            $http.get('/api/user/' + User.getHandle() + '/notification').then(function (response) {
                $scope.notifications = response.data.items;
            });
        });

        $scope.dismiss = function(notification) {
            var data = {'dismissed': '1'};
            $http({method: 'PATCH', url: notification['@id'], data: angular.toJson(data)});
            var index = $scope.notifications.indexOf(notification);
            if (index > -1) {
                $scope.notifications.splice(index, 1);
            }
        };
    }
]);
