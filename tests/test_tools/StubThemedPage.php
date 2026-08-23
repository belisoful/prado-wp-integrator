<?php

/**
 * StubThemedPage class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator\TestTools;

/**
 * A stand-in for a themed page.
 *
 * WPThemeModule::installWPTheme() only asks a page for its theme and sets its
 * master class. A real TPage resolves its theme through the page service's
 * theme manager, which needs a running request, so the tests hand it this
 * instead.
 */
class StubThemedPage
{
    /** @var mixed the theme the page reports */
    private $theme;

    /** @var null|string the master class the page was given */
    private $masterClass;

    /**
     * @param mixed $theme the theme to report, or null for an unthemed page
     */
    public function __construct($theme = null)
    {
        $this->theme = $theme;
    }

    /**
     * @return mixed the page theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return null|string the master class, null when it was never set
     */
    public function getMasterClass()
    {
        return $this->masterClass;
    }

    /**
     * @param string $value the master class
     */
    public function setMasterClass($value)
    {
        $this->masterClass = $value;
    }
}
