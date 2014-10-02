'use strict';

angular.module('dotHIVApp.directives').directive('dhProjectItem', function() {
    return {
        restrict: 'E',
        scope: {
            project: '=project'
        },
        replace: true,
        template:
            '<div class="row">' +
                '<div class="span12 profile-summary-item">' +
                    '<div class="profile-summary-item-well">' +
                        '<span class="pull-right"><rating value="3" max="5" readonly="true" class="project-rating"></rating></span>' +
                        '<h3 ng-bind="project.name"></h3>' +
                        '<div class="profile-summary-item-lower">' +
                            '<a class="pull-right" href="" ng-click="edit()">Projekt bearbeiten<div class="item-arrow"></div></a>' +
                            '<div class="pull-left item-icon"></div>' +
                            '<span><span ng-bind="project.votes"></span> Votes&nbsp;&nbsp;|&nbsp;&nbsp;Platz 21&nbsp;&nbsp;|&nbsp;&nbsp;15 Kommentare&nbsp;&nbsp;|&nbsp;&nbsp;Projekt läuft</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>',
        controller: function($scope) {
            $scope.edit = function() {
                console.log("not yet implemented.");
            };
        }
    };
});
