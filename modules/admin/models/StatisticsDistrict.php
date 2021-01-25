<?php
namespace app\modules\admin\models;

use app\models\db\District;

class StatisticsDistrict extends District
{
    public $resolved;
    public $unresolved;
    public $waiting_for_response;
    public $waiting_for_solution;
}
