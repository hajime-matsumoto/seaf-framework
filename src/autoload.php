<?php
/**
 * Seaf Auto Load
 */
Seaf::di('autoLoader')->addNamespace(
    'Seaf\\FrameWork',
    null,
    dirname(__FILE__).'/FrameWork'
);

Seaf::register('front', 'Seaf\FrameWork\FrontController');
