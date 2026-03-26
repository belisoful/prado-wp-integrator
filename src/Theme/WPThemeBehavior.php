<?php

/**
 * WPThemeBehavior class file.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/pradosoft/prado
 * @license https://github.com/pradosoft/prado/blob/master/LICENSE
 */

namespace WordPressThemer;

use Prado\Prado;
use Prado\Util\TBehavior;
use Prado\TService;
use Prado\Web\UI\WebControls\THead;
use Prado\Web\UI\TForm;

/**
 * WPThemeBehavior attaches to the TTheme class for doing screen
 * scraping of a WordPress theme and injection into the Page Controls
 * This creates a THead and TForm where needed, acting as a MasterClass
 * when the theme is a WordPress theme.
 *
 * A WordPress theme has 4 files present: style.css, index.php, comments,php
 * and screenshot.png.
 *
 * print.css is automatically made into a print media css.
 *
 * every page just before the TForm.
 * @author Brad Anderson <belisoful@icloud.com>
 * @since 0.0.1
 */
class WPThemeBehavior extends TBehavior
{
	/**
	 * This caches whether or not the specific theme is a wordpress theme
	 */
	private $_isWPTheme;

	/**
	 * @var string the page theme is set to this parameter key
	 */
	private $_placeholderId = "";


	public function attach($owner)
	{
		parent::attach($owner);
		if ($this->isWordPressTheme()) {
			$service = Prado::getApplication()->getService();
			if ($service->isa('\Prado\Web\Services\TPageService')) {
				$service->getRequestedPage()->attachEventHandler('onInitComplete', [$this, 'installWPTheme'], -19.9);
			}
		}
	}
	public function get($field)
	{

	}

	public function processParts()
	{
		$path = $this->getOwner()->getBasePath();
		define('WP_THEME_PATH', $path);
		define('WP_THEME_URI', $this->getOwner()->getBaseUrl());
		define('WP_CONTENT_TAG', '<wpcontent/>');

		$GLOBALS['wp_version'] = '5.7.0';
		$GLOBALS['wp_theme_object'] = $this->getOwner();

		//Include all the theme WP functions that are replicated to grab the html
		include 'WPFunctions.php';
		if (is_file($path . DIRECTORY_SEPARATOR . 'functions.php')) {
			include $path . DIRECTORY_SEPARATOR . 'functions.php';
		}
		ob_start();
		include $path . DIRECTORY_SEPARATOR . 'index.php';
		$page = ob_get_contents();
		ob_end_clean();

		preg_match("/^(.*?)(<head.*?\>)(.*?)(<\/head.*?>)((.*?)(<body.*?>))(.*?)(<wpcontent\/>)(.*?)((<\/body.*?>)(.*))$/sim", $page, $matches);

		$preHeadContent = $matches[1];
		$headContent = $matches[3];
		$preForm = $matches[5];
		$postForm = $matches[8];
		$preEndForm = $matches[10];
		$postEndForm = $matches[11];
		return [$preHeadContent, $headContent, $preForm, $postForm, $preEndForm, $postEndForm];
	}
	/*
	public function dyThemeConstruct($themePath, $themeUrl, $callchain)
	{
		return $callchain->dyThemeConstruct($themePath, $themeUrl);
	}

	public function dyProcessFile($returnValue, $file, $callchain)
	{
		return $callchain->dyProcessFile($returnValue, $file);
	}*/

	public function dyThemeProcess($callchain)
	{
		$stylesheets = $this->getOwner()->getStyleSheetFiles();
		$new = [];
		foreach ($stylesheets as $sheet) {
			$segs = explode('.', basename($sheet));
			// pass through style.css and any rtl. The Theme removes and places rtl
			// css in the proper place, this just needs to filter.
			if (preg_match('/style\.css$/i', $sheet) || (substr_compare(strtolower($sheet), 'rtl.css', -7) === 0) ||
			(isset($segs[2]) && (substr_compare(strtolower($segs[count($segs) - 3]), 'rtl', -3) === 0))) {
				$new[] = $sheet;
			}
		}
		$this->getOwner()->setStyleSheetFiles($new);
		return $callchain->dyThemeProcess();
	}

	public function dyCssMediaType($returnValue, $url, $callchain)
	{
		if (preg_match('/\/print\.css$/', $url)) {
			$returnValue = 'print';
		}
		return $callchain->dyCssMediaType($returnValue, $url);
	}

	/**
	 * This checks the theme for the 4 proper wordpress files present:
	 * style.css, index.php, comments,php, and screenshot.png
	 * @return bool whether or not the theme is a WordPress Theme
	 */
	public function isWordPressTheme()
	{
		if ($this->_isWPTheme !== null) {
			return $this->_isWPTheme;
		}
		$path = $this->getOwner()->getBasePath();
		$this->_isWPTheme = is_file($path . DIRECTORY_SEPARATOR . 'style.css') &&
			is_file($path . DIRECTORY_SEPARATOR . 'index.php') &&
			is_file($path . DIRECTORY_SEPARATOR . 'comments.php') &&
			is_file($path . DIRECTORY_SEPARATOR . 'screenshot.png');
		return $this->_isWPTheme;
	}

	/**
	 * This event is raised after the page is loaded onInitComplete.  This
	 * function reconfigures the page controls around the Wordpress theme.
	 * if there is a THead, and TForm, they are reused.
	 * @param mixed $page
	 * @param mixed $param
	 */
	public function installWPTheme($page, $param)
	{
		$wppage = $this->processParts();
		$ctls = $page->getControls();
		$head = $page->getHead() ?? new THead();
		$form = $page->getForm();

		if (!$form) {
			$form = new TForm();
			$fctls = $form->getControls();
			while (count($ctls)) {
				$fctls->add($ctls->removeAt(0));
			}
		} else {
			$fctls = $form->getControls();
		}
		$fctls->insertAt(0, $wppage[3]);
		$fctls->insertAt(count($fctls), $wppage[4]);

		$ctls->insertAt(0, $wppage[0]);
		$head->addParsedObject($wppage[1]);
		$ctls->insertAt(1, $head);
		$ctls->insertAt(2, $wppage[2]);
		$ctls->insertAt(3, $form);
		$ctls->insertAt(count($ctls), $wppage[5]);
	}


	/**
	 * @return string the top anchor name, defaults to 'top'.
	 */
	public function getContentPlaceHolderId()
	{
		return $this->_placeholderId;
	}

	/**
	 * @param $value string the top anchor name.
	 */
	public function setContentPlaceHolderId($value)
	{
		$this->_placeholderId = $value;
	}
}
