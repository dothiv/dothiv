'use strict';

angular.module('dotHIVApp.controllers').controller('HomeController', ['$scope', '$http', 'authService', '$dialog', 'securityDialog', 
        function($scope, $http, authService, $dialog, securityDialog) {
            // TODO move to a more general place
            $scope.$on('event:auth-loginRequired', function() {
                securityDialog.showLogin();
              });

            $scope.$on('event:auth-loginConfirmed', function() {
              });
        }
    ]);
