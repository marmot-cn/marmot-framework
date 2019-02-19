<?php
/**
 * core 核心文件
 *
 * @author  chloroplast1983
 * @version 1.0.20131007
 */

namespace Marmot;

/**
 * 文件核心类
 *
 * @author  chloroplast1983
 * @version 1.0.20130916
 */
class Core extends \Marmot\Framework\MarmotCore
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
        self::initAutoload();//autoload
        self::initTestEnv();//初始化测试环境
        self::initContainer();//引入容器
        self::initEnv();//初始化环境
        self::initCache();//初始化缓存使用
        self::initDb();//初始化mysql
        self::initError();
    }
    
    private function initTestEnv()
    {
        $_ENV['APP_ENV'] = 'test';
    }

    protected function initAutoload()
    {
        parent::initAutoload();

        //加载框架Application文件的autoload,匿名函数 -- 开始
        spl_autoload_register(
            function ($className) {
                $classFile = str_replace(['\\','Marmot/Framework/'], ['/',''], $className) . '.php';
                $classFile = $this->getAppPath().'src/'.$classFile;
                if (file_exists($classFile)) {
                      include_once $classFile;
                }
            }
        );

        spl_autoload_register(
            function ($className) {
                $classFile = str_replace(['\\','Marmot/Framework/'], ['/',''], $className) . '.php';
                $classFile = $this->getAppPath().'tests/mock/'.$classFile;
                if (file_exists($classFile)) {
                    include_once $classFile;
                }
            }
        );
    }

    protected function getAppPath() : string
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initDb()
    {
    }

    protected function initCache()
    {
    }
}
