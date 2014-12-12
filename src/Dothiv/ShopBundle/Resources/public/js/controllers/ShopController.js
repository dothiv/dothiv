'use strict';

angular.module('dotHIVApp.controllers').controller('ShopController', ['$scope', 'config', 'MoneyFormatter', function ($scope, config, MoneyFormatter) {
    var mf = new MoneyFormatter(config.locale);
    var currency = config.locale == 'en' ? 'usd' : 'eur';
    var symbol = config.locale == 'en' ? '$' : '€';
    $scope.pricePerMonth = mf.decimalFormat(config.shop.price[currency] / 12, symbol);
    $scope.order = {
        domain: "",
        clickcounter: true
    };

    $scope.$watch('order.domain', function (domain) {
        if (typeof domain == "undefined") {
            $scope.domain = "";
        } else {
            $scope.domain = domain + ".hiv";
        }
    });
}]);
