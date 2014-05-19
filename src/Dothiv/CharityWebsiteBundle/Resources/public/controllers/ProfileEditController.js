'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileEditController', ['$scope', '$location', 'security', 'dothivUserResource',
    function($scope, $location, security, dothivUserResource) {
        // get fresh user object
        $scope.user = dothivUserResource.get({"username": security.state.user.username});

        // send user object back to server
        $scope.submit = function() {
            $scope.user.$update(
                {"username": security.state.user.username},
                function() { // success
                    security.updateUserInfo();
                    $location.path( "/profile" );
                }, 
                function() { // error
                }
            );
        };
    }
]);
