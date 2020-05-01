<?php

namespace app\modules\activity\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\activity\models\TeamScore;

/**
 * TeamScoreSearch represents the model behind the search form of `app\modules\activity\models\TeamScore`.
 */
class TeamScoreSearch extends TeamScore
{
  public $team;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'points'], 'integer'],
            [['team'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query=TeamScore::find()->joinWith(['team']);

        // add conditions that should always apply here

        $dataProvider=new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if(!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'team_score.team_id' => $this->team_id,
            'team_score.points' => $this->points,
        ]);
        $query->andFilterWhere(['like', 'team.name', $this->team]);
        $dataProvider->setSort([
            'defaultOrder' => ['points'=>SORT_DESC, 'team_id'=>SORT_ASC],
            'attributes' => array_merge(
                $dataProvider->getSort()->attributes,
                [
                  'points' => [
                    'asc' => ['points'=>SORT_ASC, 'team_id'=>SORT_ASC],
                    'desc' => ['points'=>SORT_DESC, 'team_id'=>SORT_ASC],
                  ],
                  'team' => [
                      'asc' => ['team_id' => SORT_ASC],
                      'desc' => ['team_id' => SORT_DESC],
                  ],
                ]
            ),
        ]);

        return $dataProvider;
    }
}
