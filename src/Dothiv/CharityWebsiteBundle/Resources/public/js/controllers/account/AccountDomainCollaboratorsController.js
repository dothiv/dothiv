'use strict';

angular.module('dotHIVApp.controllers').controller('AccountDomainCollaboratorsController', ['$scope', '$location', '$stateParams', '$http', '$window',
    function ($scope, $location, $stateParams, $http, $window) {
        // retrieve domain id from URL parameters and get domain/banner information
        $scope.domain = $stateParams.name;

        $scope.collaborators = [];

        // Load collaborators
        var defaultUrl = '/api/domain/' + $scope.domain + '/collaborator';
        var nextUrl;

        var load = function (url) {
            $http.get(url)
                .success(function (data) {
                    if (data.items) {
                        for (var i = 0; i < data.items.length; i++) {
                            $scope.collaborators.push(data.items[i]);
                        }
                    }
                    nextUrl = data.nextPageUrl;
                    $window.setTimeout(function () {
                        loadMore();
                    }, 1);
                })
            ;
        };

        var loadMore = function () {
            if (nextUrl) {
                load(nextUrl);
            }
        };

        load(defaultUrl);

        // Add a new
        $scope.addCollaborator = function () {
            var data = {
                email: $scope.collaborator_email,
                firstname: $scope.collaborator_firstname,
                lastname: $scope.collaborator_lastname
            };
            $http({
                method: 'POST',
                url: '/api/domain/' + $scope.domain + '/collaborator',
                data: angular.toJson(data),
                headers: {'Accept-Language': $scope.locale}
            })
                .success(function (data, status, headers, config) {
                    $scope.collaborator_email = null;
                    $scope.collaborator_firstname = null;
                    $scope.collaborator_lastname = null;
                    $scope.collaborators = [];
                    $scope.collaboratorForm.$setPristine();
                    load(defaultUrl);
                })
                .error(function (data, status, headers, config) {
                })
            ;
        };

        // Remove
        $scope.remove = function (collaborator) {
            var index = $scope.collaborators.indexOf(collaborator);
            if (index > -1) {
                $scope.collaborators.splice(index, 1);
            }
            $http({
                method: 'DELETE',
                url: collaborator['@id']
            });
        }
    }
]);
