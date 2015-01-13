'use strict';

angular.module('dotHIVApp.controllers').controller('LookupResultController', [
    '$scope', '$state', '$stateParams', 'Price', '$http', 'idn', 'OrderModel', 'config',
    function ($scope, $state, $stateParams, Price, $http, idn, OrderModel, config) {
        $scope.loading = false;
        $scope.lookup = null;
        $scope.domain = $stateParams.domain;
        OrderModel.setDomain($stateParams.domain);
        $scope.secondLevel = $stateParams.domain.split('.hiv').join('');
        $scope.price = Price.getFormattedPricePerYear($scope.domain);
        $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain);


        var lookupDomain = function (domain) {
            $scope.loading = true;
            $scope.showLookupForm = false;
            $http.get('/api/shop/lookup?q=' + idn.toASCII(domain))
                .success(function (data) {
                    OrderModel.available = false;
                    if (data.available) {
                        $scope.lookup = "available";
                        OrderModel.available = true;
                    } else if (data.premium) {
                        $scope.lookup = "premium";
                    } else if (data.trademark) {
                        $scope.lookup = "trademark";
                    } else { // if(data.registered) {
                        $scope.lookup = "registered";
                        var alternatives = [];
                        var secondlevel = $scope.secondLevel.replace(/4life$/, '');
                        if (secondlevel !== $scope.secondLevel) {
                            alternatives.push(secondlevel + 'is4life.hiv');
                            alternatives.push(secondlevel + '14life.hiv'); // Increase counter, maybe.
                            alternatives.push(secondlevel + 'fight4life.hiv');
                            alternatives.push(secondlevel + 'supports4life.hiv');
                            alternatives.push(secondlevel + 'hopes4life.hiv');
                        } else {
                            if ($stateParams.locale == 'de') {
                                alternatives.push(secondlevel + '-gegen-aids.hiv');
                                alternatives.push(secondlevel + '-sozial.hiv');
                                alternatives.push(secondlevel + '-macht-mit.hiv');
                            } else {
                                alternatives.push(secondlevel + 'supports.hiv');
                                alternatives.push(secondlevel + 'fortheendofaids.hiv');
                                alternatives.push(secondlevel + 'forhope.hiv');
                            }
                        }
                        $scope.alternatives = alternatives;
                    }
                    $scope.showLookupForm = true;
                })
                .error(function (response, code, headers, request) {
                    $scope.loading = false;
                    $scope.showLookupForm = true;
                })
            ;
        };

        // 4lifepromo
        $scope.promoAvailable = false;
        $scope.promoDomain = $scope.secondLevel.toLowerCase() + "4life.hiv";
        var lookupPromoDomain = function (domain) {
            $http.get('/api/shop/lookup?q=' + idn.toASCII(domain.toLowerCase()))
                .success(function (data) {
                    if (data.available) {
                        $scope.promoAvailable = true;
                        $scope.promoPrice = Price.getFormattedPricePerYear(domain);
                        $scope.promoPricePerMonth = Price.getFormattedPricePerMonth(domain);
                    }
                })
            ;
        };

        $scope.lookupDomain = function (domain) {
            $state.transitionTo('lookup', {"locale": $stateParams.locale, "domain": domain.toLowerCase()});
        };

        // Init
        lookupDomain($stateParams.domain);
        if ($stateParams.domain.indexOf("4life.hiv") < 0 && config.shop.promo.name4life) {
            lookupPromoDomain($scope.promoDomain);
        }
    }]);
