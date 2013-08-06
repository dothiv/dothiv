'use strict';

/* http://docs.angularjs.org/guide/dev_guide.e2e-testing */

describe('dotHIVApp', function() {

  describe('login and logout management', function() {

    beforeEach(function() {
        browser().navigateTo('/');
        sleep(1);
    });

    var email;

    it('should show no login dialog at startup', function() {
        expect(element('form[name="login"]:visible').count()).toBe(0);
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
        expect(element('form[name="login"]:visible').count()).toBe(1);
    });

    it('should show the login dialog exactly once when navigating to a protected page', function() {
        browser().navigateTo('#!/profile');
        sleep(1);
        expect(element('form[name="login"]:visible').count()).toBe(1);
    });

    it('should navigate back to the homepage when starting the app on a protected page and aborting the login', function() {
        browser().navigateTo('/#!/profile');
        sleep(1);
        element('.modal-backdrop').click();
        sleep(1);
        expect(element('form[name="login"]:visible').count()).toBe(0);
        expect(browser().location().url()).toBe('/');
    });

    it('should navigate back to the homepage when navigating on a protected page and aborting the login', function() {
        browser().navigateTo('#!/profile');
        sleep(1);
        element('.modal-backdrop').click();
        sleep(1);
        expect(element('form[name="login"]:visible').count()).toBe(0);
        expect(browser().location().url()).toBe('/');
    });

    it('should log in after successful registration', function() {
        element('a[ng-click="login()"]').click();
        dhInput('input[id="registration-name"]').enter('Foo');
        dhInput('input[id="registration-surname"]').enter('Bar');
        dhInput('input[id="registration-email"]').enter(email = ('e2etest-' + Math.random().toString(36).substring(7) + '@stop.hiv'));
        dhInput('input[id="registration-password"]').enter('test123');
        dhInput('input[id="registration-passwordrepeat"]').enter('test123');
        element('form[name="registration"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="logout()"]:visible').count()).toBeGreaterThan(0);
    });

    it('should not show a login dialog when navigating to a protected page while logged in', function() {
        // navigate with ajax
       browser().navigateTo('#!/profile');
       sleep(1);
       expect(browser().location().url()).toBe('/profile');
       expect(element('form[name="login"]:visible').count()).toBe(0);

        // navigate directly
        browser().navigateTo('/#!/profile');
        sleep(1);
        expect(browser().location().url()).toBe('/profile');
        expect(element('form[name="login"]:visible').count()).toBe(0);
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
        dhInput('input[id="login-username"]').enter(email);
        dhInput('input[id="login-password"]').enter('test123');
        element('form[name="login"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="logout()"]:visible').count()).toBeGreaterThan(0);
        expect(element('a[ng-click="login()"]:visible').count()).toBe(0);
        expect(browser().location().url()).toBe('/profile');
        element('a[ng-click="logout()"]').click();
    });

    it('should show the correct page after navigating to a protected page while not logged in', function() {
        browser().navigateTo('#!/profile');
        sleep(1);
        expect(element('form[name="login"]:visible').count()).toBe(1);
        dhInput('input[id="login-username"]').enter(email);
        dhInput('input[id="login-password"]').enter('test123');
        element('form[name="login"] button[type="submit"]').click();
        sleep(1);
        expect(browser().location().url()).toBe('/profile');
        element('a[ng-click="logout()"]').click();
    });

    it('should not accept the same email address twice for registration', function() {
        element('a[ng-click="login()"]').click();
        dhInput('input[id="registration-name"]').enter('Foo1');
        dhInput('input[id="registration-surname"]').enter('Bar1');
        dhInput('input[id="registration-email"]').enter(email);
        dhInput('input[id="registration-password"]').enter('test1231');
        dhInput('input[id="registration-passwordrepeat"]').enter('test1231');
        element('form[name="registration"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="login()"]:visible').count()).toBeGreaterThan(0);
        expect(element('form[name="login"]:visible').count()).toBe(1);
    });

    it('should not accept the submission of an empty or invalid registration form', function() {
        element('a[ng-click="login()"]').click();
        expect(element('input[id="registration-name"]').attr('class')).toMatch(/ng-invalid/);
        expect(element('input[id="registration-surname"]').attr('class')).toMatch(/ng-invalid/);
        expect(element('input[id="registration-email"]').attr('class')).toMatch(/ng-invalid/);
        expect(element('input[id="registration-password"]').attr('class')).toMatch(/ng-invalid/);
        element('form[name="registration"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="login()"]:visible').count()).toBeGreaterThan(0);
        expect(element('form[name="login"]:visible').count()).toBe(1);
        dhInput('input[id="registration-name"]').enter('Foo1');
        expect(element('input[id="registration-name"]').attr('class')).not().toMatch(/ng-invalid/);
        dhInput('input[id="registration-password"]').enter('test1231');
        expect(element('input[id="registration-password"]').attr('class')).not().toMatch(/ng-invalid/);
        dhInput('input[id="registration-passwordrepeat"]').enter('test');
        expect(element('input[id="registration-passwordrepeat"]').attr('class')).toMatch(/ng-invalid/);
        dhInput('input[id="registration-passwordrepeat"]').enter('test1231');
        expect(element('input[id="registration-passwordrepeat"]').attr('class')).not().toMatch(/ng-invalid/);
    });

    it('should not accept wrong credentials for logging in', function() {
        element('a[ng-click="login()"]').click();
        dhInput('input[id="login-username"]').enter('no@suchusername.de');
        dhInput('input[id="login-password"]').enter('ölksajfdölksajfdsaölkjfd');
        element('form[name="login"] button[type="submit"]').click();
        sleep(1);
        expect(element('a[ng-click="login()"]:visible').count()).toBeGreaterThan(0);
        expect(element('form[name="login"]:visible').count()).toBe(1);
    });

    describe('tooltip management', function() {

        beforeEach(function() {
            element('.language-chooser > div > a').click();
            element('.language-chooser li:last-child > a').click();
            expect(element('.language-chooser > div > a').text()).toEqual('Keys only');
        });

        describe('on login form', function() {

            it('should show the default tooltip when opening the login dialog', function() {
                element('a[ng-click="login()"]').click();
                sleep(2);
                expect(element('input[id="login-username"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toBe('login.form.username.tooltip.default');
            });

            it('should show the tooltip on the username field when trying to submit an invalid email address', function() {
                element('a[ng-click="login()"]').click();
                dhInput('input[id="login-username"]').enter('invalid');
                dhInput('input[id="login-password"]').enter('invalid');
                element('form[name="login"] button[type="submit"]').click();
                sleep(1);
                expect(element('input[id="login-username"] + div.tooltip').count()).toBe(1);
                expect(element('input[id="login-password"] + div.tooltip').count()).toBe(0);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toBe('login.form.username.tooltip.invalid.email');
            });

            it('should show the tooltip on the password field when trying to submit a valid email address with no password', function() {
                element('a[ng-click="login()"]').click();
                dhInput('input[id="login-username"]').enter('some@username.de');
                element('form[name="login"] button[type="submit"]').click();
                sleep(1);
                expect(element('input[id="login-username"] + div.tooltip').count()).toBe(0);
                expect(element('input[id="login-password"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toBe('login.form.password.tooltip.invalid.required');
            });

            it('should show the tooltip on the username field when submitting wrong credentials', function() {
                element('a[ng-click="login()"]').click();
                dhInput('input[id="login-username"]').enter('nosuch@username.de');
                dhInput('input[id="login-password"]').enter('safdlkelkwelkwelw');
                element('form[name="login"] button[type="submit"]').click();
                sleep(1);
                expect(element('input[id="login-username"] + div.tooltip').count()).toBe(1);
                expect(element('input[id="login-password"] + div.tooltip').count()).toBe(0);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toEqual('security.login.failure.credentials');
            });

        });

        describe('on registration form', function() {

            beforeEach(function() {
                element('a[ng-click="login()"]').click();
                sleep(1);
            });

            var registrationFields = ['name','surname','email','password','passwordrepeat'];

            angular.forEach(registrationFields, function(f) {

                it('should show the default tooltip when the ' + f + ' field has the focus', function() {
                    dhInput('input[id="registration-' + f + '"]').focus();
                    expect(element('input[id="registration-' + f + '"] + div.tooltip').count()).toBe(1);
                    expect(element('div.tooltip-inner').count()).toBe(1);
                    expect(element('div.tooltip-inner').text()).toEqual('registration.form.' + f + '.tooltip.default');
                });

            });

            it('should show the invalid tooltip on the first invalid field when the form was submitted', function() {
                element('form[name="registration"] button[type="submit"]').click();
                expect(element('input[id="registration-name"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toEqual('registration.form.name.tooltip.invalid.required');

                dhInput('input[id="registration-name"]').enter('Foo');
                element('form[name="registration"] button[type="submit"]').click();

                expect(element('input[id="registration-surname"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toEqual('registration.form.surname.tooltip.invalid.required');

                dhInput('input[id="registration-surname"]').enter('Foo');
                element('form[name="registration"] button[type="submit"]').click();

                expect(element('input[id="registration-email"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toEqual('registration.form.email.tooltip.invalid.required');

                dhInput('input[id="registration-email"]').enter('novalidemail');
                element('form[name="registration"] button[type="submit"]').click();

                expect(element('input[id="registration-email"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toEqual('registration.form.email.tooltip.invalid.email');

                dhInput('input[id="registration-email"]').enter(email = ('e2etest-' + Math.random().toString(36).substring(7) + '@stop.hiv'));
                element('form[name="registration"] button[type="submit"]').click();

                expect(element('input[id="registration-password"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toEqual('registration.form.password.tooltip.invalid.required');

                dhInput('input[id="registration-password"]').enter('foobar');
                element('form[name="registration"] button[type="submit"]').click();

                expect(element('input[id="registration-passwordrepeat"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toMatch(/registration\.form\.passwordrepeat\.tooltip\.invalid\.(required|repeat)/);

                dhInput('input[id="registration-passwordrepeat"]').enter('foo');
                element('form[name="registration"] button[type="submit"]').click();

                expect(element('input[id="registration-passwordrepeat"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
                expect(element('div.tooltip-inner').text()).toEqual('registration.form.passwordrepeat.tooltip.invalid.repeat');

                dhInput('input[id="registration-passwordrepeat"]').enter('foobar');
                element('form[name="registration"] button[type="submit"]').click();
                sleep(1);

                expect(element('a[ng-click="logout()"]:visible').count()).toBeGreaterThan(0);
                element('a[ng-click="logout()"]:visible').click();
                sleep(1);

                element('a[ng-click="login()"]:visible').click();
                sleep(1);

                dhInput('input[id="registration-name"]').enter('Foo');
                dhInput('input[id="registration-surname"]').enter('Foo');
                dhInput('input[id="registration-email"]').enter(email);
                dhInput('input[id="registration-password"]').enter('foobar');
                dhInput('input[id="registration-passwordrepeat"]').enter('foobar');
                element('form[name="registration"] button[type="submit"]').click();

                expect(element('input[id="registration-email"] + div.tooltip').count()).toBe(1);
                expect(element('div.tooltip-inner').count()).toBe(1);
            });

        });

    });

  });

});
