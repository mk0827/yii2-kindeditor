<?php

/**
 * Description of KindEditorAsset
 * 
 * @author Rootkit <Rootkit, 290147164@qq.com>
 * @link #
 * @QQ 290147164
 * @date 2015-3-4
 */
namespace mkui\kindeditor;

use yii\web\AssetBundle;

class KindEditorAsset extends AssetBundle {
    //put your code here
    public $js=[
        'kindeditor-min.js',
        'lang/zh_cn.js',
       // 'kindeditor.js'
    ];
     public $css=[
        'themes/default/default.css'
    ];
    public $jsOptions=[
        'charset'=>'utf8',
    ];

    public function init() {
        //资源所在目录
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR ;
    }
}