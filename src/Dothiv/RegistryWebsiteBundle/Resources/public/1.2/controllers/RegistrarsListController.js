'use strict';

angular.module('dotHIVApp.controllers').controller('RegistrarsListController', ['$scope', '$http', 'config', 'MoneyFormatter', '$sce',
    function ($scope, $http, config, MoneyFormatter, $sce) {

        $scope.predicate = ['priceUSD', 'priceEUR'];

        var mf = new MoneyFormatter(config.locale);
        $scope.registrars = [];

        function buildPrice(registrar, currency) {
            var col = 'pricePerYear' + currency;
            if (typeof  registrar[col] == 'undefined') {
                return ["–", Infinity];
            }
            var value = parseInt(registrar[col], 10);
            var sortValue = value;
            if (currency == 'Usd') {
                sortValue = sortValue / config.eur_to_usd;
                var valueLabel = mf.decimalFormat(value, '$');
            } else {
                var valueLabel = mf.decimalFormat(value, '€');
            }
            return [valueLabel, sortValue]
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
                        country: data[k].country,
                        priceUSDLabel: priceUSD[0],
                        priceEURLabel: priceEUR[0],
                        priceUSD: priceUSD[1],
                        priceEUR: priceEUR[1],
                        url: data[k].url,
                        promotion: $sce.trustAsHtml(data[k].promotion),
                        privateRegistrar: data[k].privateRegistrar == true
                    }
                );
            }
            $scope.registrars = registrars;
        }

        $http({method: 'GET', url: '/' + config.locale + '/content/Registrar?markdown=promotion:inline'}).success(success);
    }]);
