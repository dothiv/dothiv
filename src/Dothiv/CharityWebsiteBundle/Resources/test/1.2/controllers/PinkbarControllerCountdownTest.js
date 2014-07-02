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
        rootScope = $rootScope;
        controllerFactory = $controller;
    }));

    //The actual before each for setting up common variables, dependencies or functions
    beforeEach(function () {

    });

    //Actual test
    it('should calculate the countdown for less than n*24 hours', function () {
        mockConfig = {
            now: '2014-08-07T11:17:28+00:00',
            countdown_start: '2014-08-01T00:00:00+00:00',
            general_availability: '2014-08-10T11:17:27+00:00'
        };
        createController();
        expect(scope.countdown.diffDays).toBe(2);
        expect(scope.countdown.diffTime).toBe('23:59:59');
    });

    it('should calculate the countdown for exactly n*24 hours', function () {
        mockConfig = {
            now: '2014-08-07T11:17:27+00:00',
            countdown_start: '2014-08-01T00:00:00+00:00',
            general_availability: '2014-08-10T11:17:27+00:00'
        };
        createController();
        expect(scope.countdown.diffDays).toBe(3);
        expect(scope.countdown.diffTime).toBe('0:00:00');
    });

    it('should calculate the countdown for more than n*24 hours', function () {
        mockConfig = {
            now: '2014-08-07T11:17:26+00:00',
            countdown_start: '2014-08-01T00:00:00+00:00',
            general_availability: '2014-08-10T11:17:27+00:00'
        };
        createController();
        expect(scope.countdown.diffDays).toBe(3);
        expect(scope.countdown.diffTime).toBe('0:00:01');
    });

    it('should count to zero', function () {
        mockConfig = {
            now: '2014-08-10T11:17:27+00:00',
            countdown_start: '2014-08-01T00:00:00+00:00',
            general_availability: '2014-08-10T11:17:27+00:00'
        };
        createController();
        expect(scope.countdown.diffDays).toBe(0);
        expect(scope.countdown.diffTime).toBe('0:00:00');
    });

    it('should not over-count', function () {
        mockConfig = {
            now: '2014-08-11T11:17:27+00:00',
            countdown_start: '2014-08-01T00:00:00+00:00',
            general_availability: '2014-08-10T11:17:27+00:00'
        };
        createController();
        expect(scope.countdown.diffDays).toBe(0);
        expect(scope.countdown.diffTime).toBe('0:00:00');
    });
});
