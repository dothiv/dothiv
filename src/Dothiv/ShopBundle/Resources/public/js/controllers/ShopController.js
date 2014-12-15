'use strict';

angular.module('dotHIVApp.controllers').controller('ShopController', ['$scope', 'config', 'MoneyFormatter', '$window', '$http', function ($scope, config, MoneyFormatter, $window, $http) {
    var mf = new MoneyFormatter(config.locale);
    var currency = config.locale == 'en' ? 'usd' : 'eur';
    var symbol = config.locale == 'en' ? '$' : '€';
    // FIXME: Use float format
    $scope.price = mf.decimalFormat(config.shop.price[currency], symbol);
    $scope.pricePerMonth = mf.decimalFormat(config.shop.price[currency] / 12, symbol);
    $scope.order = {
        clickcounter: true,
        redirect: "",
        duration: 1
    };
    $scope.contact = {
        organization: null
    };
    $scope.secondLevelName = "caro";
    $scope.domain = "caro.hiv";
    $scope.order.redirect = "http://caro.com/";
    $scope.country = null;

    $scope.$watch('secondLevelName', function (domain) {
        if (typeof domain == "undefined") {
            $scope.domain = "";
        } else {
            $scope.domain = domain + ".hiv";
        }
        $scope.redirectExample = "http://www." + domain + ".com/";
    });
    $scope.$watch('order.duration', function (domain) {
        updateTotals();
    });

    var vatIncluded = $scope.vatIncluded = function () {
        if ($scope.country == null) {
            return false;
        }
        if (!$scope.country.eu) {
            return false;
        }
        if (!$scope.contact.organization || $scope.contact.organization.length == 0) {
            return false;
        }
        return true;
    };

    var updateTotals = function () {
        var vatTotal = vatIncluded() ? $scope.order.duration * config.shop.price[currency] * (config.vat.de / 100) : 0;
        var itemTotal = $scope.order.duration * config.shop.price[currency];
        $scope.vatTotal = mf.format(vatTotal, symbol);
        $scope.itemTotal = mf.format(itemTotal, symbol);
        $scope.total = mf.format(vatTotal + itemTotal, symbol);
    };

    $scope.lookupDomain = function () {
        if ($scope.secondLevelName == "cook") {
            $scope.lookup = "premium";
        } else if ($scope.secondLevelName == "cto") {
            $scope.lookup = "registered";
            $scope.alternatives = [
                'cto4life',
                'ctosupports',
                'ctofightsaids',
                'ctofights',
                'clickcto'
            ];
        } else {
            $scope.lookup = "available";
        }
    };

    $scope.selectCountry = function (country) {
        $scope.country = country;
    };

    // Init
    updateTotals();
    $window.setTimeout(function () {
        $('label code').each(function (_, el) {
            var code = $(el);
            var label = $(code.parentsUntil('label').parent());
            code.click(function (ev) {
                var input = $($('#' + $(label).attr("for")));
                input.val(code.text());
                input.keyup();
            });
        });
    }, 100);
    $scope.countries = [];
    $http.get('/bundles/dothivbasewebsite/data/countries.json').success(function (data) {
        var countries = [];
        for (var i = 0; i < data.length; i++) {
            countries.push({"name": data[i][0], "eu": data[i][1]});
        }
        $scope.countries = countries;
    });
}]);
