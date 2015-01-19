'use strict';

angular.module('dotHIVApp.controllers').controller('OrderController', ['$rootScope', '$scope', '$http', 'security', 'config', 'dothivPayitforwardOrder', function ($rootScope, $scope, $http, security, config, dothivPayitforwardOrder) {
    $scope.user = false;
    $scope.step = 'form';
    $scope.order = {
        type: 'deorg',
         domain1: '',
         domain2: '',
         domain3: ''
    };

    security.updateUserInfo(function () {
        $scope.order.firstname = security.user.firstname;
        $scope.order.surname = security.user.surname;
        $scope.order.email = security.user.email;
        $scope.order.fullname = security.user.firstname + " " + security.user.surname;
    });

    $scope.countries = [];
    $http.get('/bundles/dothivbasewebsite/data/countries-en.json').success(function (data) {
        var countries = [];
        for(var i = 0; i < data.length; i++) {
            countries.push(data[i][0]);
        }
        $scope.countries = countries;
    });

    // Review

    $scope.numDomains = function () {
        var num = 0;
        for (var i = 1; i <= 3; i++) {
            if (typeof $scope.order["domain" + i] != "undefined" && $scope.order["domain" + i].length > 0) {
                num++;
            }
        }
        return num;
    };

    $scope.allChecked = function () {
        var allChecked = true;
        for (var k in $scope.confirm) {
            if ($scope.confirm[k] != true) {
                allChecked = false;
            }
        }
        return allChecked;
    };

    function getStripeAmount() {
        return totalIncludesTax() ? config.price.total[$scope.numDomains()] : config.price.net[$scope.numDomains()];
    }

    var handler = StripeCheckout.configure({
        key: config.stripe.publishableKey,
        image: config.stripe.logo,
        token: function (token) {
            $rootScope.$apply(function () {
                $scope.step = 'paid';
                $scope.order.token = token.id;
                $scope.order.livemode = token.livemode;
                dothivPayitforwardOrder.create($scope.order);
            });
        },
        closed: function () {
        }
    });

    $scope.checkout = function () {
        handler.open({
            name: 'Enter payment data',
            description: $scope.numDomains() + " payitforward voucher",
            amount: getStripeAmount(),
            currency: 'EUR',
            email: $scope.order.email
        });
    };

    // VAT stuff
    var orgTypes = {
        'noneu': {taxNoRequired: false, vatNoRequired: false, vat: false},
        'euorgnet': {taxNoRequired: false, vatNoRequired: true, vat: true},
        'euorg': {taxNoRequired: false, vatNoRequired: false, vat: true},
        'deorg': {taxNoRequired: true, vatNoRequired: true, vat: true},
        'euprivate': {taxNoRequired: false, vatNoRequired: false, vat: true}
    };
    $scope.vatNoRequired = function () {
        var vatNoRequired = orgTypes[$scope.order.type].vatNoRequired;
        if (vatNoRequired) {
            if ($scope.order.type == 'deorg') {
                if ($scope.order.taxNo) {
                    return false;
                }
            }
        }
        return vatNoRequired;
    };

    $scope.taxNoRequired = function () {
        var taxNoRequired = orgTypes[$scope.order.type].taxNoRequired;
        if (taxNoRequired && $scope.order.vatNo) {
            return false;
        }
        return taxNoRequired;
    };

    $scope.vatTaxNoRequired = function () {
        return $scope.vatNoRequired() || $scope.taxNoRequired();
    };

    function totalIncludesTax() {
        return orgTypes[$scope.order.type].vat;
    }

    $scope.totalIncludesTax = totalIncludesTax;

    // Twitter Text handling
    $scope.tweetText = '';
    function updateTweetText() {
        var tweetText = 'Coolest gift ever! Send a gift of your own to three of your friends and start a new digital movement for the end of AIDS.';
        if ($scope.order.domain && $scope.order.domainDonorTwitter) {
            tweetText = 'Coolest gift ever! We got ' + $scope.order.domain + ' from ' + $scope.order.domainDonorTwitter + '! ';
        }
        if ($scope.order.domain1Twitter || $scope.order.domain2Twitter || $scope.order.domain3Twitter) {
            tweetText += "It's our pleasure to payitforward.hiv for ";
            var domains = [];
            if ($scope.order.domain1Twitter) domains.push($scope.order.domain1Twitter);
            if ($scope.order.domain2Twitter) domains.push($scope.order.domain2Twitter);
            if ($scope.order.domain3Twitter) domains.push($scope.order.domain3Twitter);
            if (domains.length == 1) {
                tweetText += domains[0];
            } else if (domains.length == 2) {
                tweetText += domains.join(' & ');
            } else if (domains.length == 3) {
                tweetText += domains[0] + ', ' + domains[1] + ' & ' + domains[2];
            }
            tweetText += ".";
        }
        $scope.tweetText = tweetText;
    }

    // Facebook Text handling
    $scope.fbText = '';
    function updateFbText() {
        var fbText = 'Coolest gift ever! Send a gift of your own to three of your friends, business partners or clients. Together we start a new digital movement for the end of AIDS.';
        if ($scope.order.domain) {
            fbText = 'Coolest gift ever! We got ' + $scope.order.domain;
            if ($scope.order.domainDonor) {
                fbText += ' from ' + $scope.order.domainDonor;
            }
            fbText += '! Please accept and use your digital Red Ribbon. Send a gift of your own to three of your friends, business partners or clients. Together we start a new digital movement for the end of AIDS.';
        }
        $scope.fbText = fbText;
    }

    function updateSharingText() {
        updateTweetText();
        updateFbText();
    }

    $scope.updateSharingText = updateSharingText;
    $scope.updateFbText = updateFbText;
    $scope.updateTweetText = updateTweetText;
}]);
