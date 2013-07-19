'use strict';

angular.module('dotHIVApp.controllers').controller('LoginDialogController', ['$scope', 'dialog', 'security', '$window', '$location', '$translate', 'formManager',
    function($scope, dialog, security, $window, $location, $translate, formManager) {
        var loginFormManager = formManager('login', $scope);
        var registrationFormManager = formManager('registration', $scope);

        $scope.$watch('login', function(newval, oldval) {
            $scope.login.focus.username = 1;
        });

        $scope.signin = function(data) {
            if($scope.login.$invalid) {
                loginFormManager.fail();
            } else {
                security.login(data.username, data.password, function(result, error) {
                    if (result) {
                        dialog.close();
                    } else {
                        loginFormManager.showServerError(error.data);
                    }
                });
            }
        };

        $scope.register = function(data) {
            if ($scope.registration.$invalid) {
                registrationFormManager.fail();
            } else {
                security.register(data.name, data.surname, data.email, data.password, function(result, error) {
                    if (result) {
                        dialog.close();
                    } else {
                        registrationFormManager.showServerFormError(error);
                    }
                });
            }
        };

        $scope.thirdparty = function(url) {
            var popup = $window.open(url, 'thirdpartyLogin', 'width=580,height=200,location=no,menubar=no', false);
            $window.addEventListener('message', receiveMessage, false);
        }

        function receiveMessage(event) {
            if (event.origin !== $location.protocol() + "://" + $location.host()) {
                return;
            }

            if (event.data) {
                security.updateUserInfo();
                dialog.close();
            }
        };

        $scope.abort = function() {
            dialog.close();
        };
    }
]);
