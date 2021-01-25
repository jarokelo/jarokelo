<?php
namespace app\modules\admin\models;

use app\models\db\Institution;

class StatisticsInstitution extends Institution
{
    public $resolved;
    public $unresolved;
    public $waiting_for_response;
    public $waiting_for_solution;
}
