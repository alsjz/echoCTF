<?php
/**
 * @author Pantelis Roditis <proditis@echothrust.com>
 * @copyright 2022
 */

namespace app\commands;

use Yii;
use yii\console\Exception as ConsoleException;
use yii\helpers\Console;
use yii\console\Controller;
use app\modules\frontend\models\Player;
use app\components\OpenVPN;
/**
 * Manages VPN specific operations.
 *
 * @author proditis
 */
class VpnController extends Controller
{

  /**
   * IsOnline check
   * @param $string $player player: id or username to check
   */
  public function actionIsOnline($player)
  {
    $pM=Player::find()->where(['username'=>$player])->orWhere(['id'=>$player])->one();
    if($pM===NULL)
    {
      throw new ConsoleException(Yii::t('app', 'Player not found with id or username of [{values}]', ['values' => $player]));
    }

    $result=Yii::$app->db->createCommand("SELECT memc_get('ovpn:{$pM->id}') AS ovpn_status")->queryScalar();
    printf("Player %s %s\n",$pM->username,$result ?? "is offline");
  }

  /**
   * Logout a specific player from the database (no OpenVPN sessions are touched)
   * @param string $player player: id or username.
   */
  public function actionLogout($player)
  {

    $pM=Player::find()->where(['username'=>$player])->orWhere(['id'=>$player])->one();
    if($pM===NULL)
    {
      throw new ConsoleException(Yii::t('app', 'Player not found with id or username of [{values}]', ['values' => $player]));
    }
    printf("Logging out %d\n",$pM->id);
    OpenVPN::logout($pM->id);
  }

  /**
   * Kill a specific player session from OpenVPN.
   * @param string $player player: id or username.
   */
  public function actionKill($player)
  {

    $pM=Player::find()->where(['username'=>$player])->orWhere(['id'=>$player])->one();
    if($pM===NULL)
    {
      throw new ConsoleException(Yii::t('app', 'Player not found with id or username of [{values}]', ['values' => $player]));
    }
    printf("Killing %d with last local IP [%s]\n",$pM->id,$pM->last->vpn_local_address_octet);
    OpenVPN::kill($pM->id,intval($pM->last->vpn_local_address));
  }

  /**
   * Killall kill all connections from OpenVPN (takes a long time).
   */
  public function actionKillall()
  {

    foreach(Player::find()->where(['status'=>10])->all() as $pM)
    {
      printf("Logging out %d\n",$pM->id);
      OpenVPN::logout($pM->id);
    }
  }
  /**
   * Logoutall stall connections from OpenVPN.
   */
  public function actionLogoutall()
  {
    Yii::$app->db->createCommand("UPDATE player_last SET vpn_local_address=NULL, vpn_remote_address=NULL")->execute();
  }

  /**
   * Load configuration from filesystem
   */
  public function actionLoad($filepath)
  {
    $file=basename($filepath);
    try{
      $contents=file_get_contents($filepath);
      Yii::$app->db->createCommand("UPDATE openvpn SET conf=:config WHERE name=:filename",[':config'=>$contents,':filename'=>$file])->execute();
    }
    catch (\Exception $e)
    {
      printf("Error: ",$e->getMessage());
    }

  }
  /**
   * Save configuration to filesystem
   */
  public function actionSave($filepath)
  {
    try{
      $file=basename($filepath);
      $contents=Yii::$app->db->createCommand("SELECT conf FROM openvpn WHERE name=:filename",[':filename'=>$file])->queryScalar();
      file_put_contents($filepath,$contents);
    }
    catch (\Exception $e)
    {
      printf("Error: ",$e->getMessage());
    }
  }

  /**
   * Display openvpn status enriched with database details
   */
  public function actionStatus($provider_id=null)
  {
    $q=\app\modules\settings\models\Openvpn::find()->select('status_log')->andFilterWhere(['LIKE','provider_id',$provider_id]);
    $status['routing_table']=[];
    $status['client_list']=[];
    foreach($q->all() as $entry)
    {
      try {
        $parsed=OpenVPN::parseStatus($entry->status_log);
        if(property_exists($parsed,'routing_table') && count($parsed->routing_table)>0)
          $status['routing_table']=\yii\helpers\ArrayHelper::merge($status['routing_table'],$parsed->routing_table);
        if(property_exists($parsed,'client_list') && count($parsed->client_list)>0)
          $status['client_list']=\yii\helpers\ArrayHelper::merge($status['client_list'],$parsed->client_list);
      }
      catch (\Exception $e)
      {

      }
      unset($entry);
    }
    $this->stdout(  sprintf("%-5s %-10s %-10s %-18s %-10s %-10s\n", 'ID', 'Username','Local IP','Remote IP', 'Received', 'Send'), Console::BOLD);
    foreach($status['client_list'] as $entry)
    {
      $p=\app\modules\frontend\models\Player::findOne($entry->player_id);
      $this->stdout(sprintf("%-5s %-10s %-10s %-18s %-10s %-10s\n", $entry->player_id,$p->username,$p->playerLast->vpn_local_address_octet,$entry->remote_ip_port,number_format($entry->bytes_received/1024).'kb',number_format($entry->bytes_send/1024).'kb'));
    }
  }
}
