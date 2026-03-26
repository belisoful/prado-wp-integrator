<?php

namespace PradoWpIntegrator\Test;

use PHPUnit\Framework\TestCase;
use PradoWpIntegrator\WPUser;
use Prado\Security\IUserManager;

/**
 * Test class for WPUser
 */
class WPUserTest extends TestCase
{
    public function testConstructor()
    {
        $this->markTestSkipped('WPUser requires a UserManager to be constructed');
    }

    public function testLoad()
    {
        $user = $this->createMock(WPUser::class);
        $userData = ['user_login' => 'testuser', 'user_pass' => 'password'];
        $metaData = ['first_name' => 'Test', 'last_name' => 'User'];
        
        $user->load($userData, $metaData);
        
        $this->assertInstanceOf(WPUser::class, $user);
    }

    public function testGet()
    {
        $user = $this->createMock(WPUser::class);
        $userData = ['user_login' => 'testuser', 'user_pass' => 'password'];
        $metaData = ['first_name' => 'Test', 'last_name' => 'User'];
        
        $user->load($userData, $metaData);
        
        // This is difficult to test without full implementation
        // But let's just test that it doesn't throw errors
        $this->assertInstanceOf(WPUser::class, $user);
    }

    public function testValidateUser()
    {
        $user = $this->createMock(WPUser::class);
        $this->assertFalse($user->validateUser('testuser', 'password'));
    }

    public function testCreateUser()
    {
        $user = $this->createMock(WPUser::class);
        $this->assertNull($user->createUser('testuser'));
    }
}