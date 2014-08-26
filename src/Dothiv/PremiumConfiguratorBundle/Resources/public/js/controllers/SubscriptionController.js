'use strict';

angular.module('dotHIVApp.controllers').controller('SubscriptionController', ['$rootScope', '$scope', 'security', 'config', 'dothivPremiumSubscription', '$http',
    function ($rootScope, $scope, security, config, dothivPremiumSubscription, $http) {
        $scope.block = 'pc.subscription.checking';
        $scope.domain = config.domain;
        $scope.subscription = {
            type: null,
            name: null,
            address1: null,
            address2: null,
            taxNo: null,
            vatNo: null,
            cycle_start: config.cycle_start,
            cycle_end: config.cycle_end,
            vat: config.vat.de,
            country: null
        };
        $scope.countries = [];
        $http.get('/bundles/dothivbasewebsite/data/countries.json').success(function (data) {
            $scope.countries = data;
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
                    dothivPremiumSubscription.create({
                        domain: config.domain,
                        token: token.id,
                        livemode: token.livemode
                    });
                });
            },
            closed: function () {
            }
        });

        function totalIncludesTax() {
            return $scope.subscription.type == 'euorgnet'
                || $scope.subscription.type == 'euorg'
                || $scope.subscription.type == 'deorg'
                || $scope.subscription.type == 'euprivate';
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
        $scope.resetSubscriptionForm = function () {
            $scope.subscription.type = null;
        };

        $scope.vatNoRequired = function () {
            var vatNoRequired = $scope.subscription.type == 'euorgnet'
                || $scope.subscription.type == 'deorg';
            if (vatNoRequired) {
                if ($scope.subscription.type == 'deorg') {
                    if ($scope.subscription.taxNo) {
                        return false;
                    }
                }
            }
            return vatNoRequired;
        };

        $scope.taxNoRequired = function () {
            var taxNoRequired = $scope.subscription.type == 'deorg';
            if (taxNoRequired && $scope.subscription.vatNo) {
                return false;
            }
            return taxNoRequired;
        };

        $scope.vatTaxNoRequired = function () {
            return $scope.vatNoRequired() || $scope.taxNoRequired();
        };

        $scope.totalIncludesTax = function () {
            return $scope.subscription.type == 'euorgnet'
                || $scope.subscription.type == 'euorg'
                || $scope.subscription.type == 'deorg'
                || $scope.subscription.type == 'euprivate';
        }

        // Country selection limits

        function getCountryLabel(search) {
            for (var i = 0; i < $scope.countries.length; i++) {
                if ($scope.countries[i].search(search) >= 0) {
                    return $scope.countries[i];
                }
            }
        }

        var country_de = 'Germany';
        var eu_countries_without_de = [
            'Belgium',
            'Bulgaria',
            'Denmark',
            'Estonia',
            'Finland',
            'France',
            'Greece',
            'United Kingdom',
            'Ireland',
            'Italy',
            'Croatia',
            'Lettland',
            'Latvia',
            'Luxemburg',
            'Malta',
            'Netherlands',
            'Austria',
            'Poland',
            'Portugal',
            'Rumania',
            'Sweden',
            'Slovakia',
            'Slovenia',
            'Spain',
            'Czechia',
            'Hungary',
            'Cyprus'
        ];
        $scope.$watch('subscription.type', function (type) {
            if (type == 'deorg') {
                $scope.subscription.country = getCountryLabel(country_de);
            }
        });

        $scope.filterCountries = function () {
            if ($scope.subscription.type == 'deorg') {
                return [getCountryLabel(country_de)];
            }
            if ($scope.subscription.type.substr(0, 2) == 'eu') {
                return eu_countries_without_de.map(getCountryLabel);
            }
            var euCountries = eu_countries_without_de.map(getCountryLabel);
            euCountries.push(getCountryLabel(country_de));
            return $scope.countries.filter(function (c) {
                return euCountries.indexOf(c) > -1
            }).map(getCountryLabel);
        }

        $scope.countryEditDisabled = function () {
            return $scope.subscription.type == 'deorg';
        }

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
