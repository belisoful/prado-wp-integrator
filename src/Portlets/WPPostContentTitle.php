<?php

/**
 * WPPostContentTitle class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator\Portlets;

require 'src/composer.php';

use Prado\Web\UI\WebControls\TLabel;
use Prado\Prado;
use PradoWpIntegrator\WPIntegratorModule;

/**
 * WPPostContentTitle class
 *
 * Displays the title of a WordPress post or page.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @since 0.0.1
 */
class WPPostContentTitle extends TLabel
{
	/**
	 * Gets the post ID.
	 *
	 * @return string The post ID
	 */
	public function getPostId()
	{
		return $this->getViewState('PostId', '');
	}

	/**
	 * Sets the post ID.
	 *
	 * @param string $value The post ID
	 * @return void
	 */
	public function setPostId($value)
	{
		$this->setViewState('PostId', $value, '');
	}

	/**
	 * Writes the title of the WordPress post, when the post may be shown.
	 *
	 * @param \Prado\Web\UI\THtmlWriter $writer the writer used for the rendering output
	 * @return void
	 */
	public function renderContents($writer)
	{
		/** @var WPIntegratorModule $module */
		$module = $this->getPluginModule();
		Prado::trace("Loading WordPress template " . get_class($this), '\Prado\Web\UI\TTemplateControl');

		$post = $module->getWPPost($this->getPostId());

		//post cannot be password protected, must be published,
		if ($post->getStatus() == 'publish' && !$post->getHasPassword() && ($post->getType() == 'post' || $post->getType() == 'page')) {
			$writer->write($post->getTitle());
		}

	}
}
