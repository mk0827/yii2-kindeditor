<?php
namespace mkui\kindeditor\controllers;

use mkui\kindeditor\KindEditorController;
use Yii;

class KuploadController extends KindEditorController {
    public function init(){
        parent::init();
        //获得action 动作
        $action = Yii::$app->request->get('action');
        switch ($action) {
            case 'fileManagerJson':
                $this->actionFilemanager();
                break;
            case 'uploadJson':
                $this->actionUpload();
                break;
            default:
                break;
        }
    }
}