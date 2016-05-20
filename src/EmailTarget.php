<?php

namespace PrivateIT\log\target\email;

use yii\db\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class EmailTarget extends \yii\log\EmailTarget
{
    /**
     * @inheritdoc
     */
    public function export()
    {
        if (isset($this->messages[0], $this->messages[0][0])) {
            /** @var NotFoundHttpException $message */
            $message = $this->messages[0][0];
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