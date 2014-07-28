'use strict';

angular.module('dotHIVApp.controllers').controller('ConfigureController', ['$scope', 'dothivBannerResource', 'config', '$state', '$modal', '$timeout',
    function ($scope, dothivBannerResource, config, $state, $modal, $timeout) {

        $scope.fullscreen = false;
        $scope.bannerPosition = 'center';
        $scope.iframeUrl = null;
        $scope.iframeBaseUrl = null;
        $scope.iframeStyle = {'height': '600px'};
        $scope.settings = 'general';
        $scope.bannerForm = {};

        $scope.banner = dothivBannerResource.get(
            {'domain': config.domain},
            function () { // success
                $scope.bannerForm.redirectUrl = $scope.banner.redirect_url;
            },
            function (response) { // error
                if (response.status == 403) {
                    // User not allowed for this domain.
                    $modal.open({'templateUrl': 'forbidden.html', 'backdrop': 'static'});
                }
                if (response.status == 404) {
                    $scope.banner.redirect_url = null;
                    $scope.banner.language = 'de';
                    $scope.banner.position_first = 'center';
                    $scope.banner.position = 'top';
                }
            }
        );

        function updateIframeSize() {
            var height = $(window).height() - $('#topnav').outerHeight();
            if (!$scope.fullscreen) {
                height -= $('body > header').outerHeight();
                height -= $('#settings').outerHeight();
            }
            $scope.iframeStyle = {'height': height + 'px'};
        }

        function updatePreview() {
            if ($scope.iframeBaseUrl == null) {
                $scope.iframeBaseUrl = $scope.iframeUrl;
            }
            $scope.iframeUrl = $scope.iframeBaseUrl + '?position=' + $scope.bannerPosition + '&' + (new Date() / 1000);
        }

        $scope.$watch('bannerPosition', function () {
            updatePreview();
        });

        $scope.$watch('fullscreen', function () {
            $timeout(updateIframeSize, 100);
        });
        $scope.$watch('settings', function () {
            $timeout(updateIframeSize, 100);
        });

        $scope.updateBannerSettings = function () {
            if (!$scope.bannerForm.$valid) {
                return;
            }
            $scope.banner.domain = config.domain;
            $scope.banner.redirect_url = $scope.bannerForm.redirectUrl;
            $scope.banner = dothivBannerResource.update(
                $scope.banner,
                function () { // success
                    updatePreview();
                }
            );
        }
    }
]);
