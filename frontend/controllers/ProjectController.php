<?php

namespace frontend\controllers;

use common\models\AddUser;
use common\models\User;
use common\models\UserRights;
use kartik\grid\EditableColumnAction;
use Yii;
use common\models\Project;
use common\models\ProjectSearch;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
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
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['projectView'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['projectView'],
                        'matchCallback' => function($rule, $action) {
                            return static::isUserInIdProject();
                        },
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['projectAdmin'],
                    ],
                    [
                        'actions' => ['update', 'delete', 'add', 'unset-user', 'edit-user-rights'],
                        'allow' => true,
                        'roles' => ['projectAdmin'],
                        'matchCallback' => function($rule, $action) {
                            return static::isUserInIdProject();
                        },
                    ],
                ],
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
     * Проверка что текущий пользователь состоит в проекте, где id проекта в HTTP запросе id
     * @return bool
     */
    protected static function isUserInIdProject()
    {
        $ret = false;
        if (!Yii::$app->user->can('administrator')) {
            // проверить что пользователь входит в проект
            $id = Yii::$app->request->get('id');
            if ($id) {
                $Project = Project::findOne(['id' => $id]);
                if ($Project) {
                    if ( Yii::$app->user->identity->isInProject($Project->id)) {
                        $ret = true;
                    }
                }
            }
        } else {
            $ret = true;
        }
        return $ret;
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
//                'outputMessage' => function ($model, $attribute, $key, $index) {
//                    return '';
//                },
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
        $Project = $this->findModel($id);
        $model = new AddUser();
        $model->projectId = $Project->id;
        $addUserResult = false;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $addUserResult = $model->addUser();
        }
        return $this->render('add', compact('model', 'Project', 'addUserResult'));


    }

    /**
     * Удалить пользователя из проекта
     * @param $id
     * @param $email
     * @return array
     */
    public function actionUnsetUser($id, $email)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ret = [
            'result' => false,
            'message' => '',
        ];
        do {
            $Project = Project::findOne(['id' => $id]);
            $User = User::findOne(['email' => $email]);
            if (!$Project || !$User) {
                $ret['result'] = false;
                $ret['message'] = 'Неверные входные данные';
                break; // break do-while
            }

            // пользователь в проекте ?
            // что такая связь уже есть, иначе будет исключение
            if (!$User->isInProject($Project->id)) {
                $ret['result'] = false;
                $ret['message'] = 'Неверные входные данные';
                break; // break do-while
            }

            // проверить что проект принадлежит текущему пользователю
            if (!Yii::$app->user->can('administrator')) {
                $projects = Yii::$app->user->identity->getProjects()->select(['id'])->column();
                if (!in_array($Project->id, $projects)) {
                    $ret['result'] = false;
                    $ret['message'] = 'Недостаточно прав';
                    break; // break do-while
                }
            }
            // удалить из проекта
            $User->unlink('projects', $Project, true);

            $ret['result'] = true;

        } while (false);

        return $ret;
    }

}
