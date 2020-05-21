<?php

namespace app\hejiang;
/**
 * Hejiang Application
 *
 * @property SentryClient $sentry
 * @property Serializer $serializer
 * @property \Hejiang\Storage\Components\StorageComponent $storage
 * @property \Hejiang\Storage\Components\StorageComponent $storageTemp
 * @property \Hejiang\Event\EventDispatcher $eventDispatcher
 */
class Application extends \yii\web\Application
{
    public function __construct($configFile = '/config/web.php')
    {

        $this->loadDotEnv()
            ->defineConstants();

        $basePath = dirname(__DIR__);
        require $basePath . '/vendor/yiisoft/yii2/Yii.php';

        $this->loadYiiHelpers();
//        var_dump(require $basePath . $configFile);exit;
        parent::__construct(require $basePath . $configFile);

        $this->enableJsonResponse()
            ->enableErrorReporting();

    }

    /**
     * Load .env file
     *
     * @return self
     */
    protected function loadDotEnv()
    {
        try {
            $dotenv = new \Dotenv\Dotenv(dirname(__DIR__));
            $dotenv->load();
        } catch (\Dotenv\Exception\InvalidPathException $ex) {
        }
        return $this;
    }

    /**
     * Define some constants
     *
     * @return self
     */
    protected function defineConstants()
    {
        define_once('WE7_MODULE_NAME', 'zjhj_mall');
        define_once('IN_IA', true);
        $this->defineEnvConstants(['YII_DEBUG', 'YII_ENV']);
        return $this;
    }

    /**
     * Define some constants via `env()`
     *
     * @param array $names
     * @return self
     */
    protected function defineEnvConstants($names = [])
    {
        foreach ($names as $name) {
            if ((!defined($name)) && ($value = env($name))) {
                define($name, $value);
            }
        }
        return $this;
    }

    /**
     * Override yii helper classes
     *
     * @return self
     */
    protected function loadYiiHelpers()
    {
        \Yii::$classMap['yii\helpers\FileHelper'] = '@app/hejiang/FileHelper.php';
        return $this;
    }

    /**
     * Enable JSON response if app returns Array or Object
     *
     * @return self
     */
    protected function enableJsonResponse()
    {
        $this->response->on(
            \yii\web\Response::EVENT_BEFORE_SEND,
            function ($event) {
                /** @var \yii\web\Response $response */
                $response = $event->sender;
                if (is_array($response->data) || is_object($response->data)) {
                    $response->format = \yii\web\Response::FORMAT_JSON;
                }
            }
        );
        return $this;
    }

    /**
     * Enable full error reporting if using debug mode
     *
     * @return self
     */
    protected function enableErrorReporting()
    {
        if (YII_DEBUG) {
            error_reporting(E_ALL ^ E_NOTICE);
        }
        return $this;
    }
}