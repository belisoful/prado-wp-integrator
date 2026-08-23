<?php

namespace PradoWpIntegrator\Test\Theme;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Prado\I18N\TGlobalization;
use Prado\Prado;
use PradoWpIntegrator\TestTools\NullGlobalizationApplication;
use PradoWpIntegrator\TestTools\WPTestCase;

/**
 * Test class for the WPFunctions theme shims.
 *
 * The shims are global functions, so every test that includes them runs in its
 * own process: declaring them in the PHPUnit process would leak WordPress
 * functions into every other test. PHPUnit collects and merges the coverage of
 * those child processes, so the shim bodies are measured normally.
 */
class WPFunctionsTest extends WPTestCase
{
    /** @var string the shim file under test */
    private const SHIM_FILE = __DIR__ . '/../../../src/Theme/WPFunctions.php';

    /**
     * @return string the resolved shim path
     */
    private function shim(): string
    {
        return realpath(self::SHIM_FILE);
    }

    public function testEveryDeclarationIsGuarded()
    {
        $contents = file_get_contents(self::SHIM_FILE);

        $this->assertSame(0, preg_match('/^(function|class)\s/m', $contents), 'Unguarded declaration in WPFunctions.php');
        $this->assertSame(88, preg_match_all('/^if \(!function_exists\(/m', $contents));
        $this->assertSame(5, preg_match_all('/^if \(!class_exists\(/m', $contents));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testIncludingTwiceDoesNotRedeclare()
    {
        include $this->shim();
        include $this->shim();

        $this->assertTrue(function_exists('get_the_title'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testPreDeclaredFunctionTakesPrecedence()
    {
        eval('function get_the_title() { return "REAL WORDPRESS"; }');

        include $this->shim();

        $this->assertSame('REAL WORDPRESS', get_the_title());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testPreDeclaredClassTakesPrecedence()
    {
        eval('class Walker_Comment { public $real = true; }');

        include $this->shim();

        $walker = new \Walker_Comment();
        $this->assertTrue(isset($walker->real));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testWordPressPluginApiIsNotRedeclared()
    {
        // src/composer.php loads wp-includes/plugin.php on the CLI, so the real
        // add_filter()/apply_filters()/add_action()/do_action() are already
        // declared here. The shims must leave them alone.
        $this->assertTrue(function_exists('add_filter'), 'WordPress plugin.php was not loaded');

        include $this->shim();

        foreach (['add_filter', 'apply_filters', 'add_action', 'do_action'] as $function) {
            $file = (new \ReflectionFunction($function))->getFileName();
            $this->assertStringEndsWith(
                'wp-includes' . DIRECTORY_SEPARATOR . 'plugin.php',
                $file,
                $function . '() was replaced by the shim'
            );
        }
    }

    /**
     * Exercises every shim with the theme constants defined, which is how
     * WPThemeBehavior::processParts() sets them up.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testShimsWithThemeConstantsDefined()
    {
        $themePath = $this->themePath();
        define('WP_THEME_PATH', $themePath);
        define('WP_THEME_URI', 'http://example.com/theme');
        define('WP_CONTENT_TAG', '<wpcontent/>');
        $GLOBALS['wp_theme_object'] = new \stdClass();
        $this->app->getParameters()->add('blogname', 'Test Blog');
        $this->app->getParameters()->add('blogdescription', 'A test blog');
        $this->app->setGlobalization(new TGlobalization());
        $this->app->getGlobalization()->setCulture('en_US');

        include $this->shim();

        // theme paths
        $this->assertSame($themePath, get_template_directory());
        $this->assertSame('http://example.com/theme', get_template_directory_uri());

        // theme file includes
        $this->assertStringContainsString('HEADER', $this->capture('get_header'));
        $this->assertStringContainsString('FOOTER', $this->capture('get_footer'));
        $this->assertStringContainsString('SIDEBAR', $this->capture('get_sidebar'));

        // template parts, with and without a style suffix
        $this->assertSame('CONTENT-SINGLE-PART', $this->capture(fn() => get_template_part('template-parts/content', 'single')));
        $this->assertSame('CONTENT-PART', $this->capture(fn() => get_template_part('template-parts/content')));
        // no content-missing.php and no content.php under that name: nothing is included
        $this->assertSame('', $this->capture(fn() => get_template_part('template-parts/missing')));
        $this->assertSame('', $this->capture(fn() => get_template_part('template-parts/missing', 'style')));
        // the styled file is absent, so the plain template part is the fallback
        $this->assertSame('CONTENT-PART', $this->capture(fn() => get_template_part('template-parts/content', 'absent')));

        // application-backed shims
        $this->assertSame('lang="en_US"', $this->capture('language_attributes'));
        $this->assertSame('Test Blog', get_bloginfo('name'));
        $this->assertSame('A test blog', get_bloginfo('description'));
        $this->assertSame($this->app->getGlobalization()->getCharset(), get_bloginfo('charset'));
        $this->assertSame('unknown-key', get_bloginfo('unknown-key'));
        $this->assertSame('Test Blog', $this->capture(fn() => bloginfo('name')));
        $this->assertSame($GLOBALS['wp_theme_object'], wp_get_theme());
        $this->assertIsBool(is_rtl());
    }

    /**
     * Exercises the branches taken when no theme constants are defined.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testShimsWithoutThemeConstants()
    {
        include $this->shim();

        $this->assertSame('', get_template_directory());
        $this->assertSame('', get_template_directory_uri());
        $this->assertSame('', get_header());
        $this->assertSame('', get_footer());
        $this->assertSame('', get_sidebar());
        $this->assertSame('', get_template_part('template-parts/content'));
        $this->assertSame('', get_template_part('template-parts/content', 'single'));
        $this->assertSame('', get_template_part('template-parts/missing'));
        $this->assertFalse(have_posts());
    }

    /**
     * Covers the defensive null-globalization branches.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testShimsWithoutGlobalization()
    {
        Prado::setApplication(new NullGlobalizationApplication($this->appPath . '/protected', false, NullGlobalizationApplication::CONFIG_TYPE_PHP));

        include $this->shim();

        $this->assertSame('', $this->capture('language_attributes'));
        $this->assertSame('charset', get_bloginfo('charset'));
        $this->assertFalse(is_rtl());
    }

    /**
     * Calls the remaining stub shims; they exist so a theme can run, and the
     * contract is the value they hand back to the theme.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testStubShimReturnValues()
    {
        include $this->shim();

        $this->assertNull(is_search());
        $this->assertNull(is_archive());
        $this->assertTrue(is_home());
        $this->assertTrue(is_singular());
        $this->assertTrue(is_front_page());
        $this->assertFalse(is_attachment());
        $this->assertFalse(has_post_thumbnail());
        $this->assertNull(get_the_archive_title());
        $this->assertNull(get_the_archive_description());
        $this->assertFalse(have_posts());
        $this->assertNull(the_post());
        $this->assertNull(the_excerpt());
        $this->assertSame(1, the_ID());
        $this->assertNull(the_content('x'));
        $this->assertNull(post_class());
        $this->assertTrue(is_sticky());
        $this->assertFalse(is_paged());
        $this->assertSame('[post title]', $this->capture(fn() => the_title('[', ']')));
        $this->assertSame('post title', get_the_title());
        $this->assertNull(wp_link_pages([]));
        $this->assertNull(get_author_posts_url(1));
        $this->assertSame('testuser', get_the_author_meta('ID'));
        $this->assertNull(get_the_author_meta('other'));
        $this->assertNull(get_the_author());
        $this->assertIsInt(get_the_time('U'));
        $this->assertNull(get_the_time('other'));
        $this->assertIsInt(get_the_modified_time('U'));
        $this->assertNull(get_the_modified_time('other'));
        $this->assertIsInt(get_the_date());
        $this->assertIsInt(get_the_modified_date());
        $this->assertSame('', get_permalink());
        $this->assertNull(edit_post_link());
        $this->assertFalse(is_active_sidebar('sidebar-1'));
        $this->assertNull(wp_head());
        $this->assertNull(wp_footer());
        $this->assertNull(body_class());
        $this->assertNull(wp_body_open());
        $this->assertNull(wp_reset_postdata());
        $this->assertNull(wp_kses());
        $this->assertFalse(current_user_can('edit_posts'));
        $this->assertSame('fallback', get_theme_mod('key', 'fallback'));
        $this->assertNull(get_theme_mod('key'));
        // add_action/do_action/add_filter/apply_filters are supplied by the real
        // wp-includes/plugin.php in this process; their shims are covered by
        // WPFunctionsIsolatedTest, which runs without WordPress loaded.
        $this->assertNull(add_editor_style([]));
        $this->assertNull(get_theme_support());
        $this->assertFalse(has_header_image());
        $this->assertNull(the_custom_logo());
        $this->assertTrue(display_header_text());
        $this->assertNull(_ex('t', 'a', 'b'));
        $this->assertNull(get_custom_logo());
        $this->assertFalse(has_custom_logo());
        $this->assertNull(wp_nav_menu([]));
        $this->assertNull(get_terms(['taxonomy' => 'category']));
        $this->assertSame('http://x/', esc_url('http://x/'));
        $this->assertNull(get_home_url());
        $this->assertSame('<b>', esc_html('<b>'));
        $this->assertNull(_e());
        $this->assertSame('v', _x('v'));
        $this->assertNull(has_nav_menu('primary'));
        $this->assertSame('a', esc_attr_x('a'));
        $this->assertSame('a', esc_attr('a'));
        $this->assertNull(wp_list_pages([]));
        $this->assertSame(date('Y'), date_i18n('Y'));
        $this->assertSame('', home_url());
        $this->assertSame('/x', home_url('/x'));
        $this->assertSame('v', __('v'));
        $this->assertSame('page', get_post_type());
        $this->assertTrue(is_page());
        $this->assertFalse(post_password_required());
        $this->assertSame('t', $this->capture(fn() => esc_html_e('t')));
        $this->assertSame('t', esc_html__('t'));
        $this->assertNull(the_posts_pagination(1));
        $this->assertNull(get_search_form());
        $this->assertNull(get_the_posts_pagination());
        $this->assertNull(get_post_format());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testPathAndArgumentHelpers()
    {
        include $this->shim();

        $this->assertSame('/a/b/', trailingslashit('/a/b'));
        $this->assertSame('/a/b/', trailingslashit('/a/b/'));
        $this->assertSame(3, absint(-3));
        $this->assertSame(3, absint('3'));

        $parsed = null;
        $this->assertNull(wp_parse_str('a=1', $parsed));

        // array, object and string inputs, with and without defaults
        $this->assertSame(['a' => 1], wp_parse_args(['a' => 1]));
        $this->assertSame(['b' => 2, 'a' => 1], wp_parse_args(['a' => 1], ['b' => 2]));
        $this->assertSame(['a' => 1], wp_parse_args((object) ['a' => 1]));
        $this->assertNull(wp_parse_args('a=1'));
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testShimClasses()
    {
        include $this->shim();

        $this->assertInstanceOf(\Walker_Comment::class, new \Walker_Comment());
        $this->assertInstanceOf(\Walker_Page::class, new \Walker_Page());
        $this->assertInstanceOf(\Walker_Nav_Menu::class, new \Walker_Nav_Menu());
        $this->assertInstanceOf(\WP_Widget::class, new \WP_Widget());
        $query = new \WP_Query();
        $this->assertFalse($query->have_posts());
    }

    /**
     * @param callable|string $callable the shim to run
     * @return string whatever the shim echoed
     */
    private function capture($callable): string
    {
        ob_start();
        $callable();
        return (string) ob_get_clean();
    }
}
