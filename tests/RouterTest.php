<?php
namespace Seaf\FrameWork\Tests;

use Seaf;
use Seaf\FrameWork\Component\Request;
use Seaf\FrameWork\Component\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testRouterTest()
    {
        $router = new Router( );
        $this->assertInstanceOf('Seaf\FrameWork\Component\Router',$router);
    }

    public function testRouterMap()
    {
        $router = new Router( );

        $router->map('GET /user/id@id:[0-9]{3}(/@page:*)', function( $id ){
            return $id;
        });
        $router->map('GET /user/*', function( $id ){
            return $id;
        });

        $req = Request::newRequest('/user/id100/abcdef');

        $route = $router->route($req);

        $this->assertTrue( is_object($route) );

        $params = $route->getParams();
        $this->assertEquals('100',$params['id']);

        $router->next( );
        $route = $router->route($req);
        $this->assertTrue( is_object($route) );

        $router->next( );
        $route = $router->route($req);
        $this->assertFalse( is_object($route) );
    }

    public function testRouteAndInvoke ( )
    {
        $router = new Router( );

        $router->map('GET /user/id@id:[0-9]{3}(/@page:*)', function( $id ){
            return $id;
        });

        $req = Request::newRequest('/user/id100/abcdef');
        $route = $router->route($req);

        $result = $route->invoke( );

        $this->assertEquals( '100', $result);
    }

    public function testInvokeWithMethod ( )
    {
        $router = new Router( );

        $router->map('GET /', function( $n1,$n2 ) {
            return $n1 / $n2;
        });

        $req = Request::newRequest('/');
        $route = $router->route($req);

        $result = $route->invoke(4,2);

        $this->assertEquals( 2, $result);
    }

    public function testRestfull ( )
    {
        $router = new Router( );

        $router->map('GET /', function( ){
            return 'GET';
        });
        $router->map('POST /', function( ){
            return 'POST';
        });
        $router->map('PUT /', function( ){
            return 'PUT';
        });
        $router->map('DELETE /', function( ){
            return 'DELETE';
        });

        $req = Request::newRequest('/','GET');
        $route = $router->route($req);
        $result = $route->invoke( );
        $this->assertEquals( 'GET', $result);

        $req = Request::newRequest('/','POST');
        $route = $router->route($req);
        $result = $route->invoke( );
        $this->assertEquals( 'POST', $result);

        $req = Request::newRequest('/','PUT');
        $route = $router->route($req);
        $result = $route->invoke( );
        $this->assertEquals( 'PUT', $result);

        $req = Request::newRequest('/','DELETE');
        $route = $router->route($req);
        $result = $route->invoke( );
        $this->assertEquals( 'DELETE', $result);
    }
}
