<?php

namespace backend\jobs;

use common\models\Apple;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class AppleRottenJob extends BaseObject implements \yii\queue\JobInterface
{
    public $apple_id;
    public $apple_status;

    public function execute($queue)
    {
        $model = Apple::findOne(['id' => $this->apple_id]);
       
        try {
            $model->status = $this->apple_status;
            $model->save();

        } catch (\Throwable $e) {
            echo $e->getMessage() . "\n" . $e->getTraceAsString();
        }
    }
}