<?php
use yii\widgets\ListView;
echo ListView::widget([
    'id'=>$divID,
    'options'=>$options,
    'dataProvider' => $dataProvider,
    'pager'=>[
      'class'=>'yii\bootstrap4\LinkPager',
      'options'=>$pagerOptions,
      'firstPageLabel' => '<i class="fas fa-step-backward"></i>',
      'lastPageLabel' => '<i class="fas fa-step-forward"></i>',
      'maxButtonCount'=>3,
      'disableCurrentPageButton'=>true,
      'prevPageLabel'=>'<i class="fas fa-chevron-left"></i>',
      'nextPageLabel'=>'<i class="fas fa-chevron-right"></i>',
    ],
    'layout'=>$layout,
    'summary'=>$summary,
    'itemOptions' => [
      'tag' => false
    ],
    'itemView' => '_stream',
]);
