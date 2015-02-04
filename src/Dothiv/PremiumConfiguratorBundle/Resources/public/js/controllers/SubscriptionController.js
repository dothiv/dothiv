'use strict';

angular.module('dotHIVApp.controllers').controller('SubscriptionController', ['$rootScope', '$scope', 'security', 'config', 'dothivPremiumSubscription', '$http', 'VatRules',
    function ($rootScope, $scope, security, config, dothivPremiumSubscription, $http, VatRules) {
        $scope.block = 'pc.subscription.checking';
        $scope.domain = config.domain;
        $scope.subscription = {
            domain: config.domain,
            fullname: null,
            address1: null,
            address2: null,
            cycle_start: config.cycle_start,
            cycle_end: config.cycle_end,
            vat: config.vat.de,
            country: null
        };
        $scope.countries = [];
        $http.get('/bundles/dothivbasewebsite/data/countries-' + config.locale + '.json').success(function (data) {
            var countries = [];
            for (var i = 0; i < data.length; i++) {
                countries.push({"iso": data[i][0], "name": data[i][1], "eu": data[i][2]});
            }
            $scope.countries = countries;
        });

        dothivPremiumSubscription.get(
            {domain: config.domain},
            function () {
                $scope.block = 'pc.subscription.subscribed';
            },
            function () {
                $scope.block = 'pc.subscription.nosubscription';
            }
        );

        var handler = StripeCheckout.configure({
            key: config.stripe.publishableKey,
            image: config.stripe.logo,
            token: function (token) {
                $rootScope.$apply(function () {
                    $scope.block = 'pc.subscription.purchased';
                    $scope.subscription.token = token.id;
                    $scope.subscription.livemode = token.livemode;
                    dothivPremiumSubscription.create($scope.subscription);
                });
            },
            closed: function () {
            }
        });

        // VAT stuff
        /**
         * @returns {VatRules}
         */
        var createVatRules = function () {
            var country = $scope.country ? $scope.country : {"iso": "XX", "eu": false};
            return new VatRules(!!$scope.subscription.organization, country, !!$scope.subscription.vatNo);
        };

        $scope.vatNoRequired = function () {
            return createVatRules().vatNoRequired();
        };

        $scope.vatNoEnabled = function () {
            return createVatRules().vatNoEnabled();
        };

        $scope.showReverseChargeNote = function () {
            return createVatRules().showReverseChargeNote();
        };

        function totalIncludesTax() {
            return createVatRules().getVat() > 0;
        }
        $scope.totalIncludesTax = totalIncludesTax;

        function getAmount() {
            return totalIncludesTax() ? config.price.total : config.price.net;
        }

        $scope.checkout = function () {
            handler.open({
                name: config.strings.pc.stripe.checkout.name,
                description: config.strings.pc.stripe.checkout.description,
                amount: getAmount(),
                currency: 'EUR',
                email: security.user.email,
                panelLabel: config.strings.pc.stripe.checkout.button
            });
        };

        // Form stuff
        $scope.subscriptionStep = 'form';

        $scope.selectCountry = function(country) {
            $scope.country = country;
            $scope.subscription.country = country.iso;
        };

        $scope.allChecked = function () {
            var allChecked = true;
            for (var k in $scope.confirm) {
                if ($scope.confirm[k] != true) {
                    allChecked = false;
                }
            }
            return allChecked;
        }
    }
]);
