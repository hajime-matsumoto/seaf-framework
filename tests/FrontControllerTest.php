<?php
namespace Seaf\FrameWork\Tests;

use Seaf;

class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testFrontControllr()
    {
        // フロントコントローラの取得
        $fc = Seaf::front( );
        $fc->request()->setUri('/');

        // ルーティング
        $fc->router( )->map('/', function ($req,$res,$fc) {
            $fc->set('key','val');
            $res->param('template','index');
        });

        $fc->run( );


        $result = $fc->response()->toArray();
        var_dump($result);

        $this->assertEquals('index', $result['params']['template']);
    }
}
