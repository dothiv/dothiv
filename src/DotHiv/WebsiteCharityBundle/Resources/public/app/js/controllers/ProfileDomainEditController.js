'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainEditController', ['$scope', '$location', '$stateParams', 'security',
    function($scope, $location, $stateParams, security) {

        $scope.domainName = $stateParams.domainName; //TODO

        $scope.domainforwarding = 'true';
        $scope.bannerposition = 'center';
        $scope.bannersecondposition = 'upper';
        $scope.targetdomain = '';
        $scope.language = 'Deutsch';
        $scope.languages = {'german':'Deutsch', 'english':'Englisch', 'spanish':'Spanisch', 'latin':'Latein'};

        // form configuration
        $scope.formclean = true;
        $scope.nextStep = function (form, tab) {
            if (form.$valid) {
                console.log('valid');
                tab.active = true;
            } else {
                console.log('invalid');
                $scope.formclean = false;
            }
        };
    }
]);
