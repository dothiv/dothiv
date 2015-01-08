'use strict';

angular.module('dotHIVApp.controllers').controller('StepController', ['$scope', 'OrderModel', '$state', '$stateParams', function ($scope, OrderModel, $state, $stateParams) {

    $scope.gotoStep = function (step) {
        switch (step) {
            case 1:
                $state.transitionTo('lookupform', {"locale": $stateParams.locale});
                return;
            case 2:
                if (OrderModel.getDomain().search('4life.hiv') > 0) {
                    $state.transitionTo('configure4life', {
                        "locale": $stateParams.locale,
                        "domain": OrderModel.getDomain()
                    });
                } else {
                    $state.transitionTo('configure', {"locale": $stateParams.locale, "domain": OrderModel.getDomain()});
                }
                return;
        }
    };

    $scope.stepEnabled = function (step) {
        if (OrderModel.isDone()) {
            return false;
        }
        return OrderModel.step >= step;
    };

}]);
