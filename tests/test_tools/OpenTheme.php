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
 * TTheme::setStyleSheetFiles() is protected - still the case on pradosoft/prado
 * master (4.4.0-dev, checked 2026-10-07) - so WPThemeBehavior::dyThemeProcess(),
 * which is a behavior and not a subclass, cannot actually apply the filter it
 * computes. Widening the setter here lets the tests exercise that filter;
 * WPThemeBehaviorTest::testTheFilterCannotBeAppliedToAPlainTheme() pins the
 * framework behaviour the widening stands in for.
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
