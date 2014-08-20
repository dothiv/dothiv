'use strict';

angular.module('dotHIVApp.controllers').controller('RegistrarsListController', ['$scope', '$http', 'config', 'MoneyFormatter', '$sce',
    function ($scope, $http, config, MoneyFormatter, $sce) {

        $scope.predicate = 'random';

        var mf = new MoneyFormatter(config.locale);
        $scope.registrars = [];

        function buildPrice(registrar) {
            if (typeof  registrar.pricePerYear == 'undefined') {
                return ["–", Infinity];
            }
            var value = parseInt(registrar.pricePerYear, 10);
            var sortValue = value;
            if (registrar.pricePerYearCurrency == 'USD') {
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
                var price = buildPrice(data[k]);
                registrars.push(
                    {
                        name: data[k].name,
                        image: data[k].image,
                        country: data[k].country,
                        priceLabel: price[0],
                        price: price[1],
                        url: data[k].url,
                        promotion: $sce.trustAsHtml(data[k].promotion),
                        random: parseInt(Math.random() * num)
                    }
                );

            }
            $scope.registrars = registrars;
        }

        $http({method: 'GET', url: '/' + config.locale + '/content/Registrar?markdown=promotion:inline'}).success(success);
    }]);
