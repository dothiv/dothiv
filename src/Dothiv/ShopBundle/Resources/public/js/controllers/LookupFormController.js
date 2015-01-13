'use strict';

angular.module('dotHIVApp.controllers').controller('LookupFormController', ['$scope', '$state', '$stateParams', 'Price', function ($scope, $state, $stateParams, Price) {
    $scope.secondLevelName = "";
    $scope.domain = "";
    $scope.price = Price.getFormattedPricePerYear($scope.domain);
    $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain);
    $scope.promoPrice = Price.getFormattedPricePerYear('name4life.hiv');
    $scope.promoPricePerMonth = Price.getFormattedPricePerMonth('name4life.hiv');

    $scope.$watch('secondLevelName', function (secondLevelName, oldSecondLevelName) {
        if (secondLevelName === oldSecondLevelName) {
            return;
        }
        if (typeof secondLevelName == "undefined") {
            $scope.domain = "";
        } else {
            $scope.domain = secondLevelName.toLowerCase() + ".hiv";
            $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain);
        }
    });

    $scope.lookupDomain = function (domain) {
        $state.transitionTo('lookup', {"locale": $stateParams.locale, "domain": domain.toLowerCase()});
    };

    if (typeof $ !== "undefined") {
        $('#secondLevelName').focus();
    }
}]);
