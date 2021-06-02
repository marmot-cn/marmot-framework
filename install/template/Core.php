<?php
/**
 * core 核心文件
 *
 * @author  chloroplast1983
 * @version 1.0.20131007
 */

namespace Marmot;

use Marmot\Framework\Classes\Error;
use Marmot\Framework\MarmotCore;
use Marmot\Interfaces\Application\IApplication;

use Marmot\Application\Application;

define('APP_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * 文件核心类
 *
 * @author  chloroplast1983
 * @version 1.0.20130916
 */
class Core extends MarmotCore
{
    protected static $instance;

    protected $application;

    /**
     * 使用单例封装全局函数的core调用
     */
    public static function &getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 单元测试专用启动路程,用于引导phpunit,bootstrap的路由文件进入.
     * 在这里我们要实现如下功能:
     * 1. 自动加载
     * 2. 初始化容器
     * 3. 初始化缓存
     * 4. 初始化测试持久层存储,用于测试数据库和程序分离
     */
    public function initTest()
    {
        $this->initTestEnv();//初始化测试环境
        $this->initCli();
        

        parent::initMysql();
        parent::initMemcached(self::$container->get('memcached.service'));
    }
    
    protected function initApplication() : void
    {
        $this->application = new Application();
    }

    protected function getApplication() : IApplication
    {
        return $this->application;
    }

    /**
     * 自动加载方法,这里分为2个部分.这里的借鉴了yii框架的自动加载
     * 符合PSR4自动加载规范
     *
     * 1. 加载第三方的autoload,主要是composer管理的第三方依赖
     * 2. 核心框架自己的autoload
     *    2.1 核心文件主要是通过映射关系载入的,原来使用的是淘宝的一套开源的autoload,
     *        但是考虑其功能过于繁重,这里改为用文件映射
     *    2.2 应用文件(Application)主要是通过命名规则映射
     */
    protected function initAutoload()
    {
        include 'vendor/autoload.php';

        //加载框架Application文件的autoload,匿名函数 -- 开始
        spl_autoload_register(
            function ($className) {
                $file = str_replace(['\\','Marmot/Application/', 'Base/'], ['/','',''], $className) . '.php';

                $classFile = $this->getAppPath().'src/'.$file;
                if (file_exists($classFile)) {
                    include_once $classFile;
                }

                //加载 ut 测试文件
                $unitTestFile = $this->getAppPath().'tests/ut/src/'.$file;
                if (file_exists($unitTestFile)) {
                    include_once $unitTestFile;
                }

                //加载 bdd 测试文件
                $unitTestFile = $this->getAppPath().'tests/bdd/'.$file;
                if (file_exists($unitTestFile)) {
                    include_once $unitTestFile;
                }

                //加载mock文件
                $mockFile = $this->getAppPath().'tests/mock/'.$file;
                if (file_exists($mockFile)) {
                    include_once $mockFile;
                }
            }
        );
        //加载框架Application文件的autoload,匿名函数 -- 开始
    }

    private function initTestEnv()
    {
        $_ENV['APP_ENV'] = 'test';
    }

    protected function getAppPath() : string
    {
        return APP_ROOT;
    }

    /**
     * 初始化数据库
     *
     * @version 1.0.20160204
     */
    protected function initDb()
    {
        parent::initMysql();
    }

    protected function initCache()
    {
        parent::initMemcached(self::$container->get('memcached.service'));
    }
}
