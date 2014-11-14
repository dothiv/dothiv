'use strict';

angular.module('dotHIVApp.controllers').controller('RegistrarsListController', ['$scope', '$http', 'config', 'MoneyFormatter', '$sce',
    function ($scope, $http, config, MoneyFormatter, $sce) {

        $scope.currency = 'USD';
        $scope.predicate = '+priceUSD';
        $scope.eur_to_usd = config.eur_to_usd;

        var mf = new MoneyFormatter(config.locale);
        $scope.registrars = [];

        function buildPrice(registrar, currency) {
            var col = 'pricePerYear' + currency;
            var converted = false;
            if (typeof  registrar[col] == 'undefined') {
                if (currency == 'Usd' && typeof  registrar['pricePerYearEur'] != 'undefined') {
                    registrar[col] = registrar['pricePerYearEur'] * config.eur_to_usd;
                    converted = true;
                } else if (currency == 'Eur' && typeof registrar['pricePerYearUsd'] != 'undefined') {
                    registrar[col] = registrar['pricePerYearUsd'] / config.eur_to_usd;
                    converted = true;
                } else {
                    return ["–", Infinity];
                }

            }
            var value = parseInt(registrar[col], 10);
            var valueLabel;
            if (currency == 'Usd') {
                valueLabel = mf.decimalFormat(value, '$');
            } else {
                valueLabel = mf.decimalFormat(value, '€');
            }
            if (converted) {
                valueLabel = '*' + valueLabel;
            }
            return [valueLabel, value]
        }

        function success(data) {
            var registrars = [];
            var num = data.length;
            for (var k in data) {
                var priceUSD = buildPrice(data[k], 'Usd');
                var priceEUR = buildPrice(data[k], 'Eur');
                registrars.push(
                    {
                        name: data[k].name,
                        image: data[k].image,
                        preferredDeal: $sce.trustAsHtml(data[k].preferredDeal),
                        isPreferredDeal: typeof data[k].preferredDeal != "undefined",
                        country: data[k].country,
                        priceUSDLabel: priceUSD[0],
                        priceUSD: priceUSD[1],
                        priceEURLabel: priceEUR[0],
                        priceEUR: priceEUR[1],
                        url: data[k].url,
                        promotion: $sce.trustAsHtml(data[k].promotion),
                        privateRegistrar: data[k].privateRegistrar == true,
                        isPriceLeader: data[k].priceLeader == true
                    }
                );
            }
            $scope.registrars = registrars;
        }

        $scope.flipPredicate = function (p) {
            if (typeof p == "string") {
                var newDir = "+";
                if (typeof $scope.predicate == "string" && $scope.predicate.substr(1) == p) {
                    var dir = $scope.predicate.substr(0, 1);
                    newDir = dir == '+' ? '-' : '+';
                }
                $scope.predicate = newDir + p;
            }
        };

        $http({
            method: 'GET',
            url: '/' + config.locale + '/content/Registrar?markdown=promotion:inline,preferredDeal:inline'
        }).success(success);
    }]);
