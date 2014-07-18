'use strict';

angular.module('dotHIVApp.controllers').controller('PaymentController', ['$scope',
    function ($scope) {
        $scope.paymentRequired = false;
        $scope.block = 'pc.payment.nosubscription';
    }
]);
