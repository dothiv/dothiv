'use strict';

angular.module('dotHIVApp.controllers').controller('LookupResultController', ['$scope', '$state', '$stateParams', 'Price', function ($scope, $state, $stateParams, Price) {
    $scope.lookup = null;
    $scope.domain = $stateParams.domain;
    $scope.price = Price.getFormattedPricePerYear($scope.domain);
    $scope.pricePerMonth = Price.getFormattedPricePerMonth($scope.domain);

    // Fixme: Implement
    var lookupDomain = function (domain) {
        if (domain == "click.hiv") {
            $scope.lookup = "premium";
        } else if (domain == "cto.hiv") {
            $scope.lookup = "registered";
            $scope.alternatives = [
                'cto4life.hiv',
                'ctosupports.hiv',
                'ctofightsaids.hiv',
                'ctofights.hiv',
                'clickcto.hiv'
            ];
        } else {
            $scope.lookup = "available";
        }
    };

    // Init
    lookupDomain($stateParams.domain);
}]);
