<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\FrameWork\Component;

use Seaf;
use Seaf\Core\Environment\Environment;

/**
 * ルータクラス
 */
class Router
{
    private $route_list,$mount_list;
    private $route_list_position;

    public function __construct( )
    {
        $this->init( );
    }

    public function init()
    {
        $this->route_list = array();
        $this->mount_list  = array();
        $this->route_list_position = 0;
    }

    /**
     * ルートを作成する
     * ===================
     *
     * 指定方法
     * ----------------
     * * GET /index
     * * /index
     * * /index/id/@id
     *
     * @param string $pattern
     * @param callback 
     */
    public function map ( $pattern, $callback )
    {
        if (strpos($pattern, ' ') !== false) {
            list($method, $url) = explode( ' ', trim($pattern), 2);
            $methods = explode( '|', $method);
            array_push($this->route_list, new Route($url, $callback, $methods));
        } else {
            array_push($this->route_list, new Route($pattern, $callback, array('*')));
        }
    }

    /**
     * ルートを検出する
     * ===================
     *
     * @param string $pattern
     * @param callback 
     */
    public function route ( Request $req )
    {
        $list = $this->route_list;
        $pos  =& $this->route_list_position;

        while( isset($list[$pos]) ) {
            $route = $list[$pos];
            $isMatch = 
                $route->matchMethod($req->getMethod( )) &&
                $route->matchUri($req->getUri());

            if ( $isMatch ) return $route;

            $pos++;
        }

        return false;
    }

    public function next ()
    {
        $this->route_list_position++;
    }
}

/**
 * ルートクラス
 */
class Route
{
    private $patterm,$methods;
    private $callback;
    private $params = array();

    public function __construct( $pattern, $callback, $methods )
    {
        $this->pattern = $pattern;
        $this->methods = $methods;
        $this->callback = $callback;
    }

    /**
     * メソッドの一致を調べる
     */
    public function matchMethod( $method ) 
    {
        return count(
            array_intersect(
                array($method, '*'),
                $this->methods
            )
        ) > 0;
    }

    /**
     * URIをマッチする
     */
    public function matchUri($uri) 
    {
        if( $this->pattern === "*" || $this->pattern === $uri ) return true;

        $ids = array();

        // パターンの最後一文字を取得
        $char = substr($this->pattern, -1);

        // * で指定されているパターン位置を取得
        $splat = substr($uri, strpos($this->pattern, '*'));

        // ?は量指定子 {0,1}と同等
        $pattern = str_replace(')',')?',$this->pattern);
        $pattern = str_replace('*','.*?',$pattern);

        $param_index = array(); // リスト
        $regex = preg_replace_callback(
            '/@([\w]+)(:([^\/\(\)]*))?/', // パラメータ指定の部分に適用
            function( $m ) use (&$param_index) {
                // 見つかったパラメタを保存
                $param_index[$m[1]] = null;
                // var_dump($m[1]); // @の後にあった文字列
                // var_dump($m[2]); // :を含む:以降の文字列
                // var_dump($m[3]); // :を含まない:以降の文字列
                if (isset($m[3])) {
                    return '(?P<'.$m[1].'>'.$m[3].')'; 
                }
                return '(?P<'.$m[1].'>[^\/?]+)';
            }, $pattern);

        // 指定されたパターンが/で終わる場合
        // あってもなくてもヒットするように正規表現を変更
        $regex .= $char == '/' ? '?': '/?';

        if( preg_match('#^'.$regex.'(?:\?.*)?$#i', $uri, $m) ) {
            $params = array();
            foreach ($param_index as $k=>$v) {
                if (array_key_exists($k, $m)) {
                    $params[$k] = urldecode($m[$k]);
                }else{
                    $params[$k] = null;
                }
            }
            $this->params = $params;
            return true;
        }
        return false;
    }

    public function getParams( )
    {
        return $this->params;
    }

    /**
     * ルータに登録されたコールバックを実行する
     * 引数を与えるとルータパラメタの後から
     * 順に追加される
     */
    public function invoke( )
    {
        $cb = $this->callback;
        $params = $this->params;
        if (func_num_args() > 0) {
            $params += func_get_args();
        }

        return call_user_func_array( $cb, $params );
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
