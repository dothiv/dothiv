'use strict';

angular.module('dotHIVApp.directives').directive('dhDomainItem', function() {
    return {
        restrict: 'E',
        scope: {
            domain: '=domain'
        },
        replace: true,
        template:
            '<div class="row">' +
                '<div class="span12 profile-summary-item">' +
                    '<div class="profile-summary-item-well">' +
                        '<h3 ng-bind="domain.name"></h3>' +
                        '<div class="profile-summary-item-lower">' +
                            '<button class="btn-link pull-right" ng-click="edit(domain.id)">Domain bearbeiten<div class="item-arrow"></div></button>' +
                            '<div class="pull-left item-icon"></div>' +
                            '<span>223.342 Hits&nbsp;&nbsp;|&nbsp;&nbsp;Standard-Banner</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>',
        controller: function($scope, $state) {
            $scope.edit = function() {
                $state.transitionTo('=.profile.domainedit', { domainId: $scope.domain.id });
            };
        }
    };
});
