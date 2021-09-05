<?php

namespace app\modules\admin\components\widgets;

use yii\base\Widget;

class RelationHandlerWidget extends Widget
{
    /**
     * @var string
     */
    public $formName;

    /**
     * @var string
     */
    public $label;

    /**
     * @var array
     */
    public $selection;

    /**
     * @var string
     */
    public $urlName;

    /**
     * @var array
     */
    public $existingRelations = [];

    /**
     * @inheritDoc
     */
    public function run()
    {
        uasort(
            $this->existingRelations,
            function ($a, $b) {
                if (!isset($a['priority']) || !isset($b['priority'])) {
                    return;
                }

                // PHP 7 version - $a['priority'] <=> $b['priority]
                return ($a['priority'] < $b['priority'])
                    ? -1
                    : (($a['priority'] > $b['priority']) ? 1 : 0);
            }
        );

        return $this->render(
            '/widgets/relation',
            [
                'formName' => $this->formName,
                'existingRelations' => $this->existingRelations,
                'label' => $this->label,
                'selection' => $this->selection,
                'urlName' => $this->urlName,
            ]
        );
    }
}
