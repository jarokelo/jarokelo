<?php

use dosamigos\chartjs\ChartJs;
use app\components\helpers\SVG;

/** @var array $reportStatistics */
/** @var array $institutionCategoryStatistics */
?>
<?= $this->render('_hero'); ?>

<div class="container statistics">
    <div class="container__row--tablet">
        <?= $this->render('_menu'); ?>

        <div class="row center-xs">
            <div class="col-xs-12 col-lg-9">
                <?= $this->render('_institution-filter', [
                    'model' => $model,
                ]); ?>
            </div>
            <div class="col-xs-12 col-lg-9 hidden--mobile">

                <?php
                $reportStatNames = [];
                $reportStatValues = [];

                foreach (\yii\helpers\ArrayHelper::getValue($reportStatistics, 0, []) as $name => $value) {
                    $reportStatNames[] = Yii::t('const', 'report.' . str_replace('status', 'status.', $name));
                    $reportStatValues[] = $value;
                }

                echo '<div id="chartjs-tooltip"></div><div class="text-center" style="margin:0 auto;width:800px; height:200px;">' . ChartJs::widget([
                    'type' => 'bar',
                    'clientOptions' => [
                        'legend' => ['display' => false],
                        'tooltips' => ['enabled' => true],
                        'hover' => ['animationDuration' => 0],
                        'animation' => false,
                    ],
                    'options' => [
                        'responsive' => false,
                        'width' => 800,
                        'height' => 200,
                        'scales' => [
                            'yAxes' => [
                                'ticks' => [
                                    'beginAtZero' => true,
                                ],
                            ],
                        ],
                    ],
                    'data' => [
                        'labels' => $reportStatNames,
                        'datasets' => [
                            [
                                'label' => 'bejelentések száma',
                                'backgroundColor' => [
                                    'rgba(51, 173, 233, 1)',
                                    'rgba(61, 85, 207, 1)',
                                    'rgba(119, 89, 211, 1)',
                                    'rgba(249, 191, 51, 1)',
                                    'rgba(155, 209, 88, 1)',
                                    'rgba(255, 94, 94, 1)',
                                ],
                                'borderColor' => [
                                    'rgba(255,99,132,1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                ],
                                'borderWidth' => 0,
                                'data' => $reportStatValues,
                            ],
                        ],
                    ],
                ]) . '</div>';
                ?>
            </div>
        </div>


        <div class="hidden--desktop text-center">
            <?php
            foreach (\yii\helpers\ArrayHelper::getValue($reportStatistics, 0, []) as $name => $value) {
                echo '<div class="statistics__institution__report">
                            ' . ($value === null ? 0 : $value) . '
                            <p>' . Yii::t('const', 'report.' . str_replace('status', 'status.', $name)) . '</p>
                        </div>';
            }
            ?>
        </div>

        </br>

        <div class="row center-xs">
            <div class="col-xs-12 col-lg-9">
                <?= $this->render('_institution-category-filter', [
                    'model' => $model2,
                    'model2' => $model,
                ]); ?>
            </div>
            <div class="col-xs-12 col-lg-9 hidden--mobile">
                <?php
                $instStatNames = [];
                $instStatValues = [
                    [
                        'label' => Yii::t('profile', 'report.inprogress'),
                        'data' => [],
                    ],
                    [
                        'label' => Yii::t('profile', 'report.solved'),
                        'data' => [],
                    ],
                    [
                        'label' => Yii::t('profile', 'report.unsolved'),
                        'data' => [],
                    ],
                ];
                foreach ($institutionCategoryStatistics as $istat) {
                    $instStatNames[] = $istat['name'];
                    $instStatValues[0]['data'][] = (int)$istat['inprogress'];
                    $instStatValues[1]['data'][] = (int)$istat['resolved'];
                    $instStatValues[2]['data'][] = (int)$istat['unresolved'];

                    $instStatValues[0]['backgroundColor'][] = 'rgba(61, 85, 207, 1)';
                    $instStatValues[1]['backgroundColor'][] = 'rgba(155, 209, 88, 1)';
                    $instStatValues[2]['backgroundColor'][] = 'rgba(255, 94, 94, 1)';
                }

                echo '<div class="text-center" style="margin:0 auto;width:800px; height:400px;">' . ChartJs::widget([
                    'type' => 'horizontalBar',
                    'clientOptions' => [
                        'legend' => ['display' => true],
                        'tooltips' => ['enabled' => true],
                        'hover' => ['animationDuration' => 0],
                        'animation' => false,
                        'scales' => [
                            'yAxes' => [
                                [
                                    'stacked' => true,
                                ],
                            ],
                            'xAxes' => [
                                [
                                    'stacked' => true,
                                ],
                            ],
                        ],
                    ],
                    'options' => [
                        'responsive' => false,
                        'width' => 800,
                        'height' => 400,
                        'scales' => [
                            'yAxes' => [
                                'ticks' => [
                                    'beginAtZero' => true,
                                ],
                            ],
                        ],
                    ],
                    'data' => [
                        'labels' => $instStatNames,
                        'datasets' => $instStatValues,
                    ],
                ]) . '</div>';
                ?>
            </div>
        </div>

        <ul class="flex hidden--desktop">
            <?php
            foreach ($institutionCategoryStatistics as $istat) {
                    echo '<li class="col-xs-12 col--off--tablet">
                        <div class="accordion accordion--mobile">
                            <div class="accordion__title">
                                <p class="accordion__title__text heading--3">' . $istat['name'] . '</p>' .
                                    SVG::icon(SVG::ICON_CHEVRON_DOWN, ['class' => 'accordion__title__dd filter__icon']) .
                                    SVG::icon(SVG::ICON_CHEVRON_UP, ['class' => 'accordion__title__dd accordion__title__dd--active filter__icon']) . '<p class="accordion__title__text heading--4">' . $istat['repCount'] . '</p>
                        </div>

                        <div class="accordion__content">
                            <div class="statistics__city__report">
                                ' . $istat['repCount'] . '
                                <p>' . Yii::t('profile', 'report.count') . '</p>
                            </div>
                            <div class="statistics__city__solved">
                                ' . $istat['resolved'] . '
                                <p>' . Yii::t('profile', 'report.solved') . '</p>
                            </div>
                            <div class="statistics__city__unsolved">
                                ' . $istat['unresolved'] . '
                                <p>' . Yii::t('profile', 'report.unsolved') . '</p>
                            </div>
                            <div class="statistics__city__progress">
                                ' . $istat['inprogress'] . '
                                <p>' . Yii::t('profile', 'report.inprogress') . '</p>
                            </div>
                        </div>
                    </div>
                    </li>';
                }
                ?>
        </ul>
    </div>
</div>

<?= $this->render('@app/views/_snippets/_hero-bottom-dual');
