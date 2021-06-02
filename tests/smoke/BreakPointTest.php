<?php
namespace Smoke;

use PHPUnit\Framework\TestCase;

class BreakPointTest extends TestCase
{
    public function testBreakPoint()
    {
        //检查目录
        $dir = "/var/www/html/src";
        $files = [];

        $this->fetchFiles($dir, $files);

        foreach ($files as $file) {
            if ($this->isExistExit($file)) {
                $this->assertFalse(true, $file.': file exist exit');
            }

            if ($this->isExistVardump($file)) {
                $this->assertFalse(true, $file.': file exist var_dump');
            }

            if ($this->isExistEcho($file)) {
                $this->assertFalse(true, $file.': file exist echo');
            }

            if ($this->isExistPrintr($file)) {
                $this->assertFalse(true, $file.': file exist print_r');
            }
        }

        $this->assertTrue(true, 'file break point test pass');
    }

    //递归检查文件
    public function fetchFiles($path, &$files)
    {
        $dirHandler = openDir($path);
     
        while (false !== $file=readDir($dirHandler)) {
            if ($file=='.' || $file=='..') {
                continue;
            }
     
            //判断当前是否为目录
            if (is_dir($path.'/'.$file)) {
                //是目录
                $this->fetchFiles($path.'/'.$file, $files);
            }
            
            if (is_file($path.'/'.$file)) {
                 $files[] = $path.'/'.$file;
            }
        }
     
        closeDir($dirHandler);
    }

    //检查是否存在 exit 标签
    private function isExistExit($file)
    {
        $exceptFiles= [
            '/var/www/html/src/Home/Controller/IndexController.php'
        ];

        if (in_array($file, $exceptFiles)) {
            return false;
        }

        $content = file_get_contents($file);

        $matches = preg_match('/(exit;|exit\(\);)/', $content);
        return $matches;
    }

    //检查是否存在 var_dump 标签
    private function isExistVardump($file)
    {
        $exceptFiles= [
            '/var/www/html/src/Home/Controller/IndexController.php'
        ];

        if (in_array($file, $exceptFiles)) {
            return false;
        }
        
        $content = file_get_contents($file);

        $matches = preg_match('/(var_dump\()/', $content);
        return $matches;
    }

    //检查是否存在 echo 标签
    private function isExistEcho($file)
    {
        $exceptFiles= [
            '/var/www/html/src/Home/Controller/IndexController.php',
            '/var/www/html/src/Home/Controller/HealthzController.php'
        ];

        if (in_array($file, $exceptFiles)) {
            return false;
        }

        $content = file_get_contents($file);

        $matches = preg_match('/(echo)/', $content);
        return $matches;
    }

    //检查是否存在 print_r 标签
    private function isExistPrintr($file)
    {
        $content = file_get_contents($file);

        $matches = preg_match('/(print_r\()/', $content);
        return $matches;
    }
}
