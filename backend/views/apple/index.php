<?php

use common\models\Apple;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\search\AppleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Яблоки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apple-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Сгенерировать яблок', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'color',
            [
                'attribute' => 'status',
                'class' => 'yii\grid\DataColumn',
                'label' => 'Статус',
                'value' => function ($data) {
                    return ($data->status) ? Apple::getStatusList()[$data->status] : '';
                },
                'filter' => Apple::getStatusList()
            ],
            [
                'attribute' => 'eaten',
                'class' => 'yii\grid\DataColumn',
                'label' => 'Сколько сьели',
                'value' => function ($data) {
                    return $data->eaten . '%';
                },
            ],
            'size',
            'created_at:datetime',
            'fell_at:datetime',
            [
                'header' => '',
                'value' => function ($data) {
                    return ($data->status == Apple::STATUS_ON_TREE && $data->fell_at == null) ?
                        Html::a('Упасть', ['apple/fall', 'id' => $data->id],
                        ['class' => 'btn btn-success'])
                    : '';
                },
                'format' => 'html'
            ],
            [
                'header' => '',
                'value' => function ($data) {
                    return ($data->status == Apple::STATUS_FALL && $data->size > 0) ?
                        Html::a('Съесть', ['apple/eat', 'id' => $data->id],
                        ['class' => 'btn btn-warning'])
                    : '';
                },
                'format' => 'html'
            ],
            // [
            //     'class' => ActionColumn::className(),
            //     'urlCreator' => function ($action, Apple $model, $key, $index, $column) {
            //         return Url::toRoute([$action, 'id' => $model->id]);
            //      }
            // ],
        ],
    ]); ?>


</div>
