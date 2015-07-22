KindEditor Widget for Yii2
==========================
KindEditor Widget for Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mkui/yii2-kindeditor "*"
```

or add

```
"mkui/yii2-kindeditor": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \mkui\kindeditor\KindEditorWidget::widget([
    'name' => 'content',
    'options' => [], // html attributes
    'clientOptions' => [
        'width' => '680px',
        'height' => '350px',
        'themeType' => 'default', // optional: default, simple, qq
        'langType' => 'zh_CN', // optional: ar, en, ko, zh_CN, zh_TW
        ...
    ],
]); ?>
```

or use with a model:

```php
<?= \mkui\kindeditor\KindEditorWidget::widget([
    'model' => $model,
    'attribute' => 'content',
    'options' => [], // html attributes
    'clientOptions' => [
        'width' => '680px',
        'height' => '350px',
        'themeType' => 'default', // optional: default, simple, qq
        'langType' => 'zh_CN', // optional: ar, en, ko, zh_CN, zh_TW
        ...
    ],
]); ?>
```

For full details on usage, see the [documentation](http://kindeditor.net/doc.php).