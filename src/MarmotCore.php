<?php
/**
 * core 核心文件
 *
 * @author  chloroplast1983
 * @version 1.0.20131007
 */

namespace Marmot\Framework;

use Marmot\Framework\Classes\Error;

define('S_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

/**
 * 文件核心类
 *
 * @author  chloroplast1983
 * @version 1.0.20130916
j*/
abstract class MarmotCore
{
    //框架内的容器,这里暂时使用的是第三方的PHP-DI容器
    public static $container;

    //缓存驱动
    public static $cacheDriver;

    //数据库驱动
    public static $dbDriver;

    //mongo驱动
    public static $mongoDriver;

    //上一次错误
    private static $lastError;

    protected static $errorDescriptions;
    
    /**
     * 网站正常启动流程
     */
    public function init()
    {
        //autoload
        self::initAutoload();
        self::initContainer();//引入容器
        self::initCache();//初始化缓存使用
        self::initEnv();//初始化环境
        self::initDb();//初始化mysql
        self::initError();
        self::initRoute();
    }

    /**
     * cli模式专用启动路程,用于引导操作框架的一些操作.
     * 在这里我们要实现如下功能:
     * 1. 自动加载
     * 2. 初始化容器
     * 3. 初始化缓存
     * 4. 初始化测试持久层存储
     */
    public function initCli()
    {
        self::initAutoload();//autoload
        self::initContainer();//引入容器
        self::initEnv();//初始化环境
        self::initCache();//初始化缓存使用
        self::initDb();//初始化mysql
        self::initError();
    }
    
    /**
     * 符合PSR4自动加载规范
     *
     * 1. 加载第三方的autoload,主要是composer管理的第三方依赖
     * 2. 子类继承覆写自己的加载方式
     */
    protected function initAutoload()
    {
        //加载第三方的composer的autoload
        include 'vendor/autoload.php';
    }

    /**
     * 初始化网站运行环境的一些全局变量
     *
     * @author  chloroplast
     * @version 1.0.20131016
     */
    protected function initEnv()
    {
        self::$container->set('time', time());
    }

    /**
     * 初始化错误信息
     */
    protected function initError()
    {
        include_once 'errorConfig.php';
        self::$errorDescriptions = include_once 'errorDescriptionConfig.php';

        self::setLastError(ERROR_NOT_DEFINED);
    }

    public static function setLastError(int $errorCode = 0, array $source = array())
    {

        if (!isset(self::$errorDescriptions[$errorCode])) {
            return false;
        }

        self::$lastError  = new Error(
            $errorCode,
            self::$errorDescriptions[$errorCode]['link'],
            self::$errorDescriptions[$errorCode]['status'],
            self::$errorDescriptions[$errorCode]['code'],
            self::$errorDescriptions[$errorCode]['title'],
            self::$errorDescriptions[$errorCode]['detail'],
            !empty($source) ? $source : self::$errorDescriptions[$errorCode]['source'],
            self::$errorDescriptions[$errorCode]['meta']
        );
    }

    public static function getLastError() : Error
    {
        return self::$lastError ;
    }

    /**
     * 创建容器
     *
     * @author  chloroplast1983
     * @version 1.0.20160215
     */
    protected function initContainer()
    {
        //初始化容器
        $containerBuilder = new \DI\ContainerBuilder();
        //这里我们需要使用annotation,所以开启了此功能
        $containerBuilder->useAnnotations(true);
        //为容器设置缓存
        $containerCache = new \Doctrine\Common\Cache\ArrayCache();
        $containerCache->setNamespace('phpcore');
        $containerBuilder->setDefinitionCache($containerCache);

        $containerBuilder->writeProxiesToFile(true, S_ROOT.'Cache/proxies');
        //为容器设置配置文件
        $containerBuilder->addDefinitions(S_ROOT.'config.'.$_ENV['APP_ENV'].'.php');
        //创建容器
        self::$container = $containerBuilder->build();
    }
    
    /**
     * 路由,需要解决以前随意由个人设置路由的习惯,
     * 而希望能用统一的路由风格来解决这个问题.
     *
     * @version 1.0.20160204
     */
    private function initRoute()
    {
        //创建路由规则,如果对外提供接口考虑token用于验证
        $dispatcher = \FastRoute\cachedDispatcher(
            function (\FastRoute\RouteCollector $each) {
                //添加默认首页路由 -- 开始
                $each->addRoute('GET', '/', ['Home\Controller\IndexController','index']);

                //获取配置好的路由规则
                $routeRules = include S_ROOT.'/Application/routeRules.php';
                foreach ($routeRules as $route) {
                    $each->addRoute($route['method'], $route['rule'], $route['controller']);
                }
            },
            [
                'cacheFile' => S_ROOT. 'Cache/route.cache',
                'cacheDisabled' => self::$container->get('cache.route.disable'),
            ]
        );

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        $controller = ['Home\Controller\IndexController','error'];
        $parameters = [];

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                self::setLastError(ROUTE_NOT_EXIST);
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                // $allowedMethods = $routeInfo[1];
                self::setLastError(METHOD_NOT_ALLOWED);
                break;
            case \FastRoute\Dispatcher::FOUND:
                $controller = $routeInfo[1];
                $parameters = $routeInfo[2];
                break;
        }
        self::$container->call($controller, $parameters);
    }
    
    abstract protected function initDb();
    
    abstract protected function initCache();

    protected function initMysql()
    {
        self::$dbDriver = self::$container->get('Marmot\Framework\Classes\MyPdo');
    }

    protected function initMongo()
    {
        $mongoHost = self::$container->get('mongo.host');

        if (!empty($mongoHost)) {
            self::$mongoDriver = new \MongoDB\Client(
                $mongoHost,
                self::$container->get('mongo.uriOptions'),
                self::$container->get('mongo.driverOptions')
            );
        }
    }

    protected function initMemcached(array $memcachedServices)
    {
        //初始化memcached缓存 -- 开始
        $memcached = new \Memcached();
        $memcached->addServers($memcachedServices);

        self::$cacheDriver = new \Doctrine\Common\Cache\MemcachedCache();
        self::$cacheDriver->setMemcached($memcached);
        self::$cacheDriver->setNamespace('phpcore');
        //初始化memcached缓存 -- 结束
    }
}
