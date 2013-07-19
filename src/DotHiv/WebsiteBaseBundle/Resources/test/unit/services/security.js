'use strict';

describe('Security service', function() {
    var security, httpBackend, templateCache, rootScope, state;

    beforeEach(module('dotHIVApp.services'));
    beforeEach(module('ui.state'));
    beforeEach(module(function($provide) {
            $provide.factory('$state', function() {
                return {
                    transitionTo: function(state) {
                        this.current = state;
                    },
                    includes: function(state) {
                        return !!this.current.match(new RegExp('(^|\.)' + state + '($|\.)'));
                    },
                    current: 'the_default_state',
                };
            });
        })
    );
    beforeEach(function () {
        inject(function($injector) {
            security = $injector.get('security');
        });
        inject(function($httpBackend) {
            httpBackend = $httpBackend;
        });
        inject(function($templateCache) {
            templateCache = $templateCache;
        });
        inject(function($rootScope) {
            rootScope = $rootScope;
        });
        inject(function($state) {
            state = $state;
        })
        state.transitionTo('home');
    });

    it('should be instanciated for further testing', function() {
        expect(security).toBeDefined();
    });

    describe('isAuthenticated()', function() {
        it('should be false by default', function() {
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should still be false after calling updateUserInfo() without logging in', function() {
            httpBackend.expectGET(/^.*api\/login$/).respond(400);
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should be true after calling updateUserInfo() while being logged in', function() {
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(true);
        });

        it('should be false after successfully logging out', function() {
            httpBackend.expectDELETE(/^.*api\/login$/).respond(200);
            security.logout();
            rootScope.$digest();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should still be true after unsuccessfully trying to log out', function() {
            // make sure we are logged in
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();

            // logging out unsuccessfully
            httpBackend.expectDELETE(/^.*api\/login$/).respond(400);
            security.logout(); 
            rootScope.$digest();
            httpBackend.flush();

            expect(security.isAuthenticated()).toEqual(true);
        });
    });

    describe('login()', function() {
        it('should send a POST request (with correct header), containing username and password', function() {
            // check request for correct data and header
            httpBackend.expectPOST(/^.*api\/login$/, '{"username":"testuser","password":"testpassword"}').respond(201, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.login('testuser', 'testpassword');
            rootScope.$digest();
            httpBackend.flush();
        });

        it('should set isAuthenticated to true when successfully logged in', function() {
            expect(security.isAuthenticated()).toEqual(false);
            httpBackend.expectPOST(/^.*api\/login$/).respond(201, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.login('testuser', 'testpassword');
            rootScope.$digest();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(true);
        });

        it('should not set isAuthenticated to true when unsuccessfully trying to log in', function() {
            expect(security.isAuthenticated()).toEqual(false);
            httpBackend.expectPOST(/^.*api\/login$/).respond(400);
            security.login('testuser', 'testpassword');
            rootScope.$digest();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should call the callback function providing "true" when successfully logged in', function() {
            expect(security.isAuthenticated()).toEqual(false);
            httpBackend.expectPOST(/^.*api\/login$/).respond(201, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            var cbstatus = false;
            security.login('testuser', 'testpassword', function(status) {
                cbstatus = status;
            });
            rootScope.$digest();
            httpBackend.flush();
            expect(cbstatus).toEqual(true);
        });

        it('should call the callback function providing "false" and a message when unsuccessfully trying to log in', function() {
            expect(security.isAuthenticated()).toEqual(false);
            httpBackend.expectPOST(/^.*api\/login$/).respond(400, 'test message');
            var cbstatus = true;
            var cbdata;
            security.login('testuser', 'testpassword', function(status, data) {
                cbstatus = status;
                cbdata = data;
            });
            rootScope.$digest();
            httpBackend.flush();
            expect(cbstatus).toEqual(false);
            expect(cbdata.data).toEqual('test message');
        });
    });

    describe('logout()', function() {
        it('should set isAuthenticated to "false" when successfully logged out', function() {
            // make sure we are logged in
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();

            httpBackend.expectDELETE(/^.*api\/login$/).respond(200);
            security.logout();
            rootScope.$digest();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(false);
        });

        it('should call the callback function once when successfully logged out', function() {
            // make sure we are logged in
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();

            // define a 'spy' callback function
            var spyCallback = jasmine.createSpy('callback');

            httpBackend.expectDELETE(/^.*api\/login$/).respond(200);
            security.logout(spyCallback);
            rootScope.$digest();
            httpBackend.flush();
            expect(spyCallback).toHaveBeenCalled();
            expect(spyCallback.calls.length).toEqual(1);
        });

        it('should not clear the template cache when successfully logged out', function() {
            var sizeBefore = templateCache.info().size;
            // make sure we are logged in
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();

            httpBackend.expectDELETE(/^.*api\/login$/).respond(200);
            security.logout();
            rootScope.$digest();
            httpBackend.flush();
            expect(templateCache.info().size).toEqual(sizeBefore);
        });

        it('should not set isAuthenticated to "false" when unsuccessfully trying to log out', function() {
            // make sure we are logged in
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();

            httpBackend.expectDELETE(/^.*api\/login$/).respond(400);
            security.logout();
            rootScope.$digest();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(true);
        });

        it('should redirect to home state when logging out from a protected (=) state', function() {
            // make sure we are logged in
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();

            // make sure we are in a protected state
            state.transitionTo('=');

            httpBackend.expectDELETE(/^.*api\/login$/).respond(200);
            security.logout();
            spyOn(state, 'transitionTo').andCallThrough();
            rootScope.$digest();
            httpBackend.flush();
            expect(state.current).toEqual('home');
            expect(state.transitionTo).toHaveBeenCalledWith('home');
        });

        it('should not redirect to home state when logging out from a not protected state', function() {
            // make sure we are logged in
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();

            // make sure we are in a non-protected state
            state.transitionTo('somePublicPage');

            httpBackend.expectDELETE(/^.*api\/login$/).respond(200);
            spyOn(state, 'transitionTo');
            security.logout();
            rootScope.$digest();
            httpBackend.flush();
            expect(state.current).toEqual('somePublicPage');
            expect(state.transitionTo).not.toHaveBeenCalled();
        });
    });

    describe('user attribute', function() {
        it('should be empty by default', function() {
            expect('username' in security.state.user).toEqual(false);
        });

        it('should be populated after login', function() {
            expect(security.isAuthenticated()).toEqual(false);
            // make sure we are logged in
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(true);

            expect('username' in security.state.user).toEqual(true);
            expect(security.state.user.username).toEqual('testuser');
            expect(security.state.user.email).toEqual('test@email.hiv');
        });

        it('should be cleared after logout', function() {
            expect(security.isAuthenticated()).toEqual(false);
            // make sure we are logged in
            httpBackend.expectGET(/^.*api\/login$/).respond(200, '{\
                    "username": "testuser",\
                    "username_canonical": "testuser",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');
            security.updateUserInfo();
            rootScope.$digest();
            httpBackend.flush();
            expect(security.isAuthenticated()).toEqual(true);

            httpBackend.expectDELETE(/^.*api\/login$/).respond(200);
            security.logout();
            rootScope.$digest();
            httpBackend.flush();

            expect('username' in security.state.user).toEqual(false);
        });
    });

    describe('register()', function() {
        it('should send a POST request containing username, email address and password', function() {
            // check request for correct data and header
            httpBackend.expectPOST(/^.*api\/users$/, '{"email":"test@email.hiv","plainPassword":"testpassword","name":"testname","surname":"testsurname"}').respond(400);
            security.register('testname', 'testsurname', 'test@email.hiv', 'testpassword');
            rootScope.$digest();
            httpBackend.flush();
        });

        it('should login the new user and call the callback function providing "true" when successfully registered', function() {
            httpBackend.expectPOST(/^.*api\/users$/).respond(201);

            httpBackend.expectPOST(/^.*api\/login$/).respond(201, '{\
                    "username": "test@email.hiv",\
                    "username_canonical": "test@email.hiv",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');

            // define a 'spy' callback function
            var spyCallback = jasmine.createSpy('callback');

            security.register('testname', 'testsurname', 'test@email.hiv', 'testpassword', spyCallback);
            rootScope.$digest();
            httpBackend.flush();

            expect(spyCallback).toHaveBeenCalledWith(true);
        });

        it('should call the callback function providing "false" when unsuccessfully trying to register', function() {
            httpBackend.expectPOST(/^.*api\/users$/).respond(400, 'test message');

            // define a 'spy' callback function
            var spyCallback = jasmine.createSpy('callback');

            security.register('testname', 'testsurname', 'test@email.hiv', 'testpassword', spyCallback);
            rootScope.$digest();
            httpBackend.flush();

            expect(spyCallback).toHaveBeenCalledWith(false, jasmine.any(Object));
        });
    });

    describe('schedule()', function() {
        it('should call the scheduled callbacks after login', function() {
            httpBackend.expectPOST(/^.*api\/login$/).respond(201, '{\
                    "username": "test@email.hiv",\
                    "username_canonical": "test@email.hiv",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');

            security.login('testuser', 'testpassword');
            var spyCallbackA = jasmine.createSpy('callbackA');
            var spyCallbackB = jasmine.createSpy('callbackB');
            security.schedule(spyCallbackA);
            security.schedule(spyCallbackB);
            rootScope.$digest();
            httpBackend.flush();
            expect(spyCallbackA).toHaveBeenCalled();
            expect(spyCallbackB).toHaveBeenCalled();
        });

        it('should call the scheduled callback immediately if no update is pending', function() {
            var spyCallbackA = jasmine.createSpy('callbackA');
            security.schedule(spyCallbackA);
            expect(spyCallbackA).toHaveBeenCalled();
        });

        it('should not crash when schedule is called with no arguments', function() {
            httpBackend.expectPOST(/^.*api\/login$/).respond(201, '{\
                    "username": "test@email.hiv",\
                    "username_canonical": "test@email.hiv",\
                    "email": "test@email.hiv",\
                    "email_canonical": "test@email.hiv",\
                    "last_login": "2013-06-05T17:26:29+0200",\
                    "roles": ["ROLE_USER"]\
                }');

            security.login('testuser', 'testpassword');
            security.schedule();
            rootScope.$digest();
            httpBackend.flush();

            security.schedule();
        })
    })
});
