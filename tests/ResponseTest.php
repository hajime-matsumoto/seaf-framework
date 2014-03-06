<?php
namespace Seaf\FrameWork\Tests;

use Seaf;
use Seaf\FrameWork\Component\Request;
use Seaf\FrameWork\Component\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testRequestTest()
    {
        // レスポンス作成
        $res = new Response( );

        $res->status(300)
            ->header('is-test1','true')
            ->header('is-test2','true')
            ->header('is-test3','true')
            ->header('is-test4','true')
            ->write('hello')
            ->param(array('a'=>'b','c'=>'d'));

        $this->assertTrue(is_array($res->toArray()));
        $this->assertTrue(is_string($res->toJson()));
        ob_start();
    }

}
