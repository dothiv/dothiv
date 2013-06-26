'use strict';

angular.module('dotHIVApp.controllers').controller('HeaderController', ['$scope', '$state', 'security', 'securityDialog',
    function($scope, $state, security, securityDialog) {
        // make state information available
        $scope.state = $state;

        $scope.isAuthenticated = function() {
            return security.isAuthenticated();
        };

        $scope.login = function() {
            securityDialog.showLogin();
        };

        $scope.logout = function() {
            security.logout();
        };

        $scope.security = security.state;

        $scope.bar = {
            'total': 10,
            'current': 1.43,
            'click': 29000,
        };

        $scope.languagechooser = {
            'content':  '<ul class="tooltip-ul">'+
                            '<li>{% trans %}header.menu.lang.de{% endtrans %}</li>'+
                            '<li>{% trans %}header.menu.lang.en{% endtrans %}</li>'+
                            '<li>{% trans %}header.menu.lang.fr{% endtrans %}</li>'+
                            '<li>{% trans %}header.menu.lang.es{% endtrans %}</li>'+
                        '</ul>',
        };

        $scope.showfunding = false;
    }
]);
