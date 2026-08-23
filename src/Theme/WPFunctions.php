<?php

use Prado\Prado;

/**
 * WPFunctions - WordPress theme function shims.
 *
 * No defined namespace - Default for Wordpress
 * These are WordPress simulation methods to process a
 * WP theme without WordPress.
 *
 * Every declaration is guarded with function_exists()/class_exists() so that a
 * real WordPress installation always wins. This matters when WordPress is
 * loaded in the same request (through the module's WPDirectory), and when only
 * part of WordPress is loaded - the CLI bootstrap in src/composer.php includes
 * wp-includes/plugin.php, which already defines add_filter(), apply_filters(),
 * add_action() and do_action(). The file is safe to include more than once.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @since 0.0.1
 */
if (!function_exists('get_template_directory')) {
	function get_template_directory()
	{
		if (defined('WP_THEME_PATH')) {
			return WP_THEME_PATH;
		}
		return '';
	}
}
if (!function_exists('get_template_directory_uri')) {
	function get_template_directory_uri()
	{
		if (defined('WP_THEME_URI')) {
			return WP_THEME_URI;
		}
		return '';
	}
}

if (!function_exists('get_header')) {
	function get_header()
	{
		if (defined('WP_THEME_PATH')) {
			include WP_THEME_PATH . DIRECTORY_SEPARATOR . 'header.php';
		}
		return '';
	}
}


if (!function_exists('get_footer')) {
	function get_footer()
	{
		if (defined('WP_THEME_PATH')) {
			include WP_THEME_PATH . DIRECTORY_SEPARATOR . 'footer.php';
		}
		return '';
	}
}
if (!function_exists('get_sidebar')) {
	function get_sidebar()
	{
		if (defined('WP_THEME_PATH')) {
			include WP_THEME_PATH . DIRECTORY_SEPARATOR . 'sidebar.php';
		}
		return '';
	}
}


if (!function_exists('is_search')) {
	function is_search()
	{
	}
}


if (!function_exists('is_archive')) {
	function is_archive()
	{
	}
}


if (!function_exists('is_home')) {
	function is_home()
	{
		return true;
	}
}
if (!function_exists('is_singular')) {
	function is_singular()
	{
		return true;
	}
}


if (!function_exists('is_front_page')) {
	function is_front_page()
	{
		return true;
	}
}

if (!function_exists('is_attachment')) {
	function is_attachment()
	{
		return false;
	}
}

if (!function_exists('has_post_thumbnail')) {
	function has_post_thumbnail()
	{
		return false;
	}
}


if (!function_exists('get_the_archive_title')) {
	function get_the_archive_title()
	{
	}
}


if (!function_exists('get_the_archive_description')) {
	function get_the_archive_description()
	{
	}
}

if (!function_exists('have_posts')) {
	function have_posts()
	{
		// This is where the main content is inserted
		if (defined('WP_CONTENT_TAG')) {
			//echo (WP_CONTENT_TAG);
		}
		return false;
	}
}

if (!function_exists('the_post')) {
	function the_post()
	{
	}
}

if (!function_exists('the_excerpt')) {
	function the_excerpt()
	{
	}
}

if (!function_exists('get_template_part')) {
	function get_template_part($file, $style = '')
	{
		if (! defined('WP_THEME_PATH')) {
			return '';
		}

		// as in WordPress, the styled template is preferred and the plain
		// template is the fallback.
		if ($style && is_file(WP_THEME_PATH . DIRECTORY_SEPARATOR . $file . '-' . $style . '.php')) {
			include(WP_THEME_PATH . DIRECTORY_SEPARATOR . $file . '-' . $style . '.php');
		} elseif (is_file(WP_THEME_PATH . DIRECTORY_SEPARATOR . $file . '.php')) {
			include(WP_THEME_PATH . DIRECTORY_SEPARATOR . $file . '.php');
		}
	}
}

if (!function_exists('language_attributes')) {
	function language_attributes()
	{
		$globalization = Prado::getApplication()->getGlobalization();
		if ($globalization) {
			echo('lang="' . $globalization->getCulture() . '"');
		}
	}
}

if (!function_exists('wp_get_theme')) {
	function wp_get_theme()
	{
		return $GLOBALS['wp_theme_object'];
	}
}

if (!function_exists('get_bloginfo')) {
	function get_bloginfo($key)
	{
		if ($key == 'name') {
			return Prado::getApplication()->getParameters()->itemAt('blogname');
		}
		if ($key == 'description') {
			return Prado::getApplication()->getParameters()->itemAt('blogdescription');
		}
		if ($key == 'charset') {
			if ($globalization = Prado::getApplication()->getGlobalization()) {
				return $globalization->getCharset();
			}
		}
		return $key;
	}
}

if (!function_exists('bloginfo')) {
	function bloginfo($key)
	{
		echo get_bloginfo($key);
	}
}
if (!function_exists('the_ID')) {
	function the_ID()
	{
		return 1;
	}
}
if (!function_exists('the_content')) {
	function the_content($content)
	{
	}
}
if (!function_exists('post_class')) {
	function post_class()
	{
	}
}
if (!function_exists('is_sticky')) {
	function is_sticky()
	{
		return true;
	}
}
if (!function_exists('is_paged')) {
	function is_paged()
	{
		return false;
	}
}
if (!function_exists('the_title')) {
	function the_title($pre, $post)
	{
		echo($pre . 'post title' . $post);
	}
}
if (!function_exists('get_the_title')) {
	function get_the_title()
	{
		return 'post title';
	}
}
if (!function_exists('wp_link_pages')) {
	function wp_link_pages($attributes)
	{ //['before'=>'<div class="cl">', 'after' => '</div>']
	}
}
if (!function_exists('get_author_posts_url')) {
	function get_author_posts_url($author)
	{
		//return url of the author
	}
}
if (!function_exists('get_the_author_meta')) {
	function get_the_author_meta($type)
	{
		if ($type == 'ID') {
			return 'testuser';
		}
	}
}
if (!function_exists('get_the_author')) {
	function get_the_author()
	{
		//return author of the post
	}
}

if (!function_exists('get_the_time')) {
	function get_the_time($style)
	{
		if ($style == 'U') {
			return time();
		}
	}
}

if (!function_exists('get_the_modified_time')) {
	function get_the_modified_time($style)
	{
		if ($style == 'U') {
			return time();
		}
	}
}
if (!function_exists('get_the_date')) {
	function get_the_date()
	{
		return time();
	}
}
if (!function_exists('get_the_modified_date')) {
	function get_the_modified_date()
	{
		return time();
	}
}
if (!function_exists('get_permalink')) {
	function get_permalink()
	{
		return '';
	}
}
if (!function_exists('edit_post_link')) {
	function edit_post_link()
	{

	}
}
if (!function_exists('is_active_sidebar')) {
	function is_active_sidebar($sidebar)
	{
		return false;
	}
}



if (!function_exists('wp_head')) {
	function wp_head()
	{
	}
}

if (!function_exists('wp_footer')) {
	function wp_footer()
	{
	}
}

if (!function_exists('body_class')) {
	function body_class()
	{
	}
}

if (!function_exists('wp_body_open')) {
	function wp_body_open()
	{
	}
}
if (!function_exists('wp_reset_postdata')) {
	function wp_reset_postdata()
	{
	}
}

if (!function_exists('wp_kses')) {
	function wp_kses()
	{
	}
}

if (!function_exists('current_user_can')) {
	function current_user_can($can)
	{
		return false;
	}
}

if (!function_exists('get_theme_mod')) {
	function get_theme_mod($key, $default = null)
	{
		return $default;
	}
}

if (!function_exists('add_action')) {
	function add_action($action, $method)
	{
	}
}

if (!function_exists('do_action')) {
	function do_action($action)
	{
	}
}

if (!function_exists('add_filter')) {
	function add_filter($filter, $method)
	{
	}
}

if (!function_exists('apply_filters')) {
	function apply_filters($filter, $data, ...$args)
	{
		return $data;
	}
}

if (!class_exists('Walker_Comment', false)) {
	class Walker_Comment
	{
	}
}
if (!class_exists('Walker_Page', false)) {
	class Walker_Page
	{
	}
}
if (!class_exists('Walker_Nav_Menu', false)) {
	class Walker_Nav_Menu
	{
	}
}
if (!class_exists('WP_Widget', false)) {
	class WP_Widget
	{
	}
}
if (!class_exists('WP_Query', false)) {
	class WP_Query
	{
		public function have_posts()
		{
			return false;
		}
	}
}

if (!function_exists('add_editor_style')) {
	function add_editor_style($arrayStyles)
	{
	}
}
if (!function_exists('get_theme_support')) {
	function get_theme_support()
	{
	}
}
if (!function_exists('has_header_image')) {
	function has_header_image()
	{
		return false;
	}
}
if (!function_exists('the_custom_logo')) {
	function the_custom_logo()
	{
	}
}
if (!function_exists('display_header_text')) {
	function display_header_text()
	{
		return true;
	}
}

if (!function_exists('trailingslashit')) {
	function trailingslashit($path)
	{
		return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}
}

if (!function_exists('_ex')) {
	function _ex($text, $a, $b)
	{
	}
}

if (!function_exists('get_custom_logo')) {
	function get_custom_logo()
	{
	}
}
if (!function_exists('wp_parse_str')) {
	function wp_parse_str($args, &$parsed_args)
	{
	}
}

if (!function_exists('wp_parse_args')) {
	function wp_parse_args($args, $defaults = [])
	{
		if (is_object($args)) {
			$parsed_args = get_object_vars($args);
		} elseif (is_array($args)) {
			$parsed_args = & $args;
		} else {
			$parsed_args = null;
			wp_parse_str($args, $parsed_args);
		}

		if (is_array($defaults) && $defaults) {
			return array_merge($defaults, $parsed_args);
		}
		return $parsed_args;
	}
}

if (!function_exists('has_custom_logo')) {
	function has_custom_logo()
	{
		return false;
	}
}
if (!function_exists('wp_nav_menu')) {
	function wp_nav_menu($arr)
	{

	}
}
if (!function_exists('absint')) {
	function absint($value)
	{
		return (int) (abs($value));
	}
}

if (!function_exists('get_terms')) {
	function get_terms($searchTerms)
	{
		return null;
	}
}

if (!function_exists('esc_url')) {
	function esc_url($url)
	{
		return $url;
	}
}
if (!function_exists('get_home_url')) {
	function get_home_url()
	{
	}
}
if (!function_exists('esc_html')) {
	function esc_html($html)
	{
		return $html;
	}
}

if (!function_exists('_e')) {
	function _e()
	{
	}
}

if (!function_exists('_x')) {
	function _x($value)
	{
		return $value;
	}
}

if (!function_exists('has_nav_menu')) {
	function has_nav_menu($menu)
	{
	}
}
if (!function_exists('esc_attr_x')) {
	function esc_attr_x($attr)
	{
		return $attr;
	}
}
if (!function_exists('esc_attr')) {
	function esc_attr($attr)
	{
		return $attr;
	}
}
if (!function_exists('wp_list_pages')) {
	function wp_list_pages($array)
	{

	}
}
if (!function_exists('date_i18n')) {
	function date_i18n($date)
	{
		return date($date);
	}
}
if (!function_exists('home_url')) {
	function home_url($url = '')
	{
		return $url;
	}
}
if (!function_exists('__')) {
	function __($value)
	{
		return $value;
	}
}

if (!function_exists('get_post_type')) {
	function get_post_type()
	{
		return 'page';
	}
}
if (!function_exists('is_page')) {
	function is_page()
	{
		return get_post_type() == 'page';
	}
}
if (!function_exists('post_password_required')) {
	function post_password_required()
	{
		return false;
	}
}

if (!function_exists('esc_html_e')) {
	function esc_html_e($text)
	{
		echo($text);
	}
}
if (!function_exists('esc_html__')) {
	function esc_html__($text)
	{
		return $text;
	}
}

if (!function_exists('the_posts_pagination')) {
	function the_posts_pagination($value)
	{
	}
}

if (!function_exists('is_rtl')) {
	function is_rtl()
	{
		$globalization = Prado::getApplication()->getGlobalization();
		if (!$globalization) {
			return false;
		}
		$o = new \Prado\I18N\core\CultureInfo($globalization->getCulture());
		$textDirection = $o->findInfo('layout/characters');
		return $textDirection == 'right-to-left';
	}
}

if (!function_exists('get_search_form')) {
	function get_search_form()
	{
	}
}

if (!function_exists('get_the_posts_pagination')) {
	function get_the_posts_pagination()
	{
	}
}

if (!function_exists('get_post_format')) {
	function get_post_format()
	{
	}
}
