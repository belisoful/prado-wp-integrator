<?php

/**
 * WPTest class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

use Prado\Web\UI\TPage;

/**
 * WPTest class
 *
 * Example page demonstrating the WordPress content portlets. Copy this file and
 * WPTest.page into your application's page directory (by default `pages/`) and
 * browse to the page to render WordPress post 3 through PRADO.
 *
 * The class is intentionally in the global namespace: TPageService resolves a
 * page class by its file basename, falling back to `Application\Pages\<path>`.
 * A page class in any other namespace is not found.
 *
 * This file is an example and is not part of the package's autoloaded source.
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
