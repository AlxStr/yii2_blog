<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Posts';
$this->params['breadcrumbs'][] = $this->title;

?>
<h2>Admin Section</h2>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать пост', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Модерировать посты', ['moderate'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}',
                'buttons' => [
                    'active' => function ($url, $model, $key) {
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
            'created_at:datetime',
            'updated_at:datetime',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
        ],
    ]);
    ?>
</div>
