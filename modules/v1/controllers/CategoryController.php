<?php

namespace app\modules\v1\controllers;

use Yii;
use app\models\forms\CategoryForm;
use app\models\forms\CategorySearch;
use app\models\repositories\CategoryRepository;
use app\models\services\CategoryManageService;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\ServerErrorHttpException;

class CategoryController extends ActiveController
{
    private $categoryService;
    private $categoryRepository;
    public $modelClass = 'app\models\Category';

    public function __construct($id, $module, CategoryManageService $service, CategoryRepository $repository, $config = [])
    {
        parent::__construct($id, $module, $config = []);
        $this->categoryService = $service;
        $this->categoryRepository = $repository;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['authMethods'] = [
            HttpBearerAuth::className(),
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['admin'],
                ],
                [
                    'actions' => ['index', 'view'],
                    'allow' => true,
                    'roles' => ['author'],
                ]
            ],
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function actionCreate(){
        $form = new CategoryForm();
        $form->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($form->validate()) {
            $category = $this->categoryService->create($form);
            Yii::$app->response->setStatusCode(201);
            return $category;
        }elseif($form->hasErrors()){
            return $form;
        }
        throw new ServerErrorHttpException('Failed to create the category for unknown reason.');
    }

    public function actionUpdate($id){
        $category = $this->categoryRepository->get($id);
        $form = new CategoryForm($category);
        $form->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($form->validate()) {
            $this->categoryService->edit($category->id, $form);
            return $this->categoryRepository->get($id);
        }elseif($form->hasErrors()){
            return $form;
        }
        throw new ServerErrorHttpException('Failed to update the category for unknown reason.');
    }

    public function prepareDataProvider()
    {
        $searchModel = new CategorySearch();
        return $searchModel->search(\Yii::$app->request->queryParams);
    }
}