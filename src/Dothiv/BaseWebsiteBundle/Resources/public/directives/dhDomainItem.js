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
                            '<a href="" class="pull-right" ng-click="edit(domain.id)">Domain bearbeiten<div class="item-arrow"></div></a>' +
                            '<div class="pull-left item-icon"></div>' +
                            '<span><span ng-bind="domain.clickcount"></span> Hits&nbsp;&nbsp;|&nbsp;&nbsp;Standard-Banner</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>',
        controller: function($scope, $state) {
            $scope.edit = function() {
                $state.transitionTo('=.profile.domain.editors', { domainId: $scope.domain.id });
            };
        }
    };
});
