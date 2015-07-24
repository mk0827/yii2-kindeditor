<?php
namespace mkui\kindeditor;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

class KindEditorWidget extends InputWidget
{
    /**
     * The name of this widget.
     */
    const PLUGIN_NAME = 'KindEditor';

    const THEME_TYPE_DEFAULT = 'default';
    const THEME_TYPE_QQ = 'qq';
    const THEME_TYPE_SIMPLE = 'simple';

    const LANG_TYPE_AR = 'ar';
    const LANG_TYPE_EN = 'en';
    const LANG_TYPE_KO = 'ko';
    const LANG_TYPE_ZH_CN = 'zh_CN';
    const LANG_TYPE_ZH_TW = 'zh_TW';

    /**
     * @var array the KindEditor plugin options.
     * @see http://kindeditor.net/doc.php
     */
    public $clientOptions = [];

    /**
     * csrf cookie parameter
     * @var string
     */
    public $csrfCookieParam = '_csrfCookie';

    /**
     * @var boolean
     */
    public $render = true;
    /**
     * @var string 编辑器类型
     * textEditor:普通编辑器
     * uploadButton:自定义上传按钮
     * colorPicker:取色器
     * fileManager:浏览服务器
     * dialog:弹窗
     * imageDialog:上传图片
     * multiImageDialog:批量上传图片
     * fileDialog:文件上传
     */
    public $editorType = 'textEditor';
    /**
     * 默认图片
     * @var string
     */
    public $defaultImg = 'http://stc.weimob.com//img/template/lib/home-300200.jpg';

    public function init(){
        parent::init();
        $this->id = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->id;
        $this->name = $this->hasModel() ? Html::getInputName($this->model, $this->attribute) : $this->name;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
        if ($this->render) {
            if ($this->hasModel()) {
                switch ($this->editorType) {
                    case 'uploadButton':
                        return Html::activeInput('text', $this->model, $this->attribute, $this->options) . '<input type="button" id="uploadButton" value="Upload" />';
                        break;
                    case 'colorPicker':
                        return Html::activeInput('text', $this->model, $this->attribute, $this->options) . '<input type="button" id="colorpicker" value="打开取色器" />';
                        break;
                    case 'fileManager':
                        return Html::activeInput('text', $this->model, $this->attribute, $this->options) . '<input type="button" id="filemanager" value="浏览服务器" />';
                        break;
                    case 'imageDialog':
                        $value = $this->model->isNewRecord ? $this->defaultImg : Html::getAttributeValue($this->model, $this->attribute);
                        return "<img id='thumb-img-$this->id' type='img' src='$value' style='max-height:100px;' />
                            <input type='hidden' id='thumb-$this->id' name='$this->name' value='$value' class='input-medium' data-rule-url='true' />
                            <a id='img-$this->id' class='btn insertimage'>选择图片</a>";
                        break;
                    case 'fileDialog':
                        return Html::activeInput('text', $this->model, $this->attribute, $this->options) . '<input type="button" id="insertfile" value="选择文件" />';
                        break;
                    default:
                        return Html::activeTextarea($this->model, $this->attribute, $this->options);
                        break;
                }
            } else {
                switch ($this->editorType) {
                    case 'uploadButton':
                        return Html::input('text', $this->id, $this->value, $this->options) . '<input type="button" id="uploadButton" value="Upload" />';
                        break;
                    case 'colorPicker':
                        return Html::input('text', $this->id, $this->value, $this->options) . '<input type="button" id="colorpicker" value="打开取色器" />';
                        break;
                    case 'fileManager':
                        return Html::input('text', $this->id, $this->value, $this->options) . '<input type="button" id="filemanager" value="浏览服务器" />';
                        break;
                    case 'imageDialog':
                        $value = $this->model->isNewRecord ? get_params('UPLOAD_DEFAULT_IMG') : Html::getAttributeValue($this->model, $this->attribute);
                        return "<img id='thumb_img_$this->id' type='img' src='$value' style='max-height:100px;' />
                            <input type='hidden' id='thumb-$this->id' name='$this->name' value='' class='input-medium' data-rule-url='true' />
                            <a id='img-$this->id' class='btn insertimage'>选择图片</a>";
                        break;
                    case 'fileDialog':
                        return Html::input('text', $this->id, $this->value, $this->options) . '<input type="button" id="insertfile" value="选择文件" />';
                        break;
                    default:
                        return Html::textarea($this->id, $this->value, $this->options);
                        break;
                }
            }
        }
    }

    /**
     * Registers the needed client script and options.
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        $this->initClientOptions();
        $asset = KindEditorAsset::register($view);
        $preJs = '';
        $uploadJson = $this->clientOptions['uploadJson'];
        $fileManagerJson = $this->clientOptions['fileManagerJson'];
        switch ($this->editorType) {
            case 'uploadButton':
                $preJs = <<<EOT
                var uploadbutton = K.uploadbutton({
                    button : K('#uploadButton')[0],
                    fieldName : 'imgFile',
                    url : '{$uploadJson}',
                    afterUpload : function(data) {
                        if (data.error === 0) {
                            var url = K.formatUrl(data.url, 'absolute');
                            K('#{$this->id}').val(url);
                        } else {
                            alert(data.message);
                        }
                    },
                    afterError : function(str) {
                        alert('自定义错误信息: ' + str);
                    }
                });
                uploadbutton.fileBox.change(function(e) {
                    uploadbutton.submit();
                });
EOT;

                break;
            case 'colorPicker':
                $preJs = <<<EOT
                var colorpicker;
                K('#colorpicker').bind('click', function(e) {
                    e.stopPropagation();
                    if (colorpicker) {
                        colorpicker.remove();
                        colorpicker = null;
                        return;
                    }
                    var colorpickerPos = K('#colorpicker').pos();
                    colorpicker = K.colorpicker({
                        x : colorpickerPos.x,
                        y : colorpickerPos.y + K('#colorpicker').height(),
                        z : 19811214,
                        selectedColor : 'default',
                        noColor : '无颜色',
                        click : function(color) {
                            K('#{$this->id}').val(color);
                            colorpicker.remove();
                            colorpicker = null;
                        }
                    });
                });
                K(document).click(function() {
                    if (colorpicker) {
                        colorpicker.remove();
                        colorpicker = null;
                    }
                });
EOT;

                break;
            case 'fileManager':
                $preJs = <<<EOT
                var editor = K.editor({
                    fileManagerJson : '{$fileManagerJson}'
                });
                K('#filemanager').click(function() {
                    editor.loadPlugin('filemanager', function() {
                        editor.plugin.filemanagerDialog({
                            viewType : 'VIEW',
                            dirName : 'image',
                            clickFn : function(url, title) {
                                K('#{$this->id}').val(url);
                                editor.hideDialog();
                            }
                        });
                    });
                });
EOT;

                break;
            case 'imageDialog':
                $preJs = <<<EOT
                var editor = K.editor({
                    allowFileManager : true,
                    "uploadJson":"{$uploadJson}",
                    "fileManagerJson":"{$fileManagerJson}",
                });
                K('#img-{$this->id}').click(function(e) {
                    editor.loadPlugin('image', function() {
                        editor.plugin.imageDialog({
                            imageUrl : $(e.target).prev().val(),
                            clickFn : function(url, title, width, height, border, align) {
                                $(e.target).prev().val(url);
                                if ('img' == $(e.target).prev().prev().attr('type')) {
                                    $(e.target).prev().hide();
                                    $(e.target).prev().prev().attr('src', url);
                                    $(e.target).prev().prev().show();
                                }

                                editor.hideDialog();
                            }
                        });
                    });
                });
EOT;

                break;
            case 'fileDialog':
                $preJs = <<<EOT
                var editor = K.editor({
                    allowFileManager : true,
                    "uploadJson":"{$uploadJson}",
                    "fileManagerJson":"{$fileManagerJson}",
                });
                K('#insertfile').click(function() {
                    editor.loadPlugin('insertfile', function() {
                        editor.plugin.fileDialog({
                            fileUrl : K('#{$this->id}').val(),
                            clickFn : function(url, title) {
                                K('#{$this->id}').val(url);
                                editor.hideDialog();
                            }
                        });
                    });
                });
EOT;

                break;
            default:
                $preJs = "
                K.create('#{$this->id}', ".Json::encode($this->clientOptions).");";

                break;
        }

        $view->registerJsFile($asset->baseUrl . '/lang/' . $this->clientOptions['langType'] . '.js', ['depends' => '\mkui\kindeditor\KindEditorAsset']);
        $js = "
KindEditor.ready(function(K) {
    {$preJs};
});
        ";
        $view->registerJs($js);
    }

    /**
     * Initializes client options
     */
    protected function initClientOptions()
    {
        $options = array_merge($this->defaultOptions(), $this->clientOptions);
        // $_POST['_csrf'] = ...
        $options['extraFileUploadParams'][Yii::$app->request->csrfParam] = Yii::$app->request->getCsrfToken();
        // $_POST['PHPSESSID'] = ...
        $options['extraFileUploadParams'][Yii::$app->session->name] = Yii::$app->session->id;
        if (Yii::$app->request->enableCsrfCookie) {
            // $_POST['_csrfCookie'] = ...
            $options['extraFileUploadParams'][$this->csrfCookieParam] = $_COOKIE[Yii::$app->request->csrfParam];
        }
        $this->clientOptions = $options;
    }

    /**
     * Default client options
     * @return array
     */
    protected function defaultOptions()
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'fileManagerJson'   => Url::to(['Kupload', 'action' => 'fileManagerJson']),
            'uploadJson'    => Url::to(['Kupload', 'action' => 'uploadJson']),
            'width' => '680px',
            'height' => '350px',
            'themeType' => self::THEME_TYPE_DEFAULT,
            'langType' => self::LANG_TYPE_ZH_CN,
            'afterChange' => new JsExpression('function(){this.sync();}'),
        ];
    }
}
