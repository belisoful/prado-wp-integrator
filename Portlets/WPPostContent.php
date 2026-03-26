<?php

/**
 * WPPostContent class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator\Portlets;

require '../src/composer.php';

use Prado\Web\UI\TTemplateControl;
use Prado\TPropertyValue;
use Prado\Prado;
use PradoWpIntegrator\WPTemplate;

/**
 * WPPostContent class
 *
 * Displays the content of a WordPress post or page.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @since 0.0.1
 */
class WPPostContent extends TTemplateControl
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
	 * Loads the template associated with this control class.
	 *
	 * @return \Prado\Web\UI\ITemplate the parsed template structure
	 */
	protected function loadTemplate()
	{
		$module = $this->getPluginModule();
		Prado::trace("Loading WordPress template " . get_class($this), '\Prado\Web\UI\TTemplateControl');

		$post = $module->getWPPost($this->getPostId());
		$template = null;

		//post cannot be password protected, must be published,
		if ($post->getStatus() == 'publish' && !$post->getHasPassword() && ($post->getType() == 'post' || $post->getType() == 'page')) {
			$template = new WPTemplate($post->getContent(), null);
		}

		return $template;
	}

	/**
	 * Handles the control load event.
	 *
	 * @param mixed $param event parameter
	 * @return void
	 */
	public function onLoad($param)
	{

	}
}
