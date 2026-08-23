<?php

/**
 * ChildProcess class file
 *
 * @author Brad Anderson <belisoful@icloud.com>
 * @link https://github.com/belisoful/prado-wp-integrator
 * @license https://github.com/belisoful/prado-wp-integrator/blob/master/LICENSE
 */

namespace PradoWpIntegrator\TestTools;

/**
 * Runs a PHP script in a child process through child-bootstrap.php.
 *
 * Used for behaviour that cannot be observed in the PHPUnit process, such as
 * the theme shims that WordPress would otherwise supply and the include-time
 * WordPress-directory resolution in src/composer.php. The child records its own
 * coverage when PRADO_WP_COVERAGE_DIR is set, and coverage.php merges it.
 */
class ChildProcess
{
    /**
     * Runs a script and returns its exit status and output.
     *
     * @param string $code the php code to run, without the opening tag
     * @param array $env extra environment variables for the child
     * @return array{status: int, output: string} the child result
     */
    public static function run(string $code, array $env = []): array
    {
        $root = dirname(__DIR__, 2);
        $stub = tempnam(sys_get_temp_dir(), 'prado-wp-child-');
        $script = $stub . '.php';
        file_put_contents($script, "<?php\n" . $code);

        $prefix = '';
        foreach ($env as $name => $value) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
                throw new \InvalidArgumentException('Invalid environment variable name: ' . $name);
            }
            $prefix .= $name . '=' . escapeshellarg((string) $value) . ' ';
        }
        $command = $prefix . escapeshellarg(PHP_BINARY) . ' '
            . escapeshellarg($root . '/tests/test_tools/child-bootstrap.php') . ' '
            . escapeshellarg($script) . ' 2>&1';

        $output = [];
        $status = 0;
        exec($command, $output, $status);
        @unlink($script);
        @unlink($stub);

        return ['status' => $status, 'output' => trim(implode("\n", $output))];
    }
}
