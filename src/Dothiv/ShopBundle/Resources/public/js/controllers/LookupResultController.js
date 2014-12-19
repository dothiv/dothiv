'use strict';

angular.module('dotHIVApp.controllers').controller('LookupResultController', [
    '$scope', '$state', '$stateParams', 'Price', '$http', 'idn', 'OrderModel',
    function ($scope, $state, $stateParams, Price, $http, idn, OrderModel) {
        $scope.loading = false;
        $scope.lookup = null;
        $scope.domain = $stateParams.domain;
        $scope.secondLevel = $stateParams.domain.split('.hiv').join('');
        $scope.price = Price.getFormattedPricePerYear($scope.domain);
        $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain);

        var lookupDomain = function (domain) {
            $scope.loading = true;
            $http.get('/api/shop/lookup?q=' + idn.toASCII(domain))
                .success(function (data) {
                    OrderModel.available = false;
                    if (data.available) {
                        $scope.lookup = "available";
                        OrderModel.available = true;
                    } else if (data.premium) {
                        $scope.lookup = "premium";
                        // } else if (data.trademark) {
                    } else { // if(data.registered) {
                        $scope.lookup = "registered";
                        var alternatives = [
                            $scope.secondLevel + '4life.hiv',
                            $scope.secondLevel + 'supports.hiv',
                            $scope.secondLevel + 'fightsaids.hiv',
                            $scope.secondLevel + 'fights.hiv',
                            'click' + $scope.secondLevel + '.hiv'
                        ];
                        if ($stateParams.locale == 'de') {
                            alternatives.push($scope.secondLevel + 'unterstützt.hiv');
                        }
                        $scope.alternatives = alternatives;
                    }
                })
                .error(function (response, code, headers, request) {
                    $scope.loading = false;
                })
            ;
        };

        // 4lifepromo
        $scope.promoAvailable = false;
        $scope.promoDomain = $scope.secondLevel + "4life.hiv";
        var lookupPromoDomain = function (domain) {
            $http.get('/api/shop/lookup?q=' + idn.toASCII(domain))
                .success(function (data) {
                    if (data.available) {
                        $scope.promoAvailable = true;
                        $scope.promoPrice = Price.getFormattedPricePerYear(domain);
                        $scope.promoPricePerMonth = Price.getFormattedPricePerMonth(domain);
                    }
                })
            ;
        };

        // Init
        lookupDomain($stateParams.domain);
        if ($stateParams.domain.indexOf("4life.hiv") < 0) {
            lookupPromoDomain($scope.promoDomain);
        }
    }]);
