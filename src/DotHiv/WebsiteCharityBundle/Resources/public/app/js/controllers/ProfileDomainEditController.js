'use strict';

angular.module('dotHIVApp.controllers').controller('ProfileDomainEditController', ['$scope', '$location', '$stateParams', 'dothivDomainResource', 'Banner',
    function($scope, $location, $stateParams, dothivDomainResource, Banner) {

        $scope.formforwarding = 'true';
        $scope.formsecondvisit = true;

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

                            $scope.formforwarding = ($scope.banner.redirect_domain != '') ? 'true' : 'false';
                            $scope.formsecondvisit = ($scope.banner.position_alternative != '') ? true : false;
                            console.log($scope.banner);
                        }
                    }
                );
            }
        );

        // data structure for language values
        $scope.languages = {'de':'Deutsch', 'en':'Englisch', 'es':'Spanisch', 'la':'Latein'};

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

        // submit function for form
        $scope.submit = function(tab) {
            console.log($scope.banner);
            if ($scope.banner.id === undefined)
                $scope.banner.$save();
            else
              $scope.banner.$update();
            tab.active = true;
        };
    }
]);
