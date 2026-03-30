<?php

/**
 * WPPostContent class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator\Pages\Test;

require '../../composer.php';

use Prado\Web\UI\TPage;

/**
 * WPTest class
 *
 * Test page for WordPress integration functionality.
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @since 0.0.1
 */
class WPTest extends TPage
{
	/**
	 * Handles the control load event.
	 * @param mixed $param event parameter
	 */
	public function onLoad($param)
	{

		parent::onLoad($param);

		if (!$this->getIsPostBack()) {
		}
	}
}
