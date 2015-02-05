angular.module('dotHIVApp.services').factory('HttpLoadingInterceptor', ['$q', '$rootScope',
    function ($q, $rootScope) {
        $rootScope.httpPendingRequests = 0;
        $rootScope.httpOn = false;

        $rootScope.toggleLoader = function () {
            if ($rootScope.httpPendingRequests > 0 && !$rootScope.httpOn) {
                $rootScope.httpOn = true;
                $rootScope.$emit('http.on');

            } else {
                $rootScope.httpOn = false;
                $rootScope.$emit('http.off');
            }
        };

        return {
            request: function (config) {
                $rootScope.httpPendingRequests += 1;
                $rootScope.toggleLoader();
                return config;
            },
            requestError: function (rejection) {
                $rootScope.httpPendingRequests -= 1;
                $rootScope.toggleLoader();
                return $q.reject(rejection);
            },
            response: function (response) {
                $rootScope.httpPendingRequests -= 1;
                $rootScope.toggleLoader();
                return response;
            },
            responseError: function (rejection) {
                $rootScope.httpPendingRequests -= 1;
                $rootScope.toggleLoader();
                return $q.reject(rejection);
            }
        }
    }
]);
