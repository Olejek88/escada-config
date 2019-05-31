<?php

namespace backend\models;

use common\models\DeviceRegister;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DeviceRegisterSearch represents the model behind the search form about `common\models\DeviceRegister`.
 */
class DeviceRegisterSearch extends DeviceRegister
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'deviceUuid', 'date'], 'safe'],
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
        $query = DeviceRegister::find();

        // add conditions that should always apply here

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
            'date' => $this->date,
            'deviceUuid' => $this->deviceUuid
        ]);

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->orderBy(['date' => SORT_DESC]);

        return $dataProvider;
    }
}
