'use strict';

angular.module('dotHIVApp.controllers').controller('CheckoutController', [
    '$rootScope', '$scope', 'OrderModel', 'config', 'Price', '$http', '$state', '$stateParams', 'idn', '$location', '$anchorScroll', '$window',
    function ($rootScope, $scope, OrderModel, config, Price, $http, $state, $stateParams, idn, $location, $anchorScroll, $window) {
        if (OrderModel.isDone() || !OrderModel.isConfigured()) {
            $state.transitionTo('lookupform', {"locale": $stateParams.locale});
        }
        OrderModel.step = 3;
        OrderModel.currency = Price.currency.toUpperCase();
        $scope.order = OrderModel;
        $scope.contact = OrderModel.contact;
        $scope.countryModel = OrderModel.countryModel;
        $scope.domain = $stateParams.domain;
        $scope.price = Price.getFormattedPricePerYear($scope.domain);
        $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain);
        $scope.countries = [];

        var inital = true;

        $scope.$watch('order.duration', function (duration, oldDuration) {
            if (duration === oldDuration) {
                return;
            }
            updateTotals();
            var tr = $('table.summary tr.duration');
            if (!tr.hasClass('hot')) {
                tr.addClass('hot');
                $window.setTimeout(function () {
                    $('table.summary tr.duration').removeClass('hot');
                }, 200);
            }
        });
        $scope.$watch('contact.organization', function (newValue, oldValue) {
            if (newValue === oldValue) {
                return;
            }
            updateTotals();
        });
        $scope.$watch('contact.country', function (newValue, oldValue) {
            if (newValue === oldValue) {
                return;
            }
            updateTotals();
        });
        $scope.$watch('contact.vat', function (newValue, oldValue) {
            if (newValue === oldValue) {
                return;
            }
            updateTotals();
        });

        $scope.vatIncluded = function () {
            return OrderModel.vatIncluded();
        };

        var updateTotals = function () {
            var itemTotal = Price.getPricePerYear($scope.domain) * OrderModel.duration;
            var vatTotal = OrderModel.vatIncluded() ? Price.calculateVat(itemTotal) : 0;
            var total = itemTotal + vatTotal;
            $scope.vatTotal = Price.format(vatTotal / 100);
            $scope.vatPercent = OrderModel.vatIncluded() ? Price.getVat() + "%" : null;
            $scope.itemTotal = Price.format(itemTotal / 100);
            $scope.total = Price.format(total / 100);
            return total;
        };

        $scope.selectCountry = function (country) {
            OrderModel.countryModel = country;
            $scope.countryModel = country;
        };

        $scope.blurCountry = function () {
            var country = getCountryByPartial($scope.contact.country);
            if (country != null) {
                $scope.contact.country = country.name;
                OrderModel.countryModel = country;
                $scope.countryModel = country;
            } else {
                $scope.contact.country = null;
                OrderModel.countryModel = null;
                $scope.countryModel = null;
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
        $http.get('/bundles/dothivbasewebsite/data/countries-' + $stateParams.locale + '.json').success(function (data) {
            var countries = [];
            for (var i = 0; i < data.length; i++) {
                countries.push({"iso": data[i][0], "name": data[i][1], "eu": data[i][2]});
            }
            $scope.countries = countries;
        });

        // Review
        $scope.review = function () {
            $scope.step = 'review';
            $location.hash('review');
            $anchorScroll();
            OrderModel.step = 4;
        };

        $scope.edit = function () {
            $scope.step = 'edit';
            $location.hash('edit');
            $anchorScroll();
            OrderModel.step = 3;
        };

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
                    var data = OrderModel.flatten();
                    $http({
                        method: 'PUT',
                        url: "/api/shop/order/" + idn.toASCII($scope.domain),
                        data: angular.toJson(data)
                    })
                        .success(function (data) {
                            $state.transitionTo('done', {"locale": $stateParams.locale, "domain": $scope.domain})
                        })
                        .error(function (response, code, headers, request) {
                            // FIXME: Show modal
                        })
                    ;
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
                currency: OrderModel.currency,
                email: OrderModel.contact.email,
                panelLabel: $scope.stripe.button
            });
        };
    }]);
