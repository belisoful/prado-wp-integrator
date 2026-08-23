<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Prado\Security\TDbUser;
use Prado\Web\THttpCookie;
use PradoWpIntegrator\TestTools\StubSessionTokens;
use PradoWpIntegrator\TestTools\WPTestCase;
use PradoWpIntegrator\WPUser;
use PradoWpIntegrator\WPUserManager;

/**
 * Test class for WPUser.
 *
 * The cookie tests need the WordPress key constants, so they run in their own
 * process: initializeWPConstants() can only define them once.
 */
class WPUserTest extends WPTestCase
{
    /**
     * @return WPUser a user attached to a bare manager
     */
    private function makeUser(): WPUser
    {
        return new WPUser(new WPUserManager());
    }

    public function testConstructor()
    {
        $user = $this->makeUser();

        $this->assertInstanceOf(WPUser::class, $user);
        $this->assertInstanceOf(TDbUser::class, $user);
    }

    public function testLoadStoresDataAndMeta()
    {
        $user = $this->makeUser();

        $user->load(['user_login' => 'alice'], ['first_name' => 'Alice']);

        $this->assertSame('alice', $user->user_login);
        $this->assertSame('Alice', $user->first_name);
    }

    public function testDataTakesPrecedenceOverMeta()
    {
        $user = $this->makeUser();

        $user->load(['nickname' => 'from-data'], ['nickname' => 'from-meta']);

        $this->assertSame('from-data', $user->nickname);
    }

    public function testUnknownPropertiesAreNull()
    {
        $user = $this->makeUser();
        $user->load(['user_login' => 'alice'], []);

        $this->assertNull($user->no_such_field);
    }

    public function testUnknownPropertiesAreNullBeforeLoading()
    {
        $this->assertNull($this->makeUser()->user_login);
    }

    public function testValidateUserIsAStub()
    {
        $this->assertFalse($this->makeUser()->validateUser('alice', 'secret'));
    }

    public function testCreateUserIsAStub()
    {
        $this->assertNull($this->makeUser()->createUser('alice'));
    }

    public function testSaveUserToCookieIsAStub()
    {
        $this->assertNull($this->makeUser()->saveUserToCookie(new THttpCookie('c', '')));
    }

    public function testCreateUserFromCookieRejectsAMalformedCookie()
    {
        $user = $this->makeUser();

        $this->assertFalse($user->createUserFromCookie(new THttpCookie('auth', 'not|enough|parts')));
    }

    public function testCreateUserFromCookieRejectsAnExpiredCookie()
    {
        $user = $this->makeUser();
        $cookie = new THttpCookie('auth', 'alice|' . (time() - 60) . '|token|hmac');

        $this->assertNull($user->createUserFromCookie($cookie));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCreateUserFromCookieRejectsAnUnknownUser()
    {
        $user = $this->initializedModuleUser();
        $cookie = new THttpCookie('auth', 'nobody|' . (time() + 3600) . '|token|hmac');

        $this->assertNull($user->createUserFromCookie($cookie));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCreateUserFromCookieRejectsABadHmac()
    {
        $user = $this->initializedModuleUser();
        $cookie = new THttpCookie('auth', 'alice|' . (time() + 3600) . '|token|not-the-right-hmac');

        $this->assertNull($user->createUserFromCookie($cookie));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCreateUserFromCookieRejectsAnUnknownSessionToken()
    {
        $module = $this->createModule(['SecretKey' => 'secret-key', 'AuthKey' => 'auth-key', 'AuthSalt' => 'auth-salt']);
        $module->dyPreInit(null);
        $module->init(null);
        StubSessionTokens::declareWith(false);
        $user = new WPUser($this->app->getModule('wpusermanager'));

        $cookie = new THttpCookie('auth', $this->validCookieValue($module, 'alice', 'session-token'));

        // the hmac is right, so verification gets as far as the session token,
        // which the session store does not know
        $this->assertNull($user->createUserFromCookie($cookie));
    }

    /**
     * The whole cookie path: a well-formed, unexpired, correctly signed cookie
     * whose session token the store recognises returns the WordPress user.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCreateUserFromCookieReturnsTheUserForAValidCookie()
    {
        $module = $this->createModule(['SecretKey' => 'secret-key', 'AuthKey' => 'auth-key', 'AuthSalt' => 'auth-salt']);
        $module->dyPreInit(null);
        $module->init(null);
        StubSessionTokens::declareWith(true);
        $user = new WPUser($this->app->getModule('wpusermanager'));

        $cookie = new THttpCookie('auth', $this->validCookieValue($module, 'alice', 'session-token'));
        $authenticated = $user->createUserFromCookie($cookie);

        $this->assertInstanceOf(WPUser::class, $authenticated);
        $this->assertSame('alice', $authenticated->user_login);
    }

    /**
     * @param \PradoWpIntegrator\WPIntegratorModule $module the initialized module
     * @param string $username the user login
     * @param string $token the session token
     * @return string a cookie value with a valid hmac
     */
    private function validCookieValue($module, string $username, string $token): string
    {
        $expiration = time() + 3600;
        $user = $module->get_user_by('login', $username);
        $passwordFragment = substr($user->user_pass, 8, 4);
        $key = $module->wp_hash($username . '|' . $passwordFragment . '|' . $expiration . '|' . $token, 'auth');
        $hash = hash_hmac('sha256', $username . '|' . $expiration . '|' . $token, $key);

        return implode('|', [$username, $expiration, $token, $hash]);
    }

    /**
     * @return WPUser a user whose manager is wired to an initialized module
     */
    private function initializedModuleUser(): WPUser
    {
        $module = $this->createModule();
        $module->dyPreInit(null);
        $module->init(null);

        return new WPUser($this->app->getModule('wpusermanager'));
    }
}
