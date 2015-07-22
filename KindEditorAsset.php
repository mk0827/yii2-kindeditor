<?php
namespace mkui\kindeditor;

use yii\web\AssetBundle;

class KindEditorAsset extends AssetBundle
{
    public $sourcePath = '@mkui/kindeditor/assets';
    public $js = [];
    public $css = [
        'themes/default/default.css',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (YII_DEBUG) {
            $this->js[] = 'kindeditor.js';
        } else {
            $this->js[] = 'kindeditor-min.js';
        }
    }
}
