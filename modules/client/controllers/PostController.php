<?php

namespace app\modules\client\controllers;

use app\models\forms\PostForm;
use app\models\repositories\PostRepository;
use app\models\services\PostManageService;
use Yii;
use app\models\forms\PostSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class PostController extends Controller
{
    private $postService;
    private $postRepository;

    public function __construct($id, $module, PostManageService $service, PostRepository $repository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->postService = $service;
        $this->postRepository = $repository;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create'],
                        'allow' => true,
                        'roles' => ['author'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'delete', 'update'],
                        'roles' => ['ownPostsManage'],
                        'roleParams' => function($rule) {
                            return ['post' => $this->postRepository->get(Yii::$app->request->get('id'))];
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return $this->redirect(['post/index']);
                }
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(['author' => Yii::$app->user->id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->postRepository->get($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $form = new PostForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $post = $this->postService->create($form);
                return $this->redirect(['view', 'id' => $post->id]);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    public function actionUpdate($id)
    {
        $post = $this->postRepository->get($id);
        $form = new PostForm($post);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->postService->edit($post->id, $form);
                return $this->redirect(['view', 'id' => $post->id]);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $form,

        ]);
    }

    public function actionDelete($id)
    {
        $this->postService->remove($id);
        return $this->redirect(['index']);
    }
}
