<?php

use dosamigos\chartjs\ChartJs;
use app\components\helpers\SVG;

/** @var array $cities */
/** @var array $resolvedStatistics */
/** @var array $categoryStatistics */
?>
<?= $this->render('_hero'); ?>

<div class="container statistics">
    <div class="container__row--tablet">
        <?= $this->render('_menu'); ?>

        <div class="row center-xs">
            <div class="col-xs-12 col-lg-9">
                <?= $this->render('_city-filter', [
                    'model' => $model,
                ]); ?>

                <?php
                $resolvedValues = [
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

                $resolvedLabels = [];
                foreach ($resolvedStatistics as $stat) {
                    $resolvedLabels[] = $stat['name'];

                    $resolvedValues[0]['data'][] = (int)$stat['inprogress'];
                    $resolvedValues[1]['data'][] = (int)$stat['resolved'];
                    $resolvedValues[2]['data'][] = (int)$stat['unresolved'];

                    $resolvedValues[0]['backgroundColor'][] = 'rgba(61, 85, 207, 1)';
                    $resolvedValues[1]['backgroundColor'][] = 'rgba(155, 209, 88, 1)';
                    $resolvedValues[2]['backgroundColor'][] = 'rgba(255, 94, 94, 1)';
                }
                ?>
            </div>
        </div>
        <div class="row center-xs hidden--mobile">
            <div class="col-xs-12 col-lg-9" style="margin:0 auto;width:800px; height:400px;">
                <?php
                echo ChartJs::widget([
                    'type' => 'horizontalBar',
                    'clientOptions' => [
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
                        'legend' => ['display' => true],
                        'tooltips' => ['enabled' => true],
                        'hover' => ['animationDuration' => 0],
                        'animation' => false,
                    ],
                    'options' => [
                        'responsive' => true,
                        'legend' => [
                            'display' => false,
                        ],
                        'scales' => [
                            'yAxes' => [
                                'ticks' => [
                                    'beginAtZero' => false,
                                ],
                            ],
                            'xAxes' => [
                                'gridLines' => [
                                    'lineWidth' => 0,
                                    'color' => 'rgba(255,255,255,0)',
                                ],
                            ],
                        ],
                    ],
                    'data' => [
                        'labels' => $resolvedLabels,
                        'datasets' => $resolvedValues,
                    ],
                ]);
                ?>
            </div>
        </div>

        <div class="row center-xs hidden--mobile">
            <div class="col-xs-12 col-lg-9" style="margin:0 auto;width:800px; height:500px;">
                <?= $this->render('_city-category-filter', [
                    'model' => $model2,
                ]); ?>
                <?php
                $catStatNames = [];

                $catStatValues = [
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

                foreach ($categoryStatistics as $stat):
                    $catStatNames[] = $stat['name'];

                    $catStatValues[0]['data'][] = (int)$stat['inprogress'];
                    $catStatValues[1]['data'][] = (int)$stat['resolved'];
                    $catStatValues[2]['data'][] = (int)$stat['unresolved'];

                    //$resolvedValues[0]['backgroundColor'][] = 'rgba(75, 192, 192, 1)'; // türkíz
                    $catStatValues[0]['backgroundColor'][] = 'rgba(61, 85, 207, 1)';
                    $catStatValues[1]['backgroundColor'][] = 'rgba(155, 209, 88, 1)';
                    $catStatValues[2]['backgroundColor'][] = 'rgba(255, 94, 94, 1)';
                endforeach;
                ?>
                <?php
                echo '<div>' . ChartJs::widget([
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
                        'responsive' => true,
                        'scales' => [
                            'yAxes' => [
                                'ticks' => [
                                    'beginAtZero' => true,
                                ],
                            ],
                        ],
                    ],
                    'data' => [
                        'labels' => $catStatNames,
                        'datasets' => $catStatValues,
                    ],
                ]) . '</div>';
                ?>
            </div>
        </div>

        <ul class="flex hidden--desktop">
            <?php
            foreach ($resolvedStatistics as $stat) {
                echo '<li class="col-xs-12 col--off--tablet">
                    <div class="accordion accordion--mobile">
                        <div class="accordion__title">
                            <p class="accordion__title__text heading--3">' . $stat['name'] . '</p>' .
                                SVG::icon(SVG::ICON_CHEVRON_DOWN, ['class' => 'accordion__title__dd filter__icon']) .
                                SVG::icon(SVG::ICON_CHEVRON_UP, ['class' => 'accordion__title__dd accordion__title__dd--active filter__icon']) . '<p class="accordion__title__text heading--4">' . $stat['repCount'] . '</p>
                    </div>

                    <div class="accordion__content">
                        <div class="statistics__city__report">
                            ' . $stat['repCount'] . '
                            <p>' . Yii::t('profile', 'report.count') . '</p>
                        </div>
                        <div class="statistics__city__solved">
                            ' . $stat['resolved'] . '
                            <p>' . Yii::t('profile', 'report.solved') . '</p>
                        </div>
                        <div class="statistics__city__unsolved">
                            ' . $stat['unresolved'] . '
                            <p>' . Yii::t('profile', 'report.unsolved') . '</p>
                        </div>
                        <div class="statistics__city__progress">
                            ' . $stat['inprogress'] . '
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
