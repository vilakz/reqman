<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Requirement;

/**
 * RequirementSearch represents the model behind the search form about `common\models\Requirement`.
 */
class RequirementSearch extends Requirement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'projectId'], 'integer'],
            [['name', 'body', 'createdAt', 'updatedAt'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Requirement::find();

        // add conditions that should always apply here
        if (!\Yii::$app->user->can('administrator')) {
            $User = Yii::$app->user->identity;
            $query->joinWith(['project' => function($query) use ($User) {
                /** @var $query \yii\db\ActiveQuery */
                $query->joinWith(['userProjects' => function($query) use ($User) {
                    $query->andWhere(['userProject.userId' => $User->id]);
                }]);
            }]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'projectId' => $this->projectId,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'body', $this->body]);

        return $dataProvider;
    }
}
