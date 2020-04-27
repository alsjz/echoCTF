<?php

namespace app\modules\gameplay\models;

use Yii;
use app\modules\frontend\models\Player;

/**
 * This is the model class for table "network".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $ts
 *
 * @property NetworkPlayer[] $networkPlayers
 * @property Player[] $players
 * @property NetworkTarget[] $networkTargets
 * @property Target[] $targets
 */
class Network extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'network';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['ts'], 'safe'],
            [['name'], 'string', 'max' => 32],
            [['name'], 'unique'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'ts',
                'updatedAtAttribute' => 'ts',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'ts' => 'Ts',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkPlayers()
    {
        return $this->hasMany(NetworkPlayer::class, ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlayers()
    {
        return $this->hasMany(Player::class, ['id' => 'player_id'])->viaTable('network_player', ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNetworkTargets()
    {
        return $this->hasMany(NetworkTarget::class, ['network_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTargets()
    {
        return $this->hasMany(Target::class, ['id' => 'target_id'])->viaTable('network_target', ['network_id' => 'id']);
    }
}
