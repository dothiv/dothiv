'use strict';

angular.module('dotHIVApp.controllers').controller('CheckoutController', ['$rootScope', '$scope', 'OrderModel', 'config', 'Price', '$http', '$state', '$stateParams', function ($rootScope, $scope, OrderModel, config, Price, $http, $state, $stateParams) {
    if (OrderModel.isDone() || !OrderModel.isConfigured()) {
        $state.transitionTo('lookupform', {"locale": $stateParams.locale});
    }
    $scope.order = OrderModel;
    $scope.contact = OrderModel.contact;
    $scope.domain = $stateParams.domain;
    $scope.price = Price.getFormattedPricePerYear($scope.domain);
    $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain);
    $scope.countries = [];

    $scope.$watch('order.duration', function () {
        updateTotals();
    });
    $scope.$watch('contact.vat', function () {
        updateTotals();
    });

    $scope.vatIncluded = function() {
        return OrderModel.vatIncluded();
    };

    var updateTotals = function () {
        var itemTotal = Price.getPricePerYear($scope.domain);
        var vatTotal = OrderModel.vatIncluded() ? Price.calculateVat(itemTotal) : 0;
        var total = itemTotal + vatTotal;
        $scope.vatTotal = Price.format(vatTotal / 100);
        $scope.itemTotal = Price.format(itemTotal / 100);
        $scope.total = Price.format(total / 100);
        return total;
    };

    $scope.selectCountry = function (country) {
        OrderModel.countryModel = country;
    };

    $scope.blurCountry = function () {
        var country = getCountryByPartial($scope.contact.country);
        if (country != null) {
            $scope.contact.country = country.name;
            OrderModel.countryModel = country;
        } else {
            $scope.contact.country = null;
            OrderModel.countryModel = null;
        }
    };

    var getCountryByPartial = function (partial) {
        if (typeof partial !== "string") {
            return null;
        }
        var match = null;
        for (var i = 0; i < $scope.countries.length; i++) {
            if ($scope.countries[i].name.toLowerCase().indexOf(partial.toLowerCase()) > -1) {
                if (match !== null) {
                    return null;
                }
                match = $scope.countries[i];
            }
        }
        return match;
    };

    // Init
    updateTotals();

    // Load countries
    $http.get('/bundles/dothivbasewebsite/data/countries.json').success(function (data) {
        var countries = [];
        for (var i = 0; i < data.length; i++) {
            countries.push({"name": data[i][0], "eu": data[i][1]});
        }
        $scope.countries = countries;
    });

    // Checkout
    $scope.allChecked = function (model) {
        var allChecked = true;
        for (var k in $scope[model]) {
            if ($scope[model][k] != true) {
                allChecked = false;
            }
        }
        return allChecked;
    };

    var handler = StripeCheckout.configure({
        key: config.stripe.publishableKey,
        image: config.stripe.logo,
        token: function (token) {
            $rootScope.$apply(function () {
                // Persist
                OrderModel.stripe.token = token.id;
                OrderModel.stripe.card = token.card.id;
                $state.transitionTo('done', {"locale": $stateParams.locale, "domain": $scope.domain})
            });
        },
        closed: function () {
        }
    });

    $scope.submit = function () {
        handler.open({
            name: $scope.domain,
            description: $scope.stripe.description,
            amount: updateTotals(),
            currency: Price.currency.toUpperCase(),
            email: OrderModel.contact.email,
            panelLabel: $scope.stripe.button
        });
    };
}]);
