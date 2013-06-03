'use strict';

/* jasmine specs for services go here */

describe('Security service', function() {
    var security, httpBackend;
    
    beforeEach(module('myApp.services'));
    beforeEach(module('ui.state'));
    beforeEach(function () {
        inject(function($injector) {
            security = $injector.get('security');
        });
        inject(function($httpBackend) {
            httpBackend = $httpBackend;
        });
    });

    it('should be instanciated for further testing', function() {
        expect(security).toBeDefined();
    });

    describe('isAuthenticated()', function() {
        it('should be false by default', function() {
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should still be false after calling updateIsAuthenticated() without logging in', function() {
            httpBackend.expectGET('/app_dev.php/api/login_state').respond(400);
            security.updateIsAuthenticated();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should be true after calling updateIsAuthenticated() while being logged in', function() {
            httpBackend.expectGET('/app_dev.php/api/login_state').respond(200);
            security.updateIsAuthenticated();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(true);
        });

        it('should be false after successfully logging out', function() {
            httpBackend.expectGET('/app_dev.php/logout').respond(200);
            security.logout();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should still be true after unsuccessfully trying to log out', function() {
            // make sure we are logged in
            httpBackend.expectGET('/app_dev.php/api/login_state').respond(200);
            security.updateIsAuthenticated();
            httpBackend.flush();

            // logging out unsuccessfully
            httpBackend.expectGET('/app_dev.php/logout').respond(400);
            security.logout(); 
            httpBackend.flush();

            expect(security.isAuthenticated()).toEqual(true);
        });
    });

    describe('login()', function() {
        it('should send a POST request (with correct header), containing username and password', function() {
            // check request for correct data and header
            httpBackend.expectPOST('/app_dev.php/login_check', '_username=testuser&_password=testpassword', function(headers) {
                return headers['Content-Type'] == 'application/x-www-form-urlencoded';
              }).respond(201);
            security.login('testuser', 'testpassword');
            httpBackend.flush();
        });

        it('should set isAuthenticated to true when successfully logged in', function() {
            expect(security.isAuthenticated()).toEqual(false);
            httpBackend.expectPOST('/app_dev.php/login_check').respond(201);
            security.login('testuser', 'testpassword');
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(true);
        });

        it('should not set isAuthenticated to true when unsuccessfully trying to log in', function() {
            expect(security.isAuthenticated()).toEqual(false);
            httpBackend.expectPOST('/app_dev.php/login_check').respond(400);
            security.login('testuser', 'testpassword');
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should call the callback function providing "true" when successfully logged in', function() {
            expect(security.isAuthenticated()).toEqual(false);
            httpBackend.expectPOST('/app_dev.php/login_check').respond(201);
            var cbstatus = false;
            security.login('testuser', 'testpassword', function(status) {
                cbstatus = status;
            });
            httpBackend.flush();
            expect(cbstatus).toEqual(true);
        });

        it('should call the callback function providing "false" and a message when unsuccessfully trying to log in', function() {
            expect(security.isAuthenticated()).toEqual(false);
            httpBackend.expectPOST('/app_dev.php/login_check').respond(400, 'test message');
            var cbstatus = true;
            var cbdata;
            security.login('testuser', 'testpassword', function(status, data) {
                cbstatus = status;
                cbdata = data;
            });
            httpBackend.flush();
            expect(cbstatus).toEqual(false);
            expect(cbdata).toEqual('test message');
        });
    });

    describe('logout()', function() {
        it('should set isAuthenticated to "false" when successfully logged out', function() {
            // make sure we are logged in
            httpBackend.expectGET('/app_dev.php/api/login_state').respond(200);
            security.updateIsAuthenticated();
            httpBackend.flush();

            httpBackend.expectGET('/app_dev.php/logout').respond(201);
            security.logout();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should call the callback function once when successfully logged out', function() {
            // make sure we are logged in
            httpBackend.expectGET('/app_dev.php/api/login_state').respond(200);
            security.updateIsAuthenticated();
            httpBackend.flush();

            // define a 'spy' callback function
            var spyCallback = jasmine.createSpy('callback');

            httpBackend.expectGET('/app_dev.php/logout').respond(201);
            security.logout(spyCallback);
            httpBackend.flush();
            expect(spyCallback).toHaveBeenCalled();
            expect(spyCallback.calls.length).toEqual(1);
        });

        it('should not set isAuthenticated to "false" when unsuccessfully trying to log out', function() {
            // make sure we are logged in
            httpBackend.expectGET('/app_dev.php/api/login_state').respond(200);
            security.updateIsAuthenticated();
            httpBackend.flush();

            httpBackend.expectGET('/app_dev.php/logout').respond(400);
            security.logout();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(true);
        });
    });

    describe('register()', function() {
        it('should send a POST request containing username, email address and password', function() {
            // check request for correct data and header
            httpBackend.expectPOST('/app_dev.php/api/users', '{"username":"testuser","email":"test@email.hiv","plainPassword":"testpassword"}').respond(201);
            security.register('testuser', 'test@email.hiv', 'testpassword');
            httpBackend.flush();
        });

        it('should call the callback function providing "true" when successfully registered', function() {
            httpBackend.expectPOST('/app_dev.php/api/users').respond(201);

            // define a 'spy' callback function
            var spyCallback = jasmine.createSpy('callback');

            security.register('testuser', 'test@email.hiv', 'testpassword', spyCallback);
            httpBackend.flush();

            expect(spyCallback).toHaveBeenCalledWith(true);
        });

        it('should call the callback function providing "false" when unsuccessfully trying to register', function() {
            httpBackend.expectPOST('/app_dev.php/api/users').respond(400, 'test message');

            // define a 'spy' callback function
            var spyCallback = jasmine.createSpy('callback');

            security.register('testuser', 'test@email.hiv', 'testpassword', spyCallback);
            httpBackend.flush();

            expect(spyCallback).toHaveBeenCalledWith(false, 'test message');
        });
    });

/*  describe('version', function() {
    it('should return current version', inject(function(version) {
      expect(version).toEqual('0.1');
    }));
  });*/
});
