'use strict';

angular.module('dotHIVApp.controllers').controller('AccountDomainBasicEditController', ['$scope', '$location', '$stateParams', 'dothivBannerResource', '$q',
    function ($scope, $location, $stateParams, dothivBannerResource, $q) {

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
            function () { // success
            },
            function () { // error
                // no banner available, creating new one
                var banner = new dothivBannerResource();
                banner.redirect_domain = '';
                banner.language = 'de';
                banner.position = 'center';
                banner.position_alternative = 'top';
                banner.domain = domainName;
                return banner;
            }
        );

        // data structure for language values
        $scope.languages =
            [
                { key: 'de', label: 'Deutsch' },
                { key: 'en', label: 'Englisch' },
                { key: 'es', label: 'Spanisch' },
                { key: 'fr', label: 'Französisch' }
            ];

        $scope.$watch('domaineditbasic.$data.forwarding', function (forwarding) {
            if (forwarding == 'false')
                $scope.banner.redirect_domain = null;
        });

        // form configuration
        $scope.nextStep = function (tab, form) {
            // check if form is valid
            if (form.$valid)
                tab.active = true;
            else
                manager.fail();
        };

        // submit function for form
        $scope.submit = function (tab) {
            // do not submit values of disabled input fields
            if ($scope.domaineditbasic.$data.secondvisit == false)
                $scope.banner.position_alternative = null;
            if ($scope.domaineditbasic.$data.forwarding == 'false')
                $scope.banner.redirect_domain = null;

            // distinguish between new and updated banners
            if ($scope.banner.id === undefined)
                $scope.banner.$save();
            else
                $scope.banner.$update();

            // activate final tab
            tab.active = true;
        };

        $scope.activated = 0;
        $scope.dnsForward = function () {
            $scope.domain.dnsForward = 1;
            $scope.activated = 1;
            $scope.domain.$update(function () {
                $scope.activated = 2;
            });
        };
    }
]);
