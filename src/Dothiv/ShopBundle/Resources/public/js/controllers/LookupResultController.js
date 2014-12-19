'use strict';

angular.module('dotHIVApp.controllers').controller('LookupResultController', [
    '$scope', '$state', '$stateParams', 'Price', '$http', 'idn', 'OrderModel',
    function ($scope, $state, $stateParams, Price, $http, idn, OrderModel) {
        $scope.loading = false;
        $scope.lookup = null;
        $scope.domain = $stateParams.domain;
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
                        var secondLevel = domain.split('.hiv').join('');
                        var alternatives = [
                            secondLevel + '4life.hiv',
                            secondLevel + 'supports.hiv',
                            secondLevel + 'fightsaids.hiv',
                            secondLevel + 'fights.hiv',
                            'click' + secondLevel + '.hiv'
                        ];
                        if ($stateParams.locale == 'de') {
                            alternatives.push(secondLevel + 'unterstützt.hiv');
                        }
                        $scope.alternatives = alternatives;
                    }
                })
                .error(function (response, code, headers, request) {
                    $scope.loading = false;
                })
            ;
        };

        // Init
        lookupDomain($stateParams.domain);
    }]);
