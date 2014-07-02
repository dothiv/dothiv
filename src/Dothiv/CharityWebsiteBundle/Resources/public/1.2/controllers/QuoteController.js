'use strict';

angular.module('dotHIVApp.controllers').controller('QuoteController', ['$scope', '$http', '$sce', 'config',
    function ($scope, $http, $sce, config) {

        $scope.visible = false;

        $scope.quoteOffset = 0;

        $scope.quotes = [];

        var shuffle = function (o) {
            for (var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
            return o;
        };

        function success(data) {
            var qs = [];
            for (var i = 0; i < data.length; i++) {
                var q = data[i];
                q.quoteLength = q.quote.length;
                q.quote = $sce.trustAsHtml(q.quote);
                q.description = $sce.trustAsHtml(q.description);
                qs[i] = q;
            }
            $scope.quotes = shuffle(qs);
            $scope.visible = true;
        }

        $http({method: 'GET', url: '/' + config.locale + '/content/Quote?markdown=quote,description'}).success(success);

    }
]);
