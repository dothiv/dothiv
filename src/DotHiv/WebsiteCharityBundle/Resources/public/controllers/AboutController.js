'use strict';

angular.module('dotHIVApp.controllers').controller('AboutController', ['$scope', '$state', '$location', '$anchorScroll', '$timeout',
    function($scope, $state, $location, $anchorScroll, $timeout) {
        // make current state information available
        $scope.state = $state;

        // TODO hardcoded menu structure -- eventually to be replaced with dynamic solution
        // can't use plain object since JS sorts object alphabetically
        $scope.menu = [
            { name: 'mission', menu: [ 'goals', 'contribution', 'AIDS' ] },
            { name: 'aboutHIV', menu: [ 'manifesto', 'concept', 'usage', 'moneyusage', 'history', 'quotes' ] },
            { name: 'fororg', menu: [ 'apply', 'guidelines', 'expertpool' ] },
            { name: 'whoisbehind', menu: [ 'team', 'partnersupporter', 'policyca', 'board', 'heavystuff' ] },
            { name: 'getactive', menu: [ 'jobs', 'help' ] },
            { name: 'newsstream', menu: [ 'socialmedia' ] },
            { name: 'registry', menu: [] },
        ];

        /**
         * scroll to a specific anchor on the current page.
         * @param {Object} id
         */
        $scope.scrollTo = function(id) {
            $location.hash(id);
            // scrolling needs to be executed /after/ the DOM was changed.
            $timeout(function() {
                $anchorScroll();
            });
         }

        // check hash and scroll to anchor
        var show = $location.hash();
        if (show) {
            $scope.scrollTo(show);
        }
        
        // suffle partner index array for the partner carousel
        // partner 1 is always shown
        ////+ Jonas Raoni Soares Silva
        ////@ http://jsfromhell.com/array/shuffle [v1.0]
        function shuffle(o){ //v1.0
            for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
            return o;
        };
        $scope.partners = [1].concat(shuffle([2,3,4,5]));
    }
]);
