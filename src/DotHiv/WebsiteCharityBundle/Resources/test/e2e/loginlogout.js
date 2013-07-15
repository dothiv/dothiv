'use strict';

/* http://docs.angularjs.org/guide/dev_guide.e2e-testing */

describe('dotHIVApp', function() {

  describe('login, logout', function() {

    beforeEach(function() {
        browser().navigateTo('/');
        sleep(1);
    });

    var email;

    it('should show no login dialog at startup', function() {
        expect(element('form[name="loginForm"]:visible').count()).toBe(0);
    });

    it('should show a login button after logging out', function() {
        element('a[ng-click="logout()"]').click();
        expect(element('a[ng-click="login()"]:visible').count()).toBeGreaterThan(0);
    });

    it('should not show a logout button', function() {
        expect(element('a[ng-click="logout()"]:visible').count()).toBe(0);
    });

    it('should show a login dialog exactly once after clicking on login', function() {
        element('a[ng-click="login()"]').click();
        expect(element('form[name="loginForm"]:visible').count()).toBe(1);
    });

    it('should show the login dialog exactly once when navigating to a protected page', function() {
        browser().navigateTo('#!/profile');
        sleep(1);
        expect(element('form[name="loginForm"]:visible').count()).toBe(1);
    });

    it('should navigate back to the homepage when starting the app on a protected page and aborting the login', function() {
        browser().navigateTo('/#!/profile');
        sleep(1);
        element('.modal-backdrop').click();
        sleep(1);
        expect(element('form[name="loginForm"]:visible').count()).toBe(0);
        expect(browser().location().url()).toBe('/');
    });

    it('should navigate back to the homepage when navigating on a protected page and aborting the login', function() {
        browser().navigateTo('#!/profile');
        sleep(1);
        element('.modal-backdrop').click();
        sleep(1);
        expect(element('form[name="loginForm"]:visible').count()).toBe(0);
        expect(browser().location().url()).toBe('/');
    });

    it('should log in after successful registration', function() {
        element('a[ng-click="login()"]').click();
        dhInput('input[name="registration-name"]').enter('Foo');
        dhInput('input[name="registration-surname"]').enter('Bar');
        dhInput('input[name="registration-email"]').enter(email = ('e2etest-' + Math.random().toString(36).substring(7) + '@stop.hiv'));
        dhInput('input[name="registration-password"]').enter('test123');
        dhInput('input[name="registration-passwordrepeat"]').enter('test123');
        element('form[name="registrationForm"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="logout()"]:visible').count()).toBeGreaterThan(0);
    });

    it('should not show a login dialog when navigating to a protected page while logged in', function() {
        // navigate with ajax
       browser().navigateTo('#!/profile');
       sleep(1);
       expect(browser().location().url()).toBe('/profile');
       expect(element('form[name="loginForm"]:visible').count()).toBe(0);

        // navigate directly
        browser().navigateTo('/#!/profile');
        sleep(1);
        expect(browser().location().url()).toBe('/profile');
        expect(element('form[name="loginForm"]:visible').count()).toBe(0);
    });

    it('shoud redirect to home when logging out on protected page', function() {
        browser().navigateTo('/#!/profile');
        element('a[ng-click="logout()"]').click();
        sleep(1);
        expect(browser().location().url()).toBe('/');
    });

    it('should log in and redirect to the profile summary page when correctly using the log in form', function() {
        element('a[ng-click="login()"]').click();
        sleep(1);
        dhInput('input[name="login-username"]').enter(email);
        dhInput('input[name="login-password"]').enter('test123');
        element('form[name="loginForm"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="logout()"]:visible').count()).toBeGreaterThan(0);
        expect(element('a[ng-click="login()"]:visible').count()).toBe(0);
        expect(browser().location().url()).toBe('/profile');
        element('a[ng-click="logout()"]').click();
    });

    it('should show the correct page after navigating to a protected page while not logged in', function() {
        browser().navigateTo('#!/profile');
        sleep(1);
        expect(element('form[name="loginForm"]:visible').count()).toBe(1);
        dhInput('input[name="login-username"]').enter(email);
        dhInput('input[name="login-password"]').enter('test123');
        element('form[name="loginForm"] button[type="submit"]').click();
        sleep(1);
        expect(browser().location().url()).toBe('/profile');
        element('a[ng-click="logout()"]').click();
    });

    it('should not accept the same email address twice for registration', function() {
        element('a[ng-click="login()"]').click();
        dhInput('input[name="registration-name"]').enter('Foo1');
        dhInput('input[name="registration-surname"]').enter('Bar1');
        dhInput('input[name="registration-email"]').enter(email);
        dhInput('input[name="registration-password"]').enter('test1231');
        dhInput('input[name="registration-passwordrepeat"]').enter('test1231');
        element('form[name="registrationForm"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="login()"]:visible').count()).toBeGreaterThan(0);
        expect(element('form[name="loginForm"]:visible').count()).toBe(1);
    });

    it('should not accept the submission of an empty or invalid registration form', function() {
        element('a[ng-click="login()"]').click();
        expect(element('input[name="registration-name"]').attr('class')).toMatch(/ng-invalid/);
        expect(element('input[name="registration-surname"]').attr('class')).toMatch(/ng-invalid/);
        expect(element('input[name="registration-email"]').attr('class')).toMatch(/ng-invalid/);
        expect(element('input[name="registration-password"]').attr('class')).toMatch(/ng-invalid/);
        element('form[name="registrationForm"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="login()"]:visible').count()).toBeGreaterThan(0);
        expect(element('form[name="loginForm"]:visible').count()).toBe(1);
        dhInput('input[name="registration-name"]').enter('Foo1');
        expect(element('input[name="registration-name"]').attr('class')).not().toMatch(/ng-invalid/);
        dhInput('input[name="registration-password"]').enter('test1231');
        expect(element('input[name="registration-password"]').attr('class')).not().toMatch(/ng-invalid/);
        dhInput('input[name="registration-passwordrepeat"]').enter('test');
        expect(element('input[name="registration-passwordrepeat"]').attr('class')).toMatch(/ng-invalid/);
        dhInput('input[name="registration-passwordrepeat"]').enter('test1231');
        expect(element('input[name="registration-passwordrepeat"]').attr('class')).not().toMatch(/ng-invalid/);
    });

    it('should not accept wrong credentials for logging in', function() {
        element('a[ng-click="login()"]').click();
        dhInput('input[name="login-username"]').enter('no@suchusername.de');
        dhInput('input[name="login-password"]').enter('ölksajfdölksajfdsaölkjfd');
        element('form[name="loginForm"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="login()"]:visible').count()).toBeGreaterThan(0);
        expect(element('form[name="loginForm"]:visible').count()).toBe(1);
    });
  });

});
