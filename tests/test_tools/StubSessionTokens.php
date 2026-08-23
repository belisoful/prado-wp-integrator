<?php

/**
 * StubSessionTokens class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator\TestTools;

/**
 * Declares a WordPress session token store for the cookie tests.
 *
 * WPUser::createUserFromCookie() finishes by asking WP_Session_Tokens to verify
 * the session token. WP_Session_Tokens::get_instance() instantiates
 * WP_User_Meta_Session_Tokens, which lives in a part of WordPress the CLI
 * bootstrap does not load, so the tests declare a stand-in that answers with a
 * fixed set of sessions.
 */
class StubSessionTokens
{
    /**
     * Declares WP_User_Meta_Session_Tokens, if it is not declared already.
     *
     * @param bool $tokenIsValid whether the store should recognise any token
     */
    public static function declareWith(bool $tokenIsValid): void
    {
        $GLOBALS['prado_wp_test_token_is_valid'] = $tokenIsValid;

        if (class_exists('WP_User_Meta_Session_Tokens', false)) {
            return;
        }

        eval('
        class WP_User_Meta_Session_Tokens extends \WP_Session_Tokens
        {
            protected function get_sessions()
            {
                return $GLOBALS["prado_wp_test_token_is_valid"] ? ["verifier" => ["expiration" => time() + 3600]] : [];
            }

            protected function get_session($verifier)
            {
                return $GLOBALS["prado_wp_test_token_is_valid"] ? ["expiration" => time() + 3600] : null;
            }

            protected function update_session($verifier, $session = null)
            {
            }

            protected function destroy_other_sessions($verifier)
            {
            }

            protected function destroy_all_sessions()
            {
            }
        }');
    }
}
