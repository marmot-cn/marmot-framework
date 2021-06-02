<?php
/**
 * core 核心文件
 *
 * @author  chloroplast1983
 * @version 1.0.20131007
 */
namespace Marmot;

use Marmot\Interfaces\Application\IApplication;
use Marmot\Framework\MockApplication;
use Marmot\Framework\MarmotCore;

/**
 * 文件核心类
 *
 * @author  chloroplast1983
 * @version 1.0.20130916
 */
class Core extends MarmotCore
{
    private static $instance;

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
     */
    public function initTest()
    {
        $this->initAutoload();//autoload
        $this->initFramework();
        $this->initApplication();
        $this->initTestEnv();//初始化测试环境
        $this->initContainer();//引入容器
        $this->initEnv();//初始化环境
        $this->initCache();//初始化缓存使用
        $this->initDb();//初始化mysql
        $this->initError();
    }

    protected function initApplication() : void
    {
        $this->application = new MockApplication();
    }

    protected function getApplication() : IApplication
    {
        return $this->application;
    }

    private function initTestEnv()
    {
        $_ENV['APP_ENV'] = 'test';
    }

    protected function initAutoload()
    {
        include 'vendor/autoload.php';
        //加载框架Application文件的autoload,匿名函数 -- 开始
        spl_autoload_register(
            function ($className) {
                $file = str_replace(['\\','Marmot/Framework/'], ['/',''], $className) . '.php';

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

                //加载 integration 测试文件
                $integrationTestFile = $this->getAppPath().'tests/integration/'.$file;
                if (file_exists($integrationTestFile)) {
                    include_once $integrationTestFile;
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

    protected function getAppPath() : string
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initDb() : void
    {
        parent::initMysql();
    }

    protected function initCache() : void
    {
        parent::initMemcached(self::$container->get('memcached.service'));
    }
}
