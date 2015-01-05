'use strict';

angular.module('dotHIVApp.controllers').controller('LookupFormController', ['$scope', '$state', '$stateParams', 'Price', function ($scope, $state, $stateParams, Price) {
    $scope.secondLevelName = "";
    $scope.domain = "";
    $scope.price = Price.getFormattedPricePerYear($scope.domain);
    $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain);
    $scope.promoPrice = Price.getFormattedPricePerYear('name4life.hiv');
    $scope.promoPricePerMonth = Price.getFormattedPricePerMonth('name4life.hiv');


    $scope.$watch('secondLevelName', function (domain) {
        if (typeof domain == "undefined") {
            $scope.domain = "";
        } else {
            $scope.domain = domain + ".hiv";
            $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain);
        }
    });

    $scope.lookupDomain = function () {
        $state.transitionTo('lookup', {"locale": $stateParams.locale, "domain": $scope.domain});
    };

    $('#secondLevelName').focus();
}]);
