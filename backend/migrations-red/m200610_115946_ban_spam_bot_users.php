<?php

use yii\db\Migration;

/**
 * Class m200610_115946_ban_spam_bot_users
 */
class m200610_115946_ban_spam_bot_users extends Migration
{
  public $filters=[
    '%bigmir.___',
    '%nextfashion.__',
    'test@gmail.com',
    'prostitutki.rostova.vip@gmail.com'
  ];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      foreach($this->filters as $filter)
      {
        $this->db->createCommand("INSERT IGNORE INTO banned_player (old_id,username,email,registered_at,banned_at) SELECT id,username,email,created,NOW() FROM player WHERE email like '$filter'")->execute();
        $this->db->createCommand("DELETE FROM player WHERE email like '$filter'")->execute();
      }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200610_115946_ban_spam_bot_users cannot be reverted.\n";
    }

}
