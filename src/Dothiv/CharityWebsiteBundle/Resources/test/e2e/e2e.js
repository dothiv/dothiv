'use strict';

// TODO put the following page object in separate file for reuse
var dotHIVApp = function() {

    var that = this;

    this.username = '';
    this.password = '';

    this.get = function() {
        browser.get('/');
    };

    this.buttons = {
        login:  function() { return element(by.xpath('//*[@ng-click="login()"]')); },
        logout: function() { return element(by.xpath('//*[@ng-click="logout()"]')); },
        language: {
            chooser: function() { return element(by.xpath('//div[contains(concat(" ",@class," "),"language-chooser")]/div/a')); },
            keysonly: function() { return element(by.xpath('//*[contains(concat(" ",@class," "),"language-chooser")]//a[.="Keys only"]')); },
        },
    };

    this.forms = {
        registration: {
            form:           function() { return element(by.xpath('//form[@name="registration"]')); },
            name:           function() { return element(by.xpath('//input[@id="registration-name"]')); },
            surname:        function() { return element(by.xpath('//input[@id="registration-surname"]')); },
            email:          function() { return element(by.xpath('//input[@id="registration-email"]')); },
            password:       function() { return element(by.xpath('//input[@id="registration-password"]')); },
            passwordrepeat: function() { return element(by.xpath('//input[@id="registration-passwordrepeat"]')); },
            submit:         function() { return element(by.xpath('//form[@name="registration"]//button[@type="submit"]')); },
        },
        login: {
            form:     function() { return element(by.xpath('//form[@name="login"]')); },
            username: function() { return element(by.xpath('//input[@id="login-username"]')); },
            password: function() { return element(by.xpath('//input[@id="login-password"]')); },
            submit:   function() { return element(by.xpath('//form[@name="login"]//button[@type="submit"]')); },
        }
    };

    this.modals = {
        loginregistration: function() { return element(by.xpath('//div[contains(concat(" ", @class, " ")," modal ")][//form[@name="login"]]')); },
    };

    this.state = {
        authenticated: function() { return that.buttons.logout().isDisplayed(); },
    };

    this.urls = {
        homepage: '/#!/$',
        profile: '/#!/profile$',
    }

    this.mock = {
        domain: {
            register: function(name,email) {
                browser.get('/#!/mock');
                element(by.xpath('//form[@name="registerdomainform"]/input[1]')).sendKeys(email);
                element(by.xpath('//form[@name="registerdomainform"]/input[2]')).sendKeys(name);
                element(by.xpath('//form[@name="registerdomainform"]/button')).click();
                return element(by.xpath('//div[contains(concat(" ",@class," ")," modal ")]/p')).getText().then(function(resp) {
                    var token = JSON.parse(resp).claimingToken;
                    expect(token.length).toBe(64);
                    return token;
                });
            }
        }
    }

    /**
     * Returns the text content of the currently shown tooltip. If no tooltip is shown, undefined is returned.
     * To make sure the tooltip is at a certain place, specify an XPath context by giving a XPath query for the first argument.
     * Examples:
     * - '//html//' -- to search document-wide (this is default value if no argument is given)
     * - '//input[@id="myid"]/' -- to search next to the input field with id 'myid'
     */
    this.tooltip = function(at) {
        if (undefined === at)
            at = '//html//';

        // finds and counts all divs with CSS class 'tooltip'
        var c = browser.findElements(by.xpath('//div[contains(concat(" ",@class," ")," tooltip ")]')).then(function(list) { return list.length; }); // findElements returns promise, which will be resolved by expect()
        expect(c).toBeLessThan(2);

        // return undefined or the text of the element of the div with CSS class 'tooltip-inner' nested in a div with CSS class 'tooltip' which follows the element specified by `at`
        // <element matching at>
        // <div class="tooltip">
        //   <div class="tooltip-inner">this content is returned</div>
        // </div>
        return c == 0 ? undefined : element(by.xpath(at + 'following::div[contains(concat(" ",@class," ")," tooltip ")]//div[contains(concat(" ",@class," ")," tooltip-inner ")]')).getText();
    }

    /**
     * Registeres a new user and saves username and password to the classes fields.
     */
    this.register = function() {
        that.buttons.login().click();
        expect(that.modals.loginregistration().isDisplayed()).toBe(true);
        that.forms.registration.name().sendKeys('Foo');
        that.forms.registration.surname().sendKeys('Bar');
        that.forms.registration.email().sendKeys(that.username = ('e2etest-' + Math.random().toString(36).substring(7) + '@stop.hiv'));
        that.forms.registration.password().sendKeys(that.password = 'test123');
        that.forms.registration.passwordrepeat().sendKeys('test123');
        that.forms.registration.submit().click();
        expect(that.modals.loginregistration().isPresent()).toBe(false);
        expect(that.state.authenticated()).toBe(true);
    }

    /**
     * Logs the user out. Warning: fails if user is not logged in
     */
    this.logout = function() {
        that.buttons.logout().click();
        expect(that.state.authenticated()).toBe(false);
    }

    /**
     * Logs the user in. Username and password from the classes fields are used.
     */
    this.login = function() {
        expect(that.username.length).toBeGreaterThan(0);
        expect(that.password.length).toBeGreaterThan(0);
        that.buttons.login().click();
        expect(that.modals.loginregistration().isDisplayed()).toBe(true);
        that.forms.login.username().sendKeys(that.username);
        that.forms.login.password().sendKeys(that.password);
        that.forms.login.submit().click();
        expect(that.state.authenticated()).toBe(true);
    }

};

describe('dotHIVApp', function() {

  var app = new dotHIVApp();

  describe('login and logout management', function() {

    beforeEach(function() {
        app.get();
    });

    it('should show no login dialog at startup', function() {
        expect(app.forms.login.form().isPresent()).toBe(false);
    });

    it('should show a login button', function() {
        expect(app.buttons.login().isDisplayed()).toBe(true);
    });

    it('should not show a logout button', function() {
        expect(app.buttons.logout().isDisplayed()).toBe(false);
    });

    it('should show a login dialog after clicking on login', function() {
        app.buttons.login().click();
        expect(app.modals.loginregistration().isDisplayed()).toBe(true);
    });

    it('should show the login dialog when navigating to a protected page', function() {
        browser.get('/#!/profile');
        expect(app.modals.loginregistration().isDisplayed()).toBe(true);
    });

    it('should navigate back to the homepage when starting the app on a protected page and aborting the login', function() {
        browser.get('/#!/profile');
        browser.actions().mouseMove(browser.findElement(by.xpath('//body')), {x: 0, y: 0}).click().perform();
        expect(app.modals.loginregistration().isPresent()).toBe(false);
        expect(browser.getCurrentUrl()).toMatch(app.urls.homepage);
    });

    it('should log in after successful registration', function() {
        app.register();
        expect(app.state.authenticated()).toBe(true);
        app.logout();
    }, 15000);

    it('should not show a login dialog when navigating to a protected page while logged in', function() {
        app.login();
        browser.get('#!/profile');
        expect(browser.getCurrentUrl()).toMatch(app.urls.profile);
        expect(app.state.authenticated()).toBe(true);
        app.logout();
    });

    it('shoud redirect to home when logging out on protected page', function() {
        app.login();
        browser.get('#!/profile');
        app.logout();
        expect(browser.getCurrentUrl()).toMatch(app.urls.homepage);
    });

    it('should log in and redirect to the profile summary page when correctly using the log in form', function() {
        app.login();
        expect(browser.getCurrentUrl()).toMatch(app.urls.profile);
        app.logout();
    });

    it('should show the correct page after navigating to a protected page while not logged in', function() {
        browser.get('/#!/profile');
        expect(app.modals.loginregistration().isDisplayed()).toBe(true);
        app.forms.login.username().sendKeys(app.username);
        app.forms.login.password().sendKeys(app.password);
        app.forms.login.submit().click();
        expect(browser.getCurrentUrl()).toMatch(app.urls.profile);
        app.logout();
    });

    it('should not accept the same email address twice for registration', function() {
        app.buttons.login().click();
        app.forms.registration.name().sendKeys('John');
        app.forms.registration.surname().sendKeys('Doe');
        app.forms.registration.email().sendKeys(app.username);
        app.forms.registration.password().sendKeys('secret');
        app.forms.registration.passwordrepeat().sendKeys('secret');
        app.forms.registration.submit().click();
        expect(app.state.authenticated()).toBe(false);
        expect(app.modals.loginregistration().isDisplayed()).toBe(true);
    }, 15000);

    it('should not accept the submission of an empty or invalid registration form', function() {
        app.buttons.login().click();
        expect(app.forms.registration.name().getAttribute('class')).toMatch(/ng-invalid/);
        expect(app.forms.registration.surname().getAttribute('class')).toMatch(/ng-invalid/);
        expect(app.forms.registration.email().getAttribute('class')).toMatch(/ng-invalid/);
        expect(app.forms.registration.password().getAttribute('class')).toMatch(/ng-invalid/);

        app.forms.registration.submit().click();
        expect(app.state.authenticated()).toBe(false);
        expect(app.modals.loginregistration().isDisplayed()).toBe(true);

        app.forms.registration.email().sendKeys(app.username);
        expect(app.forms.registration.email().getAttribute('class')).not.toMatch(/ng-invalid/);

        app.forms.registration.name().sendKeys('Johnny');
        expect(app.forms.registration.name().getAttribute('class')).not.toMatch(/ng-invalid/);

        app.forms.registration.surname().sendKeys('McTest');
        expect(app.forms.registration.surname().getAttribute('class')).not.toMatch(/ng-invalid/);

        app.forms.registration.password().sendKeys('secret');
        expect(app.forms.registration.password().getAttribute('class')).not.toMatch(/ng-invalid/);
        expect(app.forms.registration.passwordrepeat().getAttribute('class')).toMatch(/ng-invalid/);

        app.forms.registration.passwordrepeat().sendKeys('secret');
        expect(app.forms.registration.passwordrepeat().getAttribute('class')).not.toMatch(/ng-invalid/);
    }, 15000);

    it('should not accept wrong credentials for logging in and login should work after using wrong credentials', function() {
        app.buttons.login().click();
        app.forms.login.username().sendKeys('no@suchusername.de');
        app.forms.login.password().sendKeys('ölksajfdölksajfdsaölkjfd');
        app.forms.login.submit().click();
        expect(app.state.authenticated()).toBe(false);
        expect(app.modals.loginregistration().isDisplayed()).toBe(true);

        app.forms.login.username().clear();
        app.forms.login.username().sendKeys(app.username);
        app.forms.login.password().clear();
        app.forms.login.password().sendKeys('secrrt'); // typo
        app.forms.login.submit().click();
        expect(app.state.authenticated()).toBe(false);
        expect(app.modals.loginregistration().isDisplayed()).toBe(true);

        app.forms.login.password().clear();
        app.forms.login.password().sendKeys('test123');
        app.forms.login.submit().click();
        expect(app.state.authenticated()).toBe(true);
        expect(app.modals.loginregistration().isPresent()).toBe(false);

        app.logout();
    }, 15000);

    describe('tooltip management', function() {

        beforeEach(function() {
            app.buttons.language.chooser().click();
            app.buttons.language.keysonly().click();
            expect(app.buttons.language.chooser().getText()).toEqual('Keys only');
        });

        describe('on login form', function() {

            it('should show the default tooltip when opening the login dialog', function() {
                app.buttons.login().click();
                expect(app.forms.login.form().isDisplayed()).toBe(true);
                expect(app.tooltip()).toBe('login.form.username.tooltip.default');
            });

            it('should show the tooltip on the username field when trying to submit an invalid email address', function() {
                app.buttons.login().click();
                app.forms.login.username().sendKeys('invalid');
                app.forms.login.password().sendKeys('invalid');
                app.forms.login.submit().click();
                expect(app.tooltip('//input[@id="login-username"]/')).toBe('login.form.username.tooltip.invalid.email');
            });

            it('should show the tooltip on the password field when trying to submit a valid email address with no password', function() {
                app.buttons.login().click();
                app.forms.login.username().sendKeys('some@username.de');
                app.forms.login.submit().click();
                expect(app.tooltip('//input[@id="login-password"]/')).toBe('login.form.password.tooltip.invalid.required');
            });

            it('should show the tooltip on the username field when submitting wrong credentials', function() {
                app.buttons.login().click();
                app.forms.login.username().sendKeys('nosuch@username.de');
                app.forms.login.password().sendKeys('sadfasdfasdf');
                app.forms.login.submit().click();
                expect(app.tooltip('//input[@id="login-username"]/')).toBe('security.login.failure.credentials');
            });

        });

        describe('on registration form', function() {

            beforeEach(function() {
                app.buttons.login().click();
            });

            it('should show the default tooltip when the name field has the focus', function() {
                app.forms.registration.name().sendKeys('');
                expect(app.tooltip('//input[@id="registration-name"]/')).toBe('registration.form.name.tooltip.default');
            });

            it('should show the default tooltip when the surname field has the focus', function() {
                app.forms.registration.surname().sendKeys('');
                expect(app.tooltip('//input[@id="registration-surname"]/')).toBe('registration.form.surname.tooltip.default');
            });

            it('should show the default tooltip when the email field has the focus', function() {
                app.forms.registration.email().sendKeys('');
                expect(app.tooltip('//input[@id="registration-email"]/')).toBe('registration.form.email.tooltip.default');
            });

            it('should show the default tooltip when the password field has the focus', function() {
                app.forms.registration.password().sendKeys('');
                expect(app.tooltip('//input[@id="registration-password"]/')).toBe('registration.form.password.tooltip.default');
            });

            it('should show the default tooltip when the passwordrepeat field has the focus', function() {
                app.forms.registration.passwordrepeat().sendKeys('');
                expect(app.tooltip('//input[@id="registration-passwordrepeat"]/')).toBe('registration.form.passwordrepeat.tooltip.default');
            });

            it('should show the invalid tooltip on the first invalid field when the form was submitted', function() {
                app.forms.registration.submit().click();
                expect(app.tooltip('//input[@id="registration-name"]/')).toBe('registration.form.name.tooltip.invalid.required');

                app.forms.registration.name().sendKeys('Foo');
                app.forms.registration.submit().click();
                expect(app.tooltip('//input[@id="registration-surname"]/')).toBe('registration.form.surname.tooltip.invalid.required');

                app.forms.registration.surname().sendKeys('Bar');
                app.forms.registration.submit().click();
                expect(app.tooltip('//input[@id="registration-email"]/')).toBe('registration.form.email.tooltip.invalid.required');

                app.forms.registration.email().sendKeys('invalid email address');
                app.forms.registration.submit().click();
                expect(app.tooltip('//input[@id="registration-email"]/')).toBe('registration.form.email.tooltip.invalid.email');

                app.forms.registration.email().clear();
                app.forms.registration.email().sendKeys(app.username = 'e2etest-' + Math.random().toString(36).substring(7) + '@stop.hiv');
                app.forms.registration.submit().click();
                expect(app.tooltip('//input[@id="registration-password"]/')).toBe('registration.form.password.tooltip.invalid.required');

                app.forms.registration.password().sendKeys('foobar');
                app.forms.registration.submit().click();
                expect(app.tooltip('//input[@id="registration-passwordrepeat"]/')).toMatch(/registration\.form\.passwordrepeat\.tooltip\.invalid\.(required|repeat)/);

                app.forms.registration.passwordrepeat().sendKeys('foo');
                app.forms.registration.submit().click();
                expect(app.tooltip('//input[@id="registration-passwordrepeat"]/')).toBe('registration.form.passwordrepeat.tooltip.invalid.repeat');

                app.forms.registration.passwordrepeat().sendKeys('bar');
                app.forms.registration.submit().click();
                expect(app.modals.loginregistration().isPresent()).toBe(false);
                expect(app.state.authenticated()).toBe(true);

                app.logout();
                app.buttons.login().click();
                app.forms.registration.name().sendKeys('Foo');
                app.forms.registration.surname().sendKeys('Foo');
                app.forms.registration.email().sendKeys(app.username);
                app.forms.registration.password().sendKeys('secret');
                app.forms.registration.passwordrepeat().sendKeys('secret');
                app.forms.registration.submit().click();
                expect(app.tooltip('//input[@id="registration-email"]/')).toBeDefined();

                app.forms.registration.email().clear();
                app.forms.registration.email().sendKeys(app.username = 'e2etest-' + Math.random().toString(36).substring(7) + '@stop.hiv');
                app.forms.registration.submit().click();
                expect(app.modals.loginregistration().isPresent()).toBe(false);
                expect(app.state.authenticated()).toBe(true);

                app.logout();
            }, 60000);

        });

    });

  });

  describe('domain claims', function() {

      describe('when already logged in', function() {

          beforeEach(function() {
              browser.get('/#!/');
              app.register();
              app.buttons.language.chooser().click();
              app.buttons.language.keysonly().click();
              expect(app.buttons.language.chooser().getText()).toEqual('Keys only');
          }, 60000);

          it('should claim the domain immediately if browser open the provided link', function() {
              var name = 'e2etest-' + Math.random().toString(36).substring(7) + '.hiv';
              app.mock.domain.register('e2etest-' + Math.random().toString(36).substring(7) + '.hiv', 'some@different-email.de').then(function(token) {
                  expect(app.state.authenticated()).toBe(true);
                  browser.get('/#!/profile/domains/claim?token=' + token);
              }).then(function() {
                  expect(element(by.xpath('//*[.="profile.domain.claim.success.headline"]')).isDisplayed()).toBe(true);
              });
          }, 60000);

          it('should not claim the domain if there is a problem with the token and then accept the correct token with the input field', function() {
              var name = 'e2etest-' + Math.random().toString(36).substring(7) + '.hiv';
              app.mock.domain.register('e2etest-' + Math.random().toString(36).substring(7) + '.hiv', app.username).then(function(token) {
                  expect(app.state.authenticated()).toBe(true);
                  browser.get('/#!/profile/domains/claim?token=' + token + '1');
                  return token;
              }).then(function(token) {
                  expect(element(by.xpath('//*[.="profile.domain.claim.error.headline"]')).isDisplayed()).toBe(true);
                  expect(element(by.xpath('//button[.="profile.domain.claim.error.button"]')).isDisplayed()).toBe(true);

                  element(by.xpath('//button[.="profile.domain.claim.error.button"]')).click();
                  var input = element(by.xpath('//input[@placeholder="profile.domain.claim.token.placeholder"]'));
                  expect(input.isDisplayed()).toBe(true);

                  input.sendKeys(token + "XXXwrong");
                  element(by.xpath('//button[.="profile.domain.claim.token.button"]')).click();
                  expect(element(by.xpath('//*[.="profile.domain.claim.error.headline"]')).isDisplayed()).toBe(true);
                  expect(element(by.xpath('//button[.="profile.domain.claim.error.button"]')).isDisplayed()).toBe(true);

                  element(by.xpath('//button[.="profile.domain.claim.error.button"]')).click();
                  input = element(by.xpath('//input[@placeholder="profile.domain.claim.token.placeholder"]'));
                  expect(input.isDisplayed()).toBe(true);

                  input.sendKeys(token);
                  element(by.xpath('//button[.="profile.domain.claim.token.button"]')).click();
                  expect(element(by.xpath('//*[.="profile.domain.claim.success.headline"]')).isDisplayed()).toBe(true);
              });
          }, 60000);

          it('should show the input field if no token is supplied', function() {
              var name = 'e2etest-' + Math.random().toString(36).substring(7) + '.hiv';
              app.mock.domain.register('e2etest-' + Math.random().toString(36).substring(7) + '.hiv', app.username).then(function(token) {
                  expect(app.state.authenticated()).toBe(true);
                  browser.get('/#!/profile/domains/claim');
                  return token;
              }).then(function(token) {
                  expect(element(by.xpath('//*[.="profile.domain.claim.token.headline"]')).isDisplayed()).toBe(true);
                  var input = element(by.xpath('//input[@placeholder="profile.domain.claim.token.placeholder"]'));
                  expect(input.isDisplayed()).toBe(true);

                  input.sendKeys(token);
                  element(by.xpath('//button[.="profile.domain.claim.token.button"]')).click();
                  expect(element(by.xpath('//*[.="profile.domain.claim.success.headline"]')).isDisplayed()).toBe(true);
              });
          }, 60000)

          it('should show the domain in the domain list after claiming', function() {
              var name = 'e2etest-' + Math.random().toString(36).substring(7) + '.hiv';
              app.mock.domain.register('e2etest-' + Math.random().toString(36).substring(7) + '.hiv', 'some@different-email.de').then(function(token) {
                  expect(app.state.authenticated()).toBe(true);
                  browser.get('/#!/profile/domains/claim?token=' + token);
              }).then(function() {
                  expect(element(by.xpath('//*[.="profile.domain.claim.success.headline"]')).isDisplayed()).toBe(true);
                  browser.get('/#!/profile/domains/list');
                  element(by.xpath('//input[@ng-model="searchText.name"]')).sendKeys(name);
              }).then(function() {
                  debugger;
                  expect(element(by.xpath('//h3[.="'+name+'"]')).isDisplayed()).toBe(true);
              });
          }, 60000)

          afterEach(function() {
              app.logout();
          });

      });

  });

});
