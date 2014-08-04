'use strict';

angular.module('dotHIVApp.controllers').controller('SubscriptionController', ['$scope',
    function ($scope) {
        $scope.subscriptionRequired = true;
        $scope.block = 'pc.subscription.nosubscription';
    }
]);
