'use strict';

angular.module('dotHIVApp.controllers').controller('LookupFormController', ['$scope', '$state', '$stateParams', 'Price', function ($scope, $state, $stateParams, Price) {
    $scope.secondLevelName = "";
    $scope.secondLevelName4life = "";
    $scope.domain = "";
    var currency = $stateParams.locale === 'de' ? 'eur' : 'usd';
    $scope.price = Price.getFormattedPricePerYear($scope.domain, currency);
    $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain, currency);
    $scope.promoPrice = Price.getFormattedPricePerYear('name4life.hiv', currency);
    $scope.promoPricePerMonth = Price.getFormattedPricePerMonth('name4life.hiv', currency);

    var secondLevelNameWatcher = function (secondLevelName, oldSecondLevelName) {
        if (secondLevelName === oldSecondLevelName) {
            return;
        }
        if (typeof secondLevelName == "undefined") {
            $scope.domain = "";
        } else {
            $scope.domain = secondLevelName.toLowerCase() + ".hiv";
            $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain, currency);
        }
    };

    $scope.$watch('secondLevelName', secondLevelNameWatcher);
    $scope.$watch('secondLevelName4life', secondLevelNameWatcher);


    $scope.lookupDomain = function (domain) {
        $state.transitionTo('lookup', {"locale": $stateParams.locale, "domain": domain.toLowerCase()});
    };

    if (typeof $ !== "undefined") {
        $('#secondLevelName4life').focus();
    }
}]);
