describe('Controller: PinkbarControllerCountdown', function () {
    var controllerFactory, scope, rootScope, mockConfig;

    function createController() {
        return controllerFactory('PinkbarControllerCountdown', {
            $scope: scope,
            $rootScope: rootScope,
            config: mockConfig
        });
    }

    beforeEach(module('dotHIVApp'));

    beforeEach(inject(function ($controller, $rootScope) {
        scope = $rootScope.$new();
        rootScope = $rootScope.$new();
        controllerFactory = $controller;
    }));

    //The actual before each for setting up common variables, dependencies or functions
    beforeEach(function () {
        mockConfig = {
            general_availability: '2014-08-26T00:00:00+00:00',
            countdown_start: '2014-06-21T00:00:00+00:00'
        };
    });

    //Actual test
    it('controller when loaded should calculate countdown', function () {
        createController();
        // Fixme: Add now provider.
        expect(scope.countdown.diffDays).toBe(66);
    });
});
