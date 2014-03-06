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

namespace Seaf\FrameWork;

use Seaf;
use Seaf\Core\Environment\Environment;
use Seaf\Core\Environment\EnvironmentAcceptableIF;
use Seaf\FramwWork\Component\Request;

/**
 * FramwWork Front Controller
 */
class FrontController implements EnvironmentAcceptableIF
{
    /**
     * environment
     */
    protected $environment;

    /**
     * parent environment
     */
    protected $parent_environment = null;

    /**
     * フレームワーク
     */
    public function __construct ( )
    {
        $this->init( );
    }
    
    /**
     * 親Environmentから引き継ぐもの
     */
    public function acceptEnvironment (Environment $env)
    {
        $this->parent_environment = $env;

        $this->environment->register(
            'config',
            function () use ($env){
                return $env->di('config');
            }
        );
    }

    public function init ( )
    {
        $this->environment = new Environment( );

        if ($this->parent_environment instanceof Environment) {
            $this->acceptEnvironment( $this->parent_environment );
        }

        // フレームワークコンポーネントを読み込み可能にする

        $parent_class = get_parent_class($this);
        if (!empty($parent_class)) {
            $ns = Seaf::util()->getNameSpace($parent_class);
            $this->environment->addComponentNamespace($ns.'\\Component');
        }

        $ns = Seaf::util()->getNameSpace($this);
        $this->environment->addComponentNamespace($ns.'\\Component');

        $this->map(
            'newRequest',
            array(__NAMESPACE__.'\\Component\\Request','newRequest'
        ));
    }

    // フレームワークを実行する
    public function run( )
    {
        $isDispatched = false;
        $req    = $this->request( ); // リクエストを取得
        $router = $this->router( ); // ルーターを取得
        $res    = $this->response( ); //レスポンスを取得
        $event  = $this->event( ); // イベントコントローラ

        // ディスパッチループ開始
        $event->trigger('before.dispatch-loop',$this);
        while ( $route = $router->route( $req ) ) {

            // ディスパッチ開始
            $event->trigger('before.dispatch',$this);

            $isContinue = $route->invoke( $req, $res, $this );

            // ディスパッチ終了
            $event->trigger('after.dispatch',$this);

            // ディスパッチしたことを通知
            $isDispatched = true;

            // ディスパッチした関数がTRUEを返さない限りループを終わらせる
            if ($isContinue !== true) break;


            $router->next( );
        }

        // ディスパッチループ終了
        $event->trigger('after.dispatch-loop',$this);

        // 何もディスパッチされていなければnotfoundイベントをトリガする
        $event->trigger('notfound', $this);
    }

    public function __call( $name, $params )
    {
        return $this->environment->call( $name, $params );
    }
}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
