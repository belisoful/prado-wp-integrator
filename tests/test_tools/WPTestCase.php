<?php

/**
 * WPTestCase class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator\TestTools;

use PHPUnit\Framework\TestCase;
use Prado\Data\TDataSourceConfig;
use Prado\Prado;
use Prado\TApplication;
use PradoWpIntegrator\WPIntegratorModule;

/**
 * Base test case providing a throwaway PRADO application backed by an
 * in-memory SQLite database with a minimal WordPress schema.
 *
 * The application is a real TApplication (PRADO's singleton), so classes that
 * reach for Prado::getApplication() - the module, the portlets, the theme
 * behavior - can be exercised without stubbing PRADO itself.
 */
abstract class WPTestCase extends TestCase
{
    /** @var string the temporary application base path */
    protected $appPath;

    /** @var TApplication the application under test */
    protected $app;

    /** @var TDataSourceConfig the data source module registered as 'db' */
    protected $dataSource;

    /**
     * Creates the application and the seeded database.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->appPath = $this->makeApplicationPath();
        $this->app = new TApplication($this->appPath . '/protected', false, TApplication::CONFIG_TYPE_PHP);
        $this->dataSource = new TDataSourceConfig();
        $this->dataSource->setId('db');
        $this->dataSource->getDbConnection()->setConnectionString('sqlite::memory:');
        $this->app->setModule('db', $this->dataSource);
        $this->createSchema();
        $this->seedDatabase();
    }

    /**
     * Removes the temporary application directory.
     */
    protected function tearDown(): void
    {
        if ($this->dataSource !== null) {
            $this->dataSource->getDbConnection()->setActive(false);
        }
        $this->dataSource = null;
        $this->app = null;
        Prado::setApplication(null);
        if ($this->appPath !== null && is_dir($this->appPath)) {
            $this->removeDirectory($this->appPath);
        }
        $this->appPath = null;
        parent::tearDown();
    }

    /**
     * @return string a fresh application base path with a runtime directory
     */
    protected function makeApplicationPath(): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('prado-wp-test-', true);
        mkdir($path . DIRECTORY_SEPARATOR . 'protected' . DIRECTORY_SEPARATOR . 'runtime', 0o777, true);
        return $path;
    }

    /**
     * @param string $path the directory to remove recursively
     */
    protected function removeDirectory(string $path): void
    {
        foreach (scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $path . DIRECTORY_SEPARATOR . $entry;
            is_dir($full) ? $this->removeDirectory($full) : @unlink($full);
        }
        @rmdir($path);
    }

    /**
     * @return \Prado\Data\TDbConnection the active test connection
     */
    protected function db()
    {
        $connection = $this->dataSource->getDbConnection();
        $connection->setActive(true);
        return $connection;
    }

    /**
     * @param string $sql the statement to execute
     */
    protected function execute(string $sql): void
    {
        $this->db()->createCommand($sql)->execute();
    }

    /**
     * Creates the subset of the WordPress schema the module touches.
     */
    protected function createSchema(): void
    {
        $statements = [
            'CREATE TABLE wp_options (option_id INTEGER PRIMARY KEY AUTOINCREMENT, option_name TEXT, option_value TEXT, autoload TEXT)',
            'CREATE TABLE wp_users (ID INTEGER PRIMARY KEY AUTOINCREMENT, user_login TEXT, user_pass TEXT, user_email TEXT, user_nicename TEXT, display_name TEXT)',
            'CREATE TABLE wp_usermeta (umeta_id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, meta_key TEXT, meta_value TEXT)',
            'CREATE TABLE wp_posts (ID INTEGER PRIMARY KEY AUTOINCREMENT, post_author INTEGER, post_date TEXT, post_date_gmt TEXT, post_content TEXT, post_title TEXT, post_excerpt TEXT, post_status TEXT, comment_status TEXT, ping_status TEXT, post_password TEXT, post_name TEXT, post_modified TEXT, post_modified_gmt TEXT, post_parent INTEGER, guid TEXT, post_type TEXT, post_mime_type TEXT, comment_count INTEGER)',
            'CREATE TABLE wp_postmeta (meta_id INTEGER PRIMARY KEY AUTOINCREMENT, post_id INTEGER, meta_key TEXT, meta_value TEXT)',
        ];
        foreach ($statements as $sql) {
            $this->execute($sql);
        }
    }

    /**
     * Seeds one option, one user and one published post.
     */
    protected function seedDatabase(): void
    {
        $this->addOption('siteurl', 'http://example.com');
        $this->addOption('home', 'http://example.com');
        $this->addOption('blogname', 'Test Blog');
        $this->addUser('alice', '$P$Babcdefghijklmnop', 'alice@example.com', ['first_name' => 'Alice']);
        $this->addPost([
            'post_author' => 7,
            'post_content' => '<p>Hello world</p>',
            'post_title' => 'Hello',
            'post_status' => 'publish',
            'post_type' => 'post',
        ], ['thumbnail' => 'yes']);
    }

    /**
     * @param string $name the option name
     * @param string $value the option value
     * @param string $autoload the autoload flag
     */
    protected function addOption(string $name, string $value, string $autoload = 'yes'): void
    {
        $connection = $this->db();
        $command = $connection->createCommand(
            'INSERT INTO wp_options (option_name, option_value, autoload) VALUES (:name, :value, :autoload)'
        );
        $command->bindValue(':name', $name);
        $command->bindValue(':value', $value);
        $command->bindValue(':autoload', $autoload);
        $command->execute();
    }

    /**
     * @param string $login the user login
     * @param string $pass the hashed password
     * @param string $email the user email
     * @param array $meta the user meta key/value pairs
     * @return int the new user id
     */
    protected function addUser(string $login, string $pass, string $email, array $meta = []): int
    {
        $connection = $this->db();
        $command = $connection->createCommand(
            'INSERT INTO wp_users (user_login, user_pass, user_email, user_nicename, display_name)'
            . ' VALUES (:login, :pass, :email, :login, :login)'
        );
        $command->bindValue(':login', $login);
        $command->bindValue(':pass', $pass);
        $command->bindValue(':email', $email);
        $command->execute();
        $id = (int) $connection->getLastInsertID();

        foreach ($meta as $key => $value) {
            $command = $connection->createCommand(
                'INSERT INTO wp_usermeta (user_id, meta_key, meta_value) VALUES (:id, :key, :value)'
            );
            $command->bindValue(':id', $id);
            $command->bindValue(':key', $key);
            $command->bindValue(':value', $value);
            $command->execute();
        }
        return $id;
    }

    /**
     * @param array $fields the wp_posts columns to set
     * @param array $meta the post meta key/value pairs
     * @return int the new post id
     */
    protected function addPost(array $fields, array $meta = []): int
    {
        $fields += [
            'post_author' => 1,
            'post_date' => '2020-01-02 03:04:05',
            'post_date_gmt' => '2020-01-02 11:04:05',
            'post_content' => '',
            'post_title' => '',
            'post_excerpt' => '',
            'post_status' => 'publish',
            'comment_status' => 'open',
            'ping_status' => 'open',
            'post_password' => '',
            'post_name' => 'post-name',
            'post_modified' => '2020-02-03 04:05:06',
            'post_modified_gmt' => '2020-02-03 12:05:06',
            'post_parent' => 0,
            'guid' => 'http://example.com/?p=1',
            'post_type' => 'post',
            'post_mime_type' => '',
            'comment_count' => 0,
        ];
        $columns = array_keys($fields);
        $connection = $this->db();
        $command = $connection->createCommand(
            'INSERT INTO wp_posts (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', $columns) . ')'
        );
        foreach ($fields as $column => $value) {
            $command->bindValue(':' . $column, $value);
        }
        $command->execute();
        $id = (int) $connection->getLastInsertID();

        foreach ($meta as $key => $value) {
            $command = $connection->createCommand(
                'INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES (:id, :key, :value)'
            );
            $command->bindValue(':id', $id);
            $command->bindValue(':key', $key);
            $command->bindValue(':value', $value);
            $command->execute();
        }
        return $id;
    }

    /**
     * Creates a module wired to the test database. The module is NOT
     * initialized; init() defines global constants and belongs in a test
     * running in its own process.
     *
     * @param array $properties extra property values to set, keyed by setter suffix
     * @return WPIntegratorModule the configured module
     */
    protected function createModule(array $properties = []): WPIntegratorModule
    {
        $module = new WPIntegratorModule();
        $module->setId('wp');
        $module->setConnectionID('db');
        $module->setWPDbParameterID('wpdbparameter');
        $module->setWPUserManagerID('wpusermanager');
        $module->setWPAuthManagerID('wpauthmanager');
        foreach ($properties as $name => $value) {
            $module->{'set' . $name}($value);
        }
        $this->app->setModule('wp', $module);
        return $module;
    }

    /**
     * Attaches a plugin module to a control.
     *
     * TControl::getPluginModule() resolves the module from the application on
     * its own, so this only pins the module a test wants a control to use,
     * independent of how the framework resolves it.
     *
     * @param \Prado\Web\UI\TControl $control the control to wire up
     * @param null|WPIntegratorModule $module the module, or null for the module on the application
     */
    protected function attachPluginModule($control, $module = null): void
    {
        $property = new \ReflectionProperty(\Prado\Web\UI\TControl::class, '_pluginmodule');
        $property->setAccessible(true);
        $property->setValue($control, $module ?? $this->app->getModule('wp'));
    }

    /**
     * @return string the absolute path of the WordPress theme fixture
     */
    protected function themePath(): string
    {
        return realpath(__DIR__ . '/../fixtures/wp-content/themes/prado-test-theme');
    }

    /**
     * @return string the absolute path of the non-WordPress theme fixture
     */
    protected function plainThemePath(): string
    {
        return realpath(__DIR__ . '/../fixtures/wp-content/themes/prado-plain-theme');
    }
}
