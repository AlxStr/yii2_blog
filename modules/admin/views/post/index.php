<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать пост', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <div class="box">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update} {active}',
                        'buttons' => [
                            'active' => function ($url, $model, $key) {
                                if ($model->status == 1) return false;
                                return Html::a('<span class="glyphicon glyphicon-ok" title="Активно"></span>', $url);
                            },
                        ],
                    ],

                    'title',
                    [
                        'attribute' => 'status',
                        'value' => 'statusName',
                        'filter' => app\models\Post::getStatusList(),
                    ],

                    [
                        'attribute' => 'category_id',
                        'value' => 'category.title',
                        'filter' => $categories,
                    ],
                    'logo',
                    [
                        'attribute' => 'author',
                        'value' => 'postAuthor.username'
                    ],

                    [
                        'attribute' => 'created_at',
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date_create',
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'pluginOptions' => [
                                'todayHighlight'=>true,
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd',
                            ]
                        ]),
                        'format' => 'datetime',
                    ],
                    [
                        'attribute' => 'updated_at',
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date_update',
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'pluginOptions' => [
                                'todayHighlight'=>true,
                                'autoclose'=>true,
                                'format' => 'yyyy-mm-dd',
                            ]
                        ]),
                        'format' => 'datetime',
                    ],

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
                ],
            ]);
            ?>
        </div>
    </div>

</div>
