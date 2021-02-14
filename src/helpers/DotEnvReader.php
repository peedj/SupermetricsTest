<?php

namespace ApiConnect\helpers;

/**
 *
 * Usage: (new DotEnvReader(__DIR__ . '/.env'))->load();
 * echo getenv('APP_ENV');
 *
 * Class DotEnvReader
 * @package ApiConnect\helpers
 */
class DotEnvReader
{
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected string $envPath;

    /**
     * DotEnvReader constructor.
     * @param string $envPath
     */
    public function __construct(string $envPath)
    {
        if (!file_exists($envPath)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $envPath));
        }
        $this->envPath = $envPath;
    }

    /**
     * Loads Env File
     */
    public function load(): void
    {
        if (!is_readable($this->envPath)) {
            throw new \RuntimeException(sprintf('file not found in %s', $this->envPath));
        }

        $lines = file($this->envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) { // skip commented lines
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}