<?php

namespace api\controllers;

use common\controllers\EntityOverallController;
use common\models\Entity;
use common\models\EntitySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

/**
 * REST для Entity
 */
class EntityController extends ActiveController
{

    /**
     * @inheritdoc
     */
    public $modelClass = 'common\models\Entity';

    /**
     * @inheritdoc
     */
    public $createScenario = Entity::SCENARIO_REST_CREATE;

    /**
     * @inheritdoc
     */
    public $updateScenario = Entity::SCENARIO_REST_UPDATE;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['authMethods'] = [
            HttpBasicAuth::className(),
            HttpBearerAuth::className(),
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => EntityOverallController::getAccessRules(),
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'projectId',
            'description',
            'createdAt',
            'updatedAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * @return mixed
     */
    public function prepareDataProvider()
    {
        $searchModel = new EntitySearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

    /**
     * Получить возможные path по нескольким символам по ajax
     * @return array
     * @internal param string $word искомые символы
     */
    public function actionSelectPath()
    {
        $result = EntityOverallController::actionSelectPath($this);

        return $result;
    }

}