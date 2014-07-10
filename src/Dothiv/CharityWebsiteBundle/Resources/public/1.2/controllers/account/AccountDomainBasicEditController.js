'use strict';

angular.module('dotHIVApp.controllers').controller('AccountDomainBasicEditController', ['$scope', '$location', '$stateParams', 'dothivBannerResource',
    function ($scope, $location, $stateParams, dothivBannerResource) {

        $scope.errorMessage = null;

        // set default values when form is initialized
        $scope.$watch('domaineditbasic', function () {
            $scope.domaineditbasic.$data = {};
            $scope.domaineditbasic.$data.forwarding = 'true';
            $scope.domaineditbasic.$data.secondvisit = true;
        });

        // retrieve domain id from URL parameters and get domain/banner information
        var domainName = $stateParams.name;
        $scope.domain = {name: domainName};
        $scope.banner = dothivBannerResource.get(
            {domain: domainName},
            function (data) { // success
                $scope.domaineditbasic.$data.forwarding = (typeof data.redirect_url !== "undefined") ? "true" : "false";
                $scope.domaineditbasic.$data.secondvisit = data.position != data.position_first;
            },
            function () { // error
                // no banner available, creating new one
                var banner = new dothivBannerResource();
                banner.redirect_url = '';
                banner.language = 'de';
                banner.position_first = 'center';
                banner.position = 'top';
                return banner;
            }
        );

        $scope.$watch('domaineditbasic.$data.forwarding', function (forwarding) {
            if (forwarding == 'false')
                $scope.banner.redirect_url = null;
        });

        // form configuration
        $scope.nextStep = function (tab, form) {
            // check if form is valid
            if (form.$valid) {
                tab.active = true;
            }
        };

        // submit function for form
        $scope.submit = function (tab) {
            $scope.errorMessage = null;

            // do not submit values of disabled input fields
            if ($scope.domaineditbasic.$data.secondvisit == false) {
                $scope.banner.position_alternative = null;
            }
            if ($scope.domaineditbasic.$data.forwarding == 'false') {
                $scope.banner.redirect_url = null;
            }

            $scope.banner.$update(
                {domain: domainName},
                function () {
                    // activate final tab
                    tab.active = true;
                },
                function (response) { // error
                    $scope.errorMessage = response.statusText;
                    $scope.tabs[1].active = true;
                }
            );
        };

        $scope.activated = 0;
    }
]);
