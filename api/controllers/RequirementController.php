<?php

namespace api\controllers;

use common\controllers\RequirementOverallController;
use common\models\Requirement;
use common\models\RequirementSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

/**
 * REST для Requirement
 */
class RequirementController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'common\models\Requirement';

    /**
     * @inheritdoc
     */
    public $createScenario = Requirement::SCENARIO_REST_CREATE;

    /**
     * @inheritdoc
     */
    public $updateScenario = Requirement::SCENARIO_REST_UPDATE;

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
            'rules' => RequirementOverallController::getAccessRules(),
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
            'body',
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
        $searchModel = new RequirementSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }

}