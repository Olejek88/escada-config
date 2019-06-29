<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SoundFile;

/**
 * SoundFileSearch represents the model behind the search form of `common\models\SoundFile`.
 */
class SoundFileSearch extends SoundFile
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['_id', 'deleted'], 'integer'],
            [['uuid', 'title', 'soundFile', 'nodeUuid', 'createdAt', 'changedAt'], 'safe'],
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
        $query = SoundFile::find();

        // add conditions that should always apply here
        $query->where(['deleted' => false]);

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
            '_id' => $this->_id,
            'deleted' => $this->deleted,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'soundFile', $this->soundFile])
            ->andFilterWhere(['like', 'nodeUuid', $this->nodeUuid]);

        return $dataProvider;
    }
}
