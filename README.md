# yii2-fluentd

Logging with Fluentd for Yii2.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "cyntelli/yii2-log-fluentd" "*"
```

or add

```json
"cyntelli/yii2-log-fluentd" : "*"
```

to the `require` section of your application's `composer.json` file.

Usage
-----

Add Fluentd target to your log component config:
```php
<?php
return [
    ...
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            // 'flushInterval' => 1,
            'targets' => [
                'fluentTarget' => [
                    'class' => 'cyntelli\log\FluentdTarget',
                    'levels' => ['error', 'warning'], // Log levels
                    // 'exportInterval' => 1,
                    'host' => 'host', // Fluentd host
                    'port' => '24224', // Fluentd port
                    'options' => [], // Options for Fluentd client
                    'tag' => 'app' // Tag
                ]
            ],
        ],
    ],
    ...
];
```
