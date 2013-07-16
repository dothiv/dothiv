'use strict';

angular.module('dotHIVApp.controllers').controller('HomeController', ['$scope', 
    function($scope) {
    
        $scope.projectOffset = 0 ;
        $scope.incrementProjectOffset = function() {
            $scope.projectOffset++;
            if ($scope.projectOffset == $scope.projects.length) {
                $scope.projectOffset = 0;
            }
        }
        $scope.decrementProjectOffset = function() {
            $scope.projectOffset--;
            if ($scope.projectOffset < 0) {
                $scope.projectOffset = $scope.projects.length-1;
            }
        }
    
        $scope.projects = [
                           {
                               'id': 1,
                               'headline': 'Projekt 1',
                               'votes': 123,
                               'place': 4,
                               'pictureurl': '/bundles/websitecharity/images/projects/schoolclass.jpg'
                               
                           },
                           {
                               'id': 2,
                               'headline': 'Kinder und AIDS, Hilfe für HIV-betroffene Familien bla bla blablabla bla bla blablabla bla bla blablabla',
                               'votes': 124,
                               'place': 3,
                               'pictureurl': '/bundles/websitecharity/images/projects/papermen.jpg'
                               
                           },
                           {
                               'id': 3,
                               'headline': 'Projekt 3',
                               'votes': 127,
                               'place': 2,
                               'pictureurl': '/bundles/websitecharity/images/projects/trees.jpg'
                               
                           },
                           {
                               'id': 4,
                               'headline': 'Projekt 4',
                               'votes': 12123,
                               'place': 1,
                               'pictureurl': '/bundles/websitecharity/images/projects/trees.jpg'
                               
                           }
                       ]
    
        $scope.firmOffset = 0 ;
        $scope.incrementFirmOffset = function() {
            $scope.firmOffset++;
            if ($scope.firmOffset == $scope.firms.length) {
                $scope.firmOffset = 0;
            }
        }
        $scope.decrementFirmOffset = function() {
            $scope.firmOffset--;
            if ($scope.firmOffset < 0) {
                $scope.firmOffset = $scope.firms.length-1;
            }
        }
        $scope.firms = [
                        { 
                                'img': '/bundles/websitecharity/images/firms/github.jpg',
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/websitecharity/images/firms/liga01.jpg', 
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/websitecharity/images/firms/jovoto.jpg', 
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/websitecharity/images/firms/swipe.jpg', 
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/websitecharity/images/firms/github.jpg', 
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/websitecharity/images/firms/liga01.jpg', 
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/websitecharity/images/firms/jovoto.jpg', 
                                'url': '#!/'
                        },
                        { 
                                'img': '/bundles/websitecharity/images/firms/swipe.jpg', 
                                'url': '#!/'
                        }
                    ]
        $scope.quoteOffset = 0 ;
        $scope.incrementQuoteOffset = function() {
            $scope.quoteOffset++;
            if ($scope.quoteOffset == $scope.quotes.length) {
                $scope.quoteOffset = 0;
            }
        }
        $scope.decrementQuoteOffset = function() {
            $scope.quoteOffset--;
            if ($scope.quoteOffset < 0) {
                $scope.quoteOffset = $scope.quotes.length-1;
            }
        }
        $scope.quotes = [
                         {
                             'text': 'Wenn man einen Filter hat, sieht jedes Problem aus wie ein Hammer.',
                             'person': 'Komplize N'
                         },
                         {
                             'text': 'Das ist ja geil!',
                             'person': 'Komplize A'
                         },
                         {
                             'text': 'Awesome!',
                             'person': 'Komplize B'
                         },
                         {
                             'text': 'Erstma schön nen pull-Request machen...',
                             'person': 'Komplize M'
                         }
                     ]
                
                
    
    }
]);
