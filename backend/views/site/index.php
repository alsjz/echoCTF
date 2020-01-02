<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\gameplay\models\Target;
use app\modules\gameplay\models\Finding;
use app\modules\gameplay\models\Treasure;
use app\modules\gameplay\models\Challenge;
use app\modules\gameplay\models\Question;
use app\modules\frontend\models\Player;
use app\modules\frontend\models\Team;
use app\modules\activity\models\Report;
use app\modules\activity\models\Stream;
use app\modules\activity\models\PlayerTreasure;
use app\modules\activity\models\PlayerFinding;
use app\modules\activity\models\PlayerQuestion;
use app\modules\activity\models\PlayerScore;
/* @var $this yii\web\View */
//$query = (new \yii\db\Query());
$this->title = 'echoCTF mUI';
?>
<div class="site-index">

    <div class="jumbotron">
        <img src="/images/logo.png" width="512"/>
        <h1>echoCTF Management interface</h1>

    </div>

    <div class="body-content">
<?php if(!Yii::$app->user->isGuest): ?>

        <div class="row">
            <div class="col-lg-3">
                <h2>System</h2>
                <p>
                  <ul>
                    <li><?= Html::a('Players &raquo;', ['/frontend/player'] ) ?>: <abbr title="Total players"><?=Player::find()->count()?></abbr> / <abbr title="Active players"><?=Player::find()->where(['active'=>1])->count()?></abbr> / <abbr title="Online"><?=Player::find()->having(['>','online',1])->count()?></abbr> / <abbr title="On VPN"><?=Player::find()->having(['>','ovpn',0])->count()?></abbr>
                    <li><?= Html::a('Teams &raquo;', ['/frontend/team'] ) ?>: <?=Team::find()->count()?>
                    <li><?= Html::a('Targets &raquo;', ['/gameplay/target'] ) ?>: <?=Target::find()->count()?> / <?=Target::find()->where(['active'=>1])->count()?>
                    <li><?= Html::a('Challenges &raquo;', ['/gameplay/challenge'] ) ?>: <?=Challenge::find()->count()?>
                    <li><?= Html::a('Questions &raquo;', ['/gameplay/question'] ) ?>: <?=Question::find()->count()?>
                    <li><?= Html::a('Findings &raquo;', ['/gameplay/finding'] ) ?>: <?=Finding::find()->count()?> / <?=(int) (new \yii\db\Query())->from('finding')->sum('points');?>pts
                    <li><?= Html::a('Treasures &raquo;', ['/gameplay/treasure'] ) ?>: <?=Treasure::find()->count()?> / <?=(int) (new \yii\db\Query())->from('treasure')->sum('points');?>pts
                  </ul>
                </p>
            </div>
            <div class="col-lg-3">
                <h2>Activity</h2>
                <p>
                  <ul>
                    <li><?= Html::a('Player Score &raquo;', ['/activity/player-score'] ) ?>: <abbr title="Players with non zero scores"><?=PlayerScore::find()->where(['>', 'points', 0])->count()?></abbr> / <abbr title="Players with zero scores"><?=PlayerScore::find()->where(['points'=>0])->count()?></abbr>
                    <li><?= Html::a('Player Treasures &raquo;', ['/activity/playertreasure'] ) ?>: <abbr title="Total player treasure records"><?=PlayerTreasure::find()->count()?></abbr> / <abbr title="Distinct players on player treasure"><?=(new \yii\db\Query())->from('player_treasure')->select('player_id')->distinct()->count()?></abbr> / <abbr title="Distinct treasure on player treasure"><?=(new \yii\db\Query())->from('player_treasure')->select('treasure_id')->distinct()->count()?></abbr>
                    <li><?= Html::a('Player Findings &raquo;', ['/activity/playerfinding'] ) ?>: <abbr title="Total player finding records"><?=PlayerFinding::find()->count()?></abbr> / <abbr title="Distinct players on player finding"><?=(new \yii\db\Query())->from('player_finding')->select('player_id')->distinct()->count()?></abbr> / <abbr title="Distinct finding on player finding"><?=(new \yii\db\Query())->from('player_finding')->select('finding_id')->distinct()->count()?></abbr>
                    <li><?= Html::a('Player Questions &raquo;', ['/activity/playerquestion'] ) ?>: <abbr title="Total player question records"><?=PlayerQuestion::find()->count()?></abbr> / <abbr title="Distinct players on player question"><?=(new \yii\db\Query())->from('player_question')->select('player_id')->distinct()->count()?></abbr> / <abbr title="Distinct question on player question"><?=(new \yii\db\Query())->from('player_question')->select('question_id')->distinct()->count()?></abbr>
                  </ul>
                </p>
            </div>
            <div class="col-lg-3">
                <h2>Last entries</h2>
                <p>
                  <?= Html::ul(ArrayHelper::map(Report::find()->where(['status' => 'pending'])->all(),'id',function($model) {
                          return Html::a($model['title'],['activity/report/view','id'=>$model['id']]);}),['encode'=>false]) ?>
                </p>
                <p>
                  <?= Html::a(sprintf("Player %d: %s", Player::find()->limit(1)->orderBy('id desc')->one()->id, Player::find()->limit(1)->orderBy('id desc')->one()->username), ['/frontend/player/view','id'=>Player::find()->limit(1)->orderBy('id desc')->one()->id] ) ?><br/>
                  <?= Html::a(sprintf("Stream %d: %s", Stream::find()->limit(1)->orderBy('id desc')->one()->id, Stream::find()->limit(1)->orderBy('id desc')->one()->formatted), ['/activity/stream/view','id'=>Stream::find()->limit(1)->orderBy('id desc')->one()->id] ) ?>
                </p>

            </div>
            <div class="col-lg-3">
                <h2>Containers</h2>
                <p>
                  <?= Html::ul(ArrayHelper::map(Target::find()->where(['!=','image', ''])->all(),'id',function($model) {
                          return Html::a($model['name'].' | '.$model['ipoctet'].' | '.$model['server'],['gameplay/target/view','id'=>$model['id']]);}),['encode'=>false]) ?>
                </p>
            </div>
        </div>
<?php endif;?>
    </div>
</div>