'use strict';

angular.module('dotHIVApp.controllers').controller('AccountDomainBasicEditController', ['$scope', '$location', '$stateParams', 'dothivBannerResource', '$modal',
    function ($scope, $location, $stateParams, dothivBannerResource, $modal) {

        $scope.errorMessage = null;
        $scope.banner = {};
        $scope.banner.$resolved = false;

        // set default values when form is initialized
        $scope.$watch('domaineditbasic', function () {
            $scope.domaineditbasic.$data = {};
            $scope.domaineditbasic.$data.forwarding = 'true';
            $scope.domaineditbasic.$data.secondvisit = false;
        });

        // retrieve domain id from URL parameters and get domain/banner information
        var domainName = $stateParams.name;
        $scope.domain = {name: domainName};

        dothivBannerResource.get(
            {domain: domainName},
            function (data) { // success
                $scope.domaineditbasic.$data.forwarding = (typeof data.redirect_url !== "undefined") ? "true" : "false";
                $scope.domaineditbasic.$data.secondvisit = data.position != data.position_first;
                $scope.banner = data;
                $scope.banner.domain = domainName;
                $scope.banner.$resolved = true;
            },
            function () { // error
                // no banner available, creating new one
                $scope.domaineditbasic.$data.forwarding = 'true';
                $scope.banner.redirect_url = getDefaultRedirect();
                $scope.banner.language = 'de';
                $scope.banner.position_first = 'center';
                $scope.banner.position = 'top';
                $scope.banner.domain = domainName;
                $scope.banner.$resolved = true;
            }
        );

        function getDefaultRedirect() {
            return 'http://' + domainName.split('.hiv').join('.com');
        }

        $scope.$watch('domaineditbasic.$data.forwarding', function (forwarding) {
            if (forwarding == 'false') {
                $scope.banner.redirect_url = null;
            } else {
                $scope.banner.redirect_url = getDefaultRedirect();
            }
        });

        // FIXME: don't show warning if loading page
        $scope.$watch('banner.position', function (position) {
            if (position != "invisible") {
                return;
            }
            $modal.open({'templateUrl': 'invisiblehint.html'});
        });
        $scope.$watch('banner.position_first', function (position) {
            if (position != "invisible") {
                return;
            }
            $modal.open({'templateUrl': 'invisiblehint.html'});
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
                $scope.banner.position = $scope.banner.position_first;
            }
            if ($scope.domaineditbasic.$data.forwarding == 'false') {
                $scope.banner.redirect_url = null;
            }

            dothivBannerResource.update(
                $scope.banner,
                function () {
                    // activate final tab
                    tab.active = true;
                },
                function (response) { // error
                    $scope.errorMessage = response.statusText;
                }
            );
        };

        $scope.activated = 0;
    }
]);
