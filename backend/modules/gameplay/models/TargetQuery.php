<?php

namespace app\modules\gameplay\models;

/**
 * This is the ActiveQuery class for [[Target]].
 *
 * @see Target
 */
class TargetQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[active]]=1');
    }

    public function online()
    {
        return $this->andWhere('[[status]]="online"');
    }

    public function poweredup()
    {
        return $this->joinWith(['target_ondemand'])->andWhere('target_ondemand.target_id is null or target_ondemand.status=1');
    }

    public function powerup()
    {
      return $this->andWhere(['status'=>'powerup'])->andWhere(new \yii\db\Expression("scheduled_at is not null"))->andWhere('scheduled_at<NOW()');
    }

    public function powerdown()
    {
      return $this->andWhere(['status'=>'powerdown'])->andWhere(new \yii\db\Expression("scheduled_at is not null"))->andWhere('scheduled_at<NOW()');
    }

    public function offline()
    {
      return $this->andWhere(['status'=>'offline'])->andWhere(new \yii\db\Expression("scheduled_at is not null"))->andWhere('scheduled_at<NOW()');
    }


    public function docker_servers()
    {
        return $this->select('server')->distinct()->andWhere(['like','server',"tcp://%:2376"]);
    }

    /**
     * {@inheritdoc}
     * @return Target[]|array
     */
    public function all($db=null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Target|array|null
     */
    public function one($db=null)
    {
        return parent::one($db);
    }
}
