<?php
namespace Seaf\FrameWork\Tests;

use Seaf;
use Seaf\FrameWork\Component\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testRequestTest()
    {
        // 新規リクエスト発行
        $request = Request::newRequest( );

        $request->setUri('/index',Request::METHOD_GET);

        $this->assertEquals('/index',$request->getUri());
        $this->assertEquals(Request::METHOD_GET,$request->getMethod());
    }

    public function testGetUriWithMask()
    {
        // 新規リクエスト発行
        $request = Request::newRequest( );

        $request->setUriMask('/admin');

        $request->setUri('/admin/index',Request::METHOD_GET);

        $this->assertEquals('/index',$request->getUri());
        $this->assertEquals(Request::METHOD_GET,$request->getMethod());
    }

    public function testCallFromFrontController()
    {
        $req = Seaf::front( )->request( )->newRequest( );
        $this->assertInstanceOf('Seaf\FrameWork\Component\Request',$req);

        $req = Seaf::front( )->newRequest( );
        $this->assertInstanceOf('Seaf\FrameWork\Component\Request',$req);
    }
}
