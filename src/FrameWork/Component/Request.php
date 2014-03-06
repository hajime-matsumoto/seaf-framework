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
use Seaf\Core\Base\Container;

/**
 * リクエストクラス
 */
class Request extends Container
{
    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';

    private $uri,$params,$method,$uri_mask;

    /**
     * 新規リクエストを発行
     */
    public static function newRequest ( $uri = null,  $method = self::METHOD_GET )
    {
        return new self($uri,$method);
    }

    public function __construct ( $uri = null,  $method = self::METHOD_GET)
    {
        $this->setUri( $uri );
        $this->setMethod( $method );

        $this->init( );
    }

    public function init( )
    {
    }

    /**
     * URI取得時に無視してほしいマスクをかける
     *
     * 例)
     *
     * オリジナルリクエスト: /admin/index
     * マスク: /admin
     * getUriの結果: /index
     */
    public function setUriMask($mask)
    {
        $this->uri_mask = rtrim($mask,'/');
    }

    public function setUri( $url )
    {
        $this->uri = $url;
    }

    public function setMethod( $method )
    {
        $this->method = $method;
    }

    /**
     * URIを取得する
     *
     * マスクがかかっていれば処理後の文字列
     * 空になるようであれば'/'を返す
     */
    public function getUri( )
    {
        $uri = $this->uriMask( $this->uri );

        if (empty($uri)) return '/';

        return $uri;
    }

    public function getMethod( )
    {
        return $this->method;
    }

    private function uriMask( $uri )
    {
        $mask = $this->uri_mask;

        if (!empty($mask) && 0 === strpos($uri,$mask)) {
            return substr($uri,strlen($mask));
        }
        return $uri;
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
