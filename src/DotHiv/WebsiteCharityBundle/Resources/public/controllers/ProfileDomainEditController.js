'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainEditController', ['$scope', '$location', '$stateParams', 'dothivDomainResource', 'Banner', 'formManager',
    function($scope, $location, $stateParams, dothivDomainResource, Banner, formManager) {

        var manager = formManager('domaineditbasic', $scope, 'banner');

        // set default values when form is initialized
        $scope.$watch('domaineditbasic', function() {
            $scope.domaineditbasic.$data = {};
            $scope.domaineditbasic.$data.forwarding = 'true';
            $scope.domaineditbasic.$data.secondvisit = true;
        });

        // retrieve domain id from URL parameters and get domain/banner information
        var domainId = $stateParams.domainId;
        $scope.domain = dothivDomainResource.get(
            {"id": domainId},
            function() { // success
                // retrieve list of banners for this domain
                $scope.banners = dothivDomainResource.getBanners(
                    {"id": domainId},
                    function() { // success
                        if ($scope.banners.length == 0) {
                            // no banner available, creating new one
                            $scope.banner = new Banner();
                            $scope.banner.redirect_domain = '';
                            $scope.banner.language = 'de';
                            $scope.banner.position = 'center';
                            $scope.banner.position_alternative = 'top';

                            console.log($scope.banner);
                        } else {
                            // always take the first banner, TODO: let the user decide
                            $scope.banner = new Banner();
                            $scope.banner.id = $scope.banners[0].id;
                            $scope.banner.redirect_domain = $scope.banners[0].redirect_domain;
                            $scope.banner.language = $scope.banners[0].language;
                            $scope.banner.position = $scope.banners[0].position;
                            $scope.banner.position_alternative = $scope.banners[0].position_alternative;

                            $scope.domaineditbasic.$data.forwarding = ($scope.banner.redirect_domain != undefined) ? 'true' : 'false';
                            if ($scope.banner.position_alternative != undefined) {
                                $scope.domaineditbasic.$data.secondvisit = true;
                            } else {
                                $scope.domaineditbasic.$data.secondvisit = false;
                                $scope.banner.position_alternative = 'top';
                            }
                            console.log($scope.banner);
                        }
                    }
                );
            }
        );

        // data structure for language values
        $scope.languages = {'de':'Deutsch', 'en':'Englisch', 'es':'Spanisch', 'la':'Latein'};

        $scope.$watch('domaineditbasic.$data.forwarding', function(forwarding) {
            if (forwarding == 'false')
                $scope.banner.redirect_domain = null;
        });

        // form configuration
        $scope.nextStep = function(tab, form) {
            // check if form is valid
            if (form.$valid)
                tab.active = true;
            else
                manager.fail();
        };

        // submit function for form
        $scope.submit = function(tab) {
            // do not submit values of disabled input fields
            if ($scope.domaineditbasic.$data.secondvisit == false)
                $scope.banner.position_alternative = null;
            if ($scope.domaineditbasic.$data.forwarding == 'false')
                $scope.banner.redirect_domain = null;

            console.log($scope.banner);

            // distinguish between new and updated banners
            if ($scope.banner.id === undefined)
                $scope.banner.$save();
            else
                $scope.banner.$update();

            // activate final tab
            tab.active = true;
        };
    }
]);
