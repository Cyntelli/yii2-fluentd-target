<?php

/**
 * @link https://www.cyntelli.com
 * @copyright Copyright (c) 2020 Cyntelli
 * @license https://github.com/Cyntelli/yii2-fluentd-target/blob/master/LICENSE
 */

namespace cyntelli\log;

use Fluent\Logger\FluentLogger;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\log\Logger;

/**
 * FluentdTarget sends log messages to Fluentd
 
 * @author Lanmor Yang <lanmor.yang@adgeek.com.tw>
 * @version 0.1.1
 */
class FluentdTarget extends \yii\log\Target
{
    /**
     * @var array $levels Logger::LEVEL_*
     */
    public $levels;
    /**
     * @var string Fluentd host
     */
    public $host = 'localhost';
    /**
     * @var int Fluentd port
     */
    public $port = 24224;
    /**
     * @var array Options for Fluentd client
     */
    public $options = [];

    /**
     * @var string Tag 
     */
    public $tag = 'app';

    private $_logger;

    public function init()
    {
        parent::init();

        $this->_logger = FluentLogger::open($this->host, $this->port, $this->options);
    }
    /**
     * @inheritdoc
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            $level = Logger::getLevelName($level);

            if (!in_array($level, $this->levels)) continue;
            $shortMsg = '';
            $fullMsg = '';
            $line = '';
            $file = '';
            // For string log message set only shortMessage
            if (is_string($text)) {
                $shortMsg = $text;
            } else {
                $shortMsg = ('Exception ' . get_class($text) . ': ' . $text->getMessage());
                $fullMsg = ((string) $text);
                $line = $text->getLine();
                $file = $text->getFile();
            }

            // elseif ($text instanceof \Exception) {
            //     $shortMsg = ('Exception ' . get_class($text) . ': ' . $text->getMessage());
            //     $fullMsg = ((string) $text);
            //     $line = $text->getLine();
            //     $file = $text->getFile();
            // } else {
            //     $short = ArrayHelper::remove($text, 'short');
            //     $full = ArrayHelper::remove($text, 'full');

            //     if ($short !== null) {
            //         $shortMsg = $short;
            //         // All remaining message is fullMessage by default
            //         $fullMsg = VarDumper::dumpAsString($text);
            //     } else {
            //         // Will use log message as shortMessage by default (no need to add fullMessage in this case)
            //         $shortMsg = VarDumper::dumpAsString($text);
            //     }
            //     // If 'full' is set will use it as fullMessage (note that all other stuff in log message will not be logged, except 'short' and 'add')
            //     if ($full !== null) {
            //         $fullMsg = (VarDumper::dumpAsString($full));
            //     }
            // }

            if (isset($message[4]) && is_array($message[4])) {
                $traces = [];
                foreach ($message[4] as $index => $trace) {
                    $traces[] = "{$trace['file']}:{$trace['line']}";
                }
                $trace = implode("\n", $traces);
            }
            $finalMsg = [
                'message' => $shortMsg,
                'message_full' => $fullMsg,
                'file' => $file,
                'trace' => $trace,
                'level' => $level,
                'category' => $category,
                'line' => $line
            ];
            $this->_logger->post($this->tag, $finalMsg);
        }
    }
}
