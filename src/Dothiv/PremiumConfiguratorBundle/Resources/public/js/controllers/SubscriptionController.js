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
            vat: config.vat.de
        };
        $scope.countries = $http.get('/bundles/dothivbasewebsite/data/countries.json');


        $scope.block = 'pc.subscription.nosubscription';

        /*
         $scope.subscription = dothivPremiumSubscription.get(
         {domain: config.domain},
         function () {
         $scope.block = 'pc.subscription.subscribed';
         },
         function () {
         $scope.block = 'pc.subscription.nosubscription';
         }
         );
         */

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

        $scope.checkout = function () {
            handler.open({
                name: config.strings.pc.stripe.checkout.name,
                description: config.strings.pc.stripe.checkout.description,
                amount: 1295,
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
            console.log($scope.subscription);
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
        }

        $scope.totalIncludesTax = function () {
            return $scope.subscription.type == 'euorgnet'
                || $scope.subscription.type == 'euorg'
                || $scope.subscription.type == 'deorg'
                || $scope.subscription.type == 'euprivate';
        }

        $scope.$watch('$scope.subscription.type', function (type) {
            if ($scope.subscription.type == 'deorg') {
                $scope.subscription.country = 'Deutschland';
            }
        });
    }
]);
