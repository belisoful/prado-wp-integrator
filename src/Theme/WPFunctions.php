<?php

use Prado\Prado;

/**
 * No defined namespace - Default for Wordpress
 * These are WordPress simulation methods to process a
 * WP theme without WordPress.
 */
function get_template_directory()
{
	if (defined('WP_THEME_PATH')) {
		return WP_THEME_PATH;
	}
	return '';
}
function get_template_directory_uri()
{
	if (defined('WP_THEME_URI')) {
		return WP_THEME_URI;
	}
	return '';
}

function get_header()
{
	if (defined('WP_THEME_PATH')) {
		include WP_THEME_PATH . DIRECTORY_SEPARATOR . 'header.php';
	}
	return '';
}


function get_footer()
{
	if (defined('WP_THEME_PATH')) {
		include WP_THEME_PATH . DIRECTORY_SEPARATOR . 'footer.php';
	}
	return '';
}
function get_sidebar()
{
	if (defined('WP_THEME_PATH')) {
		include WP_THEME_PATH . DIRECTORY_SEPARATOR . 'sidebar.php';
	}
	return '';
}


function is_search()
{
}


function is_archive()
{
}


function is_home()
{
	return true;
}
function is_singular()
{
	return true;
}


function is_front_page()
{
	return true;
}

function is_attachment()
{
	return false;
}

function has_post_thumbnail()
{
	return false;
}


function get_the_archive_title()
{
}


function get_the_archive_description()
{
}

function have_posts()
{
	// This is where the main content is inserted
	if (defined('WP_CONTENT_TAG')) {
		//echo (WP_CONTENT_TAG);
	}
	return false;
}

function the_post()
{
}

function the_excerpt()
{
}

function get_template_part($file, $style = '')
{
	if ($style) {
		$file .= '-' . $style;
	}

	if (! defined('WP_THEME_PATH')) {
		return '';
	}

	if (is_file(WP_THEME_PATH . DIRECTORY_SEPARATOR . $file . '-' . $style . '.php')) {
		include(WP_THEME_PATH . DIRECTORY_SEPARATOR . $file . '-' . $style . '.php');
	} elseif (is_file(WP_THEME_PATH . DIRECTORY_SEPARATOR . $file . '.php')) {
		include(WP_THEME_PATH . DIRECTORY_SEPARATOR . $file . '.php');
	}
}

function language_attributes()
{
	$globalization = Prado::getApplication()->getGlobalization();
	if ($globalization) {
		echo('lang="' . $globalization->getCulture() . '"');
	}
}

function wp_get_theme()
{
	return $GLOBALS['wp_theme_object'];
}

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

function bloginfo($key)
{
	echo get_bloginfo($key);
}
function the_ID()
{
	return 1;
}
function the_content($content)
{
}
function post_class()
{
}
function is_sticky()
{
	return true;
}
function is_paged()
{
	return false;
}
function the_title($pre, $post)
{
	echo($pre . 'post title' . $post);
}
function get_the_title()
{
	return 'post title';
}
function wp_link_pages($attributes)
{ //['before'=>'<div class="cl">', 'after' => '</div>']
}
function get_author_posts_url($author)
{
	//return url of the author
}
function get_the_author_meta($type)
{
	if ($type == 'ID') {
		return 'testuser';
	}
}
function get_the_author()
{
	//return author of the post
}

function get_the_time($style)
{
	if ($style == 'U') {
		return time();
	}
}

function get_the_modified_time($style)
{
	if ($style == 'U') {
		return time();
	}
}
function get_the_date()
{
	return time();
}
function get_the_modified_date()
{
	return time();
}
function get_permalink()
{
	return '';
}
function edit_post_link()
{

}
function is_active_sidebar($sidebar)
{
	return false;
}



function wp_head()
{
}

function wp_footer()
{
}

function body_class()
{
}

function wp_body_open()
{
}
function wp_reset_postdata()
{
}

function wp_kses()
{
}

function current_user_can($can)
{
	return false;
}

function get_theme_mod($key, $default = null)
{
	return $default;
}

function add_action($action, $method)
{
}

function do_action($action)
{
}

function add_filter($filter, $method)
{
}

function apply_filters($filter, $data)
{
	return $data;
}

class Walker_Comment
{
}
class Walker_Page
{
}
class Walker_Nav_Menu
{
}
class WP_Widget
{
}
class WP_Query
{
	public function have_posts()
	{
		return false;
	}
}

function add_editor_style($arrayStyles)
{
}
function get_theme_support()
{
}
function has_header_image()
{
	return false;
}
function the_custom_logo()
{
}
function display_header_text()
{
	return true;
}

function trailingslashit($path)
{
	return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
}

function _ex($text, $a, $b)
{
}

function get_custom_logo()
{
}
function wp_parse_str($args, &$parsed_args)
{
}

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

function has_custom_logo()
{
	return false;
}
function wp_nav_menu($arr)
{

}
function absint($value)
{
	return (int) (abs($value));
}

function get_terms($searchTerms)
{
	return null;
}

function esc_url($url)
{
	return $url;
}
function get_home_url()
{
}
function esc_html($html)
{
	return $html;
}

function _e()
{
}

function _x($value)
{
	return $value;
}

function has_nav_menu($menu)
{
}
function esc_attr_x($attr)
{
	return $attr;
}
function esc_attr($attr)
{
	return $attr;
}
function wp_list_pages($array)
{

}
function date_i18n($date)
{
	return date($date);
}
function home_url($url = '')
{
	return $url;
}
function __($value)
{
	return $value;
}

function get_post_type()
{
	return 'page';
}
function is_page()
{
	return get_post_type() == 'page';
}
function post_password_required()
{
	return false;
}

function esc_html_e($text)
{
	echo($text);
}
function esc_html__($text)
{
	return $text;
}

function the_posts_pagination($value)
{
}

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

function get_search_form()
{
}

function get_the_posts_pagination()
{
}

function get_post_format()
{
}
