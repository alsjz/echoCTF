<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Twitter;
use app\modules\game\models\Headshot;

?>
<div class="card card-profile">
  <div class="card-avatar bg-primary">
    <a href="<?=$profile->linkTo?>">
      <img class="img" src="/images/avatars/<?=$profile->avtr?>" />
    </a>
  </div>
  <div class="card-body">
    <h6 class="badge badge-secondary">Level <?=$profile->experience->id?> / <?=$profile->experience->name?></h6>
    <h4 class="card-title"><?=Html::encode($profile->owner->username)?></h4>
    <p class="card-description">
      <?=Html::encode($profile->bio)?>
    </p>
    <?php if($profile->isMine):?>
      <?php echo Html::a(Url::to(['profile/index', 'id'=>$profile->id], 'https'), ['profile/index', 'id'=>$profile->id]);?> <?php echo Twitter::widget([
            'message'=>sprintf('Checkout my profile at echoCTF.RED! %s', $profile->braggingRights),
            'url'=>Url::to(['profile/index', 'id'=>$profile->id], 'https'),
            'linkOptions'=>['class'=>'profile-tweet', 'target'=>'_blank', 'style'=>'font-size: 1.3em;'],
        ]);?>
    <?php else: ?>
      <?php echo Html::a(Url::to(['profile/index', 'id'=>$profile->id], 'https'), ['profile/index', 'id'=>$profile->id]);?> <?php echo Twitter::widget([
            'message'=>sprintf('Checkout the profile of %s at echoCTF.RED', $profile->twitterHandle),
            'linkOptions'=>['class'=>'profile-tweet', 'target'=>'_blank', 'style'=>'font-size: 1.3em;'],
        ]);?>
    <?php endif;?>
    <ul class="nav flex-column">
  <?php if(intval(Yii::$app->user->id) === intval($profile->player_id) || (!Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin)):?>
          <li class="nav-item text-center"><?=Html::a("<i class='fas fa-user-shield'></i> OpenVPN", ['profile/ovpn'], ['class'=>'btn btn-primary','alt'=>'Download OpenVPN Configuration'])?> <?=Html::a("<i class='fas fa-id-badge'></i> Your badge", ['profile/badge','id'=>$profile->id], ['class'=>'btn btn-success'])?></li>
          <li class="nav-item text-left"><strong><i class="fa fa-eye"></i> Visibility</strong> <span class="pull-right"><?=$profile->visibilities[$profile->visibility]?></span></li>
          <li class="nav-item text-left"><strong><i class="fas fa-sync-alt"></i> Spins</strong> <span class="pull-right"><abbr title="Spins today"><?=intval($profile->spins->counter)?></abbr> / <abbr title="Total Spins"><?=intval($profile->spins->total)?></abbr></span></li>
  <?php endif;?>
          <li class="nav-item text-left"><strong><i class="fas fa-file-signature"></i> Real name</strong> <span class="pull-right"><?=Html::encode($profile->owner->fullname)?></span></li>
          <li class="nav-item text-left"><strong><i class="fas fa-globe"></i> Country</strong> <span class="pull-right"><?=$profile->rCountry->name?></span></li>
          <li class="nav-item text-left"><strong><i class="fas fa-calendar-check"></i> Joined</strong> <span class="pull-right"><?=date("d.m.Y", strtotime($profile->owner->created))?></span></li>
          <li class="nav-item text-left"><strong><i class="far fa-calendar-alt"></i> Last seen</strong> <span class="pull-right"><?=date("d.m.Y", strtotime($profile->last->on_pui))?></span></li>
          <?php if(trim($profile->twitter)):?><li class="nav-item text-left"><strong><i class="fab fa-twitter"></i> Twitter</strong> <span class="pull-right"><?=Html::a(Html::encode($profile->twitterHandle), "https://twitter.com/".Html::encode($profile->twitter), ['target'=>'_blank'])?></span></li><?php endif;?>
          <?php if(trim($profile->github)):?><li class="nav-item text-left"><strong><i class="fab fa-github"></i> Github</strong> <span class="pull-right"><?=Html::a(Html::encode($profile->github), "https://github.com/".Html::encode($profile->github), ['target'=>'_blank'])?></span></li><?php endif;?>
          <?php if(trim($profile->discord)):?><li class="nav-item text-left"><strong><i class="fab fa-discord"></i> Discord</strong> <span class="pull-right"><?=Html::encode($profile->discord)?></span></li><?php endif;?>
          <?php if(trim($profile->htb)):?><li class="nav-item text-left"><strong>HTB</strong> <span class="pull-right"><small><?=Html::a("https://hackthebox.eu/profile/".Html::encode($profile->htb), "https://hackthebox.eu/profile/".Html::encode($profile->htb), ['target'=>'_blank'])?></small></span></li><?php endif;?>
      </ul>
      <hr/>
      <ul class="nav flex-column">
        <li class="nav-header text-left"><h6>Details</h6></li>
        <li class="nav-item text-left"><strong><i class="fa fa-signal"></i> Current Rank</strong> <span class="pull-right"><?php if($profile->rank) echo $profile->rank->ordinalPlace?></span></li>
        <li class="nav-item text-left"><strong><i class="fa fa-list"></i> Points</strong> <span class="pull-right"><?=number_format($profile->owner->playerScore->points)?></span></li>
        <li class="nav-item text-left"><strong><i class="fas fa-flag"></i> Flags</strong> <span class="pull-right"><?php echo $profile->totalTreasures;?></span></li>
        <li class="nav-item text-left"><strong><i class="fas fa-fire"></i> Findings</strong> <span class="pull-right"><?php echo $profile->totalFindings;?></span></li>
        <li class="nav-item text-left"><strong><i class="fas fa-tasks"></i> Challenges</strong> <span class="pull-right"><?=count($profile->owner->challengeSolvers)?></span></li>
      </ul>
  </div>
</div><!-- // end user profile card -->
