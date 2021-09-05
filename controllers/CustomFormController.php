<?php

namespace app\controllers;

use app\models\db\CustomFormRelation;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class CustomFormController extends Controller
{
    /**
     * @return array
     */
    protected function getCustomQuestions()
    {
        $entityId = Yii::$app->request->get('entity_id');
        $type = Yii::$app->request->get('type');

        if (!$entityId || !$type) {
            return [];
        }

        /** @var CustomFormRelation[] $relations */
        $relations = CustomFormRelation::getRelationsById($entityId, $type);
        $result = [];

        foreach ($relations as $relation) {
            $rel = $relation->customForm;
            $data = $rel->toArray();
            $data['customQuestions'] = $rel->customQuestions;
            $data['priority'] = $relation->priority;
            $result[] = $data;
        }

        uasort($result, function ($a, $b) {
            if (!isset($a['priority']) || !isset($b['priority'])) {
                return;
            }

            // PHP 7 version - $a['priority'] <=> $b['priority]
            return ($a['priority'] < $b['priority'])
                ? -1
                : (($a['priority'] > $b['priority']) ? 1 : 0);
        });

        return $result;
    }

    /**
     * @return array
     */
    public function actionGetCustomFormByRelation()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isGet) {
            return [];
        }

        return $this->getCustomQuestions();
    }
}
