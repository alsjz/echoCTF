<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\target\models\Target;
use app\models\Player;

class GeneratorController extends Controller {

  /**
   * Generate sitemap.xml
   */
    public function actionSitemap($baseurl='https://echoctf.red/')
    {
      $targets=Target::find()->active()->all();
      $contents=$this->renderFile(\Yii::getAlias('@app/views/sitemap.php'), ['targets'=>$targets, 'BASEURL'=>$baseurl, 'profiles'=>[], 'TvsP'=>[]]);
      file_put_contents(\Yii::getAlias('@app/web/sitemap.xml'), $contents);
    }

    /**
     * Generate participant avatars based on robohash
     */
      public function actionAvatar()
      {
        $players=Player::find()->active()->all();
        foreach($players as $player)
        {
          $robohash=new \app\models\Robohash($player->profile->id,'set1');
          $image=$robohash->generate_image();
          if(get_resource_type($image)=== 'gd')
          {
            $dst_img=\Yii::getAlias('@app/web/images/avatars/'.$player->profile->id.'.png');
            imagepng($image,$dst_img);
            imagedestroy($image);
            $player->profile->avatar=$player->profile->id.'.png';
            $player->profile->save(false);
          }
        }
      }

    /**
    * Generate participant avatars based on robohash
    */
    public function actionAuthKeys()
    {
      $players=Player::find()->where(['auth_key'=>''])->active()->all();
      foreach($players as $player)
      {
        $player->generateAuthKey();
        if(!$player->save(false))
        {
          echo $player->id, " ", $player->username, implode(', ', $player->getErrors());
        }
      }
    }

    public function actionUrls()
    {
      $config=include(__DIR__.'/../config/web.php');
      $urlmgr=new \yii\web\UrlManager();
      $urlmgr->baseUrl="";
      $urlmgr->setHostInfo("https://echoctf.red/");
      $urlmgr->enablePrettyUrl = true;
      $urlmgr->enableStrictParsing = true;
      $urlmgr->showScriptName = false;
      $urlmgr->addRules($config['components']['urlManager']['rules']);
      $urlmgr->init();
      //var_dump($urlmgr->createUrl(['site/index']));
      foreach($config['components']['urlManager']['rules'] as $key => $val)
      {
        if(strstr($key,'<profile')!==false)
          echo $urlmgr->createAbsoluteUrl([$val,'id'=>2,'profile_id'=>1]),"\n";
        elseif(strstr($key,'<id')!==false)
          echo $urlmgr->createAbsoluteUrl([$val,'id'=>1]),"\n";
        elseif(strstr($key,'<token')!==false)
          echo $urlmgr->createAbsoluteUrl([$val,'token'=>'abcdedf']),"\n";
        else
          echo $urlmgr->createAbsoluteUrl($val),"\n";

      }
    }
}
