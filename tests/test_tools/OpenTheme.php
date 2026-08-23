<?php

/**
 * OpenTheme class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator\TestTools;

use Prado\Web\UI\TTheme;

/**
 * A theme whose stylesheet list can be written from outside the class.
 *
 * TTheme::setStyleSheetFiles() is protected in pradosoft/prado 4.3.2, so
 * WPThemeBehavior::dyThemeProcess() - which is a behavior, not a subclass -
 * cannot actually apply the filter it computes. Widening the setter here lets
 * the tests exercise that filter; WPThemeBehaviorTest also pins the framework
 * behaviour the widening stands in for.
 */
class OpenTheme extends TTheme
{
    /**
     * @param array $value list of CSS files (URL) in the theme
     */
    public function setStyleSheetFiles($value)
    {
        parent::setStyleSheetFiles($value);
    }
}
