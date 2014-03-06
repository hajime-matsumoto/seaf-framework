<?php
namespace Seaf\Component\Router\Tests;

use Seaf;
use Seaf\App\App;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateContainer()
    {
        $app = new App;
        $router =  $app->router();
        $this->assertInstanceOf('Seaf\App\Component\Router', $router);
    }

    public function testMap()
    {
        $app = new App;
        $app->router( )->map('/', function( ) {
            return 'hello world';
        });
        $app->router( )->map('/@name', function( $name ) {
            return 'hello world '.$name;
        });

        $req = $app->request( )->newSimpleRequest('/',array(),'GET');

        $route = $app->router( )->route( $req );
        $this->assertEquals('hello world',$route->invoke());

        $req = $app->request( )->newSimpleRequest('/hajime',array(),'GET');
        $route = $app->router( )->route( $req );
        $this->assertEquals('hello world hajime',$route->invoke());
    }

    public function testMount()
    {
        $app1 = new App;
        $app2 = new App;
        $req = $app1->request( )->newSimpleRequest('/',array(),'GET');

        $app1->router( )->map('/', function( ) {
            return 'hello world';
        });
        $app2->router( )->map('/', function( ) {
            return 'hello wild';
        });

        $app1->router( )->mount('/wild', $app2 );

        $route = $app1->router( )->route( $req );
        $this->assertEquals('hello world',$route->invoke());

        $req = $app1->request( )->newSimpleRequest('/wild',array(),'GET');

        $this->assertEquals('hello wild',$app1->run( $req ) );
    }

}
