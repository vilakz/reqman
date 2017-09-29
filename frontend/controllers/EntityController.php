<?php

namespace frontend\controllers;

use Yii;
use common\models\Entity;
use common\models\EntitySearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
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
                        'actions' => ['update', 'delete', 'select-path'],
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

    /**
     * Получить возможные path по нескольким символам по ajax
     * @param $id integer Entity.id
     * @param $word string искомые символы
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionSelectPath($id, $word)
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = Entity::find()
            ->orderBy(['entity.path' => SORT_DESC])
            ->andWhere(new \yii\db\Expression("`entity`.`path` LIKE '%".($this->processSlashes($word))."%'") )            ;

        if (!\Yii::$app->user->can('administrator')) {
            $User = Yii::$app->user->identity;
            $query->joinWith(['project' => function($query) use ($User) {
                /** @var $query \yii\db\ActiveQuery */
                $query->joinWith(['userProjects' => function($query) use ($User) {
                }]);
            }]);
            $query->andWhere([
                'or',
                ['entity.projectId' => null],
                ['userProject.userId' => $User->id],
            ]);
        }
        $result = $query->asArray()->all();
        array_walk($result, function(&$item){
            // защита вывода, т.к. в result все данные из таблиц entity, связанные с ней project, userProject
            $new = ['id' => $item['id'], 'path' => $item['path']];
            $item = $new;
        });
        return $result;
    }

    /**
     * Слешевые дела
     * @param $word
     * @return mixed|string
     */
    protected function processSlashes($word)
    {
        $wordQuery = str_replace('\\', '\\\\\\\\', $word);
        if (0 === strpos($wordQuery, '\\')) {
            $strAfterReplace = preg_replace('/^(\\\\+)([^\x00]*?)$/ui','${2}',$wordQuery);
            if (0 == strlen($strAfterReplace)) {
                // если в поиске только один слеш, то выдать такую последовательность
                $wordQuery = '\\\\\\\\\\\\';
            } else {
                $wordQuery = '\\\\'.$strAfterReplace;
            }
        }
        return $wordQuery;
    }

}
