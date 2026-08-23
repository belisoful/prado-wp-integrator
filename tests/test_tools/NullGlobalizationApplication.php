<?php

/**
 * NullGlobalizationApplication class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator\TestTools;

use Prado\TApplication;

/**
 * An application without globalization.
 *
 * TApplication::getGlobalization() creates a TGlobalization on demand, so the
 * defensive "no globalization" branches in the theme shims are only reachable
 * through an application that returns null.
 */
class NullGlobalizationApplication extends TApplication
{
    /**
     * @param bool $createIfNotExists ignored
     * @return null always null
     */
    public function getGlobalization($createIfNotExists = true)
    {
        return null;
    }
}
