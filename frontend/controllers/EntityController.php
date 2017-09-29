<?php

namespace frontend\controllers;

use Yii;
use common\models\Entity;
use common\models\EntitySearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EntityController implements the CRUD actions for Entity model.
 */
class EntityController extends Controller
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
                            return static::isUserInIdEntity();
                        },
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['projectEdit'],
                    ],
                    [
                        'actions' => ['update', 'delete'],
                        'allow' => true,
                        'roles' => ['projectEdit'],
                        'matchCallback' => function($rule, $action) {
                            return static::isUserInIdEntity();
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
     * Проверка что текущий пользователь состоит в проекте по сущности, где id сущности в HTTP запросе id
     * @return bool
     */
    protected static function isUserInIdEntity()
    {
        $ret = false;
        if (!Yii::$app->user->can('administrator')) {
            // проверить что пользователь входит в проект
            $id = Yii::$app->request->get('id');
            if ($id) {
                $Entity = Entity::findOne(['id' => $id]);
                if ($Entity) {
                    $Project = $Entity->project;
                    if ($Project) {
                        if ( Yii::$app->user->identity->isInProject($Project->id)) {
                            $ret = true;
                        }
                    } else {
                        // Если сущность без проекта, то она разрешена несмотря на проект
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
     * Lists all Entity models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EntitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Entity model.
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
     * Creates a new Entity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Entity();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Entity model.
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
     * Deletes an existing Entity model.
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
     * Finds the Entity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Entity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Entity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
