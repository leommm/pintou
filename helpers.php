<?php

/**
 * 强调：此处不要出现 use 语句！
 */

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @param  string  $delimiter
     * @return mixed
     */
    function env($key, $default = null, $delimiter = '')
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (strlen($value) > 1 && str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = substr($value, 1, -1);
        }

        if (strlen($delimiter) > 0) {
            if(strlen($value) == 0) {
                $value = $default;
            } else {
                $value = explode($delimiter, $value);
            }
        }

        return $value;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function str_starts_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function str_ends_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('define_once')) {
    /**
     * Define a const if not exists.
     *
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    function define_once($name, $value = true)
    {
        return defined($name) or define($name, $value);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump the passed variable and end the script.
     *
     * @param  mixed  $arg
     * @return void
     */
    function dd($arg)
    {
        echo "<pre>";
        // http_response_code(500);
        \yii\helpers\VarDumper::dump($arg);
        die(1);
    }
}

function hj_core_version()
{
    static $version = null;
    if ($version) {
        return $version;
    }
    $file = __DIR__ . '/version.json';
    if (!file_exists($file)) {
        throw new Exception('Version not found');
    }
    $res = json_decode(file_get_contents($file), true);
    if (!is_array($res)) {
        throw new Exception('Version cannot be decoded');
    }
    return $version = $res['version'];
}

function hj_pdo_run($sql)
{
    try {
        pdo_query($sql);
        return true;
    } catch (Exception $e) {
        return false;
    }
}



