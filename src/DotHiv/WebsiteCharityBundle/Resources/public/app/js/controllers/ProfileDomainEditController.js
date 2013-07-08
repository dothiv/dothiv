'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainEditController', ['$scope', '$location', '$stateParams', 'dothivDomainResource',
    function($scope, $location, $stateParams, dothivDomainResource) {

        // retrieve domain id from URL parameters and get domain information
        var domainId = $stateParams.domainId;
        $scope.domain = dothivDomainResource.get({"id": domainId});

        // data structure for form values
        $scope.domainData = {};
        $scope.domainData.forwarding = 'true';
        $scope.domainData.bannerposition = 'center';
        $scope.domainData.bannersecondposition = 'upper';
        $scope.domainData.targetdomain = '';
        $scope.domainData.language = 'Deutsch';

        $scope.languages = {'german':'Deutsch', 'english':'Englisch', 'spanish':'Spanisch', 'latin':'Latein'};

        // form configuration
        $scope.formclean = true;
        $scope.nextStep = function(form, tab) {
            if (form.$valid) {
                console.log('valid');
                tab.active = true;
            } else {
                console.log('invalid');
                $scope.formclean = false;
            }
        };
        $scope.submit = function(tab) {
            console.log($scope.domainData);
            //dothivDomainResource.save(); //TODO
            tab.active = true;
        };
    }
]);
