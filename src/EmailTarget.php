<?php

namespace PrivateIT\log\target\email;

use yii\db\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class EmailTarget extends \yii\log\EmailTarget
{
    public $excSubject;
    public $excStatusCode = [403, 404];
    public $excErrorCode = [];

    /**
     * @inheritdoc
     */
    public function export()
    {
        if (isset($this->messages[0], $this->messages[0][0])) {
            /** @var NotFoundHttpException $message */
            $message = $this->messages[0][0];

            // Exclude by subject
            if ($this->excSubject) {
                if (preg_match($this->excSubject, $this->message['subject']) !== false) {
                    return;
                }
            }

            // Exclude by http status code
            if (sizeof($this->excStatusCode) > 0) {
                if (in_array($message->statusCode, $this->excStatusCode)) {
                    return;
                }
            }

            // Exclude by error code
            if (sizeof($this->excErrorCode) > 0) {
                if (in_array($message->getCode(), $this->excErrorCode)) {
                    return;
                }
            }

            if ($message instanceof HttpException) {
                $this->message['subject'] = strtr($this->message['subject'], [
                    '{message}' => '[' . $message->statusCode . '] ' . $message->getMessage(),
                ]);
            } elseif ($message instanceof \Exception) {
                $this->message['subject'] = strtr($this->message['subject'], [
                    '{message}' => '[' . $message->getCode() . '] ' . $message->getMessage() . ' - ' . $message->getFile() . ':' . $message->getLine(),
                ]);
            }
        }
        parent::export();
    }

}