<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use yii\bootstrap\ActiveForm;
use app\widgets\Twitter;

/* @var $this yii\web\View */
/* @var $model app\models\Challenge */
$this->title=Html::encode(Yii::$app->sys->event_name.' Challenges / (ID#'.$model->id.') '.$model->name);
$this->_description=\yii\helpers\StringHelper::truncateWords(strip_tags($model->description), 15);
$this->_url=\yii\helpers\Url::to(['view', 'id'=>$model->id], 'https');

?>
<div class="challenge-view">
  <div class="body-content">
    <div class="row">
      <div class="col-lg-8 col-md-6 col-sm-6">
        <div class="well">
          <h2><b><?=Html::encode($model->name).' (ID#'.$model->id.')'?> <?php if($model->completed):?><i class="fas fa-check-double"></i> <?=Twitter::widget(['message'=>'Hey check this out, I completed the challenge '.$model->name]);?><?php else:?><?=Twitter::widget(['message'=>'I currently grinding the challenge '.$model->name]);?><?php endif;?></b></h2>
          <h4><b>Category:</b> <?=Html::encode($model->category);?></h4>
          <h4><b>Difficulty:</b> <?=Html::encode($model->difficulty)?></h4>
          <h4><b>Points:</b> <?=Html::encode(number_format($model->points));?></h4>
          <?=trim($model->filename) !== '' ? '<h4><b>Challenge file:</b> '.Html::a($model->filename, ['/uploads/'.$model->filename], ['data-pjax'=>"0"]).'</h4>' : ''?>
          <p><?=$model->description;?></p>
        </div>
        <?php
          echo ListView::widget([
              'dataProvider' => $dataProvider,
              'summary'=>false,
              'itemOptions' => [
                'tag' => false
              ],
              'itemView' => '_question',
          ]);
        ?>
<?php $form=ActiveForm::begin([
    'enableClientValidation' => false,
    'id' => 'answer-form',
    'options'=>['autocomplete'=>'off'],
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "<div class=\"row\"><div class=\"control-label col-lg-1\">{label}</div>\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-8\">{error}</div></div>",
        //'labelOptions' => ['class' => 'control-label col-lg-2'],
    ],
    ]);?>
<?php if(!$model->completed):?>
    <hr/>
    <?=$form->field($answer, 'answer')->textInput(['autofocus' => true, 'autocomplete'=>'off']) ?>
    <?=Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'answer-button'])?>
<?php endif;?>
    <?php ActiveForm::end();?>
  </div>
  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="card bg-dark solves">
      <h4><i class="fas fa-tasks"></i> <?=$solvers->count()?> Solvers (older first)</h4>
      <div class="card-body table-responsive"><?php
        $solves=[];
        foreach($solvers->orderBy(['created_at'=>SORT_ASC, 'player_id'=>SORT_ASC])->all() as $solver)
        {
          if((int) $solver->player->active === 1)
            $solves[]=$solver->player->profile->link;
        }
        if(!empty($solves))
        {
          echo "<code>",implode(", ", array_slice($solves, 0,19)),"</code>";
          if(count($solves)>19){
            echo "<details class=\"headshotters\">";
            echo "<summary data-open=\"Hide more\" data-close=\"Show more\"></summary>";
            echo "<code>",implode(", ", array_slice($solves, 19)),"</code>";
            echo "</details>";
          }
        }
        else
          echo '<code>none yet...</code>';?>
      </div>
    </div>
  </div>

  </div>
</div>
</div>
