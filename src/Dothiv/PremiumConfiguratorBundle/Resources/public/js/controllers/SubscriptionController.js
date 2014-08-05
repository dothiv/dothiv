'use strict';

angular.module('dotHIVApp.controllers').controller('SubscriptionController', ['$rootScope', '$scope', 'security', 'config', 'dothivPremiumSubscription',
    function ($rootScope, $scope, security, config, dothivPremiumSubscription) {
        $scope.block = 'pc.subscription.checking';
        $scope.domain = config.domain;

        $scope.subscription = dothivPremiumSubscription.get(
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

        $scope.checkout = function () {
            handler.open({
                name: config.strings.pc.stripe.checkout.name,
                description: config.strings.pc.stripe.checkout.description,
                amount: 1295,
                currency: 'EUR',
                email: security.user.email,
                panelLabel: config.strings.pc.stripe.checkout.button
            });
        }
    }
]);
