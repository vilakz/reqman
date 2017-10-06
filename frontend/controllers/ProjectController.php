<?php

namespace frontend\controllers;

use common\controllers\ProjectOverallController;
use common\models\UserRights;
use kartik\grid\EditableColumnAction;
use Yii;
use common\models\Project;
use common\models\ProjectSearch;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => array_merge(
                    ProjectOverallController::getAccessRules(),
                    [
                        [
                            'actions' => ['edit-user-rights'],
                            'allow' => true,
                            'roles' => ['projectAdmin'],
                            'matchCallback' => function ($rule, $action) {
                                return ProjectOverallController::isUserInIdProject();
                            },
                        ],

                    ]),
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'edit-user-rights' => [
                'class' => EditableColumnAction::className(),
                'modelClass' => UserRights::className(),
                'outputValue' => function ($model, $attribute, $key, $index) {
                    $roles = Yii::$app->authManager->getRolesByUser($model->id);
                    return implode(';', \yii\helpers\ArrayHelper::getColumn($roles, 'name'));
                },
                'showModelErrors' => true,
                'postOnly' => true,
                'ajaxOnly' => true,
                'findModel' => function ($id, $action) {
                    $User = UserRights::findOne(['id' => $id]);
                    if (!$User) {
                        throw new Exception('Пользователь не найден');
                    }
                    return $User;
                },
                'formName' => 'User',
                'checkAccess' => function ($action, $model) {
                    $user = Yii::$app->user;
                    return ($user->can('administrator') || $user->can('projectAdmin'));
                },
            ],
        ];
    }

    /**
     * Lists all Project models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Project model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Project();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Project model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Добавить пользователя в проект
     * @param $id Project.id
     * @return string
     */
    public function actionAdd($id)
    {
        $result = ProjectOverallController::actionAdd($this, $id);
        return $this->render('add', $result);
    }

    /**
     * Удалить пользователя из проекта
     * @param $id
     * @param $email
     * @return array
     */
    public function actionUnsetUser($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ret = ProjectOverallController::actionUnsetUser($this, $id);
        return $ret;
    }

}
