<?php

namespace app\modules\admin\models;

use app\models\db\Admin;

use Yii;

use yii\base\Model;

/**
 * Permission form for editing the Admin.
 *
 * @package app\modules\admin\models
 */
class PermissionForm extends Model
{
    /**
     * @var boolean
     */
    public $reportEdit;

    /**
     * @var boolean
     */
    public $reportDelete;

    /**
     * @var boolean
     */
    public $reportStatistics;

    /**
     * @var boolean
     */
    public $adminView;

    /**
     * @var boolean
     */
    public $adminEdit;

    /**
     * @var boolean
     */
    public $adminAdd;

    /**
     * @var boolean
     */
    public $adminDelete;

    /**
     * @var boolean
     */
    public $cityView;

    /**
     * @var boolean
     */
    public $cityEdit;

    /**
     * @var boolean
     */
    public $cityAdd;

    /**
     * @var boolean
     */
    public $userView;

    /**
     * @var boolean
     */
    public $userEdit;

    /**
     * @var boolean
     */
    public $userAdd;

    /**
     * @var boolean
     */
    public $userDelete;

    /**
     * @var boolean
     */
    public $userKill;

    /**
     * @var boolean
     */
    public $userFullDataExport;

    /**
     * @var boolean
     */
    public $institutionView;

    /**
     * @var boolean
     */
    public $institutionEdit;

    /**
     * @var boolean
     */
    public $institutionAdd;

    /**
     * @var boolean
     */
    public $institutionDelete;

    /**
     * @var boolean
     */
    public $prPageEdit;

    /**
     * @var boolean
     */
    public $prPageDelete;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['prPageDelete', 'prPageEdit', 'reportEdit', 'reportStatistics', 'reportDelete', 'adminView', 'adminEdit', 'adminAdd', 'adminDelete', 'cityView', 'cityEdit', 'cityAdd', 'userView', 'userEdit', 'userDelete', 'userKill', 'userFullDataExport', 'institutionView', 'institutionEdit', 'institutionAdd', 'institutionDelete'], 'required'],
            [['prPageDelete', 'prPageEdit', 'reportEdit', 'reportStatistics', 'reportDelete', 'adminView', 'adminEdit', 'adminAdd', 'adminDelete', 'cityView', 'cityEdit', 'cityAdd', 'userView', 'userEdit', 'userDelete', 'userKill', 'userFullDataExport', 'institutionView', 'institutionEdit', 'institutionAdd', 'institutionDelete'], 'boolean'],
        ];
    }

    public static function permissionMap()
    {
        return [
            'reportEdit' => Admin::PERM_REPORT_EDIT,
            'reportStatistics' => Admin::PERM_REPORT_STATISTICS,
            'reportDelete' => Admin::PERM_REPORT_DELETE,
            'adminView' => Admin::PERM_ADMIN_VIEW,
            'adminEdit' => Admin::PERM_ADMIN_EDIT,
            'adminAdd' => Admin::PERM_ADMIN_ADD,
            'adminDelete' => Admin::PERM_ADMIN_DELETE,
            'cityView' => Admin::PERM_CITY_VIEW,
            'cityEdit' => Admin::PERM_CITY_EDIT,
            'cityAdd' => Admin::PERM_CITY_ADD,
            'userView' => Admin::PERM_USER_VIEW,
            'userEdit' => Admin::PERM_USER_EDIT,
            'userDelete' => Admin::PERM_USER_DELETE,
            'userKill' => Admin::PERM_USER_KILL,
            'userFullDataExport' => Admin::PERM_USER_FULL_DATA_EXPORT,
            'institutionView' => Admin::PERM_INSTITUTION_VIEW,
            'institutionEdit' => Admin::PERM_INSTITUTION_EDIT,
            'institutionAdd' => Admin::PERM_INSTITUTION_ADD,
            'institutionDelete' => Admin::PERM_INSTITUTION_DELETE,
            'prPageEdit' => Admin::PERM_PR_PAGE_EDIT,
            'prPageDelete' => Admin::PERM_PR_PAGE_DELETE,
        ];
    }

    /**
     * Loads this model's data from an Admin instance.
     *
     * @param \app\models\db\Admin $admin
     */
    public function loadAdmin($admin)
    {
        foreach (static::permissionMap() as $variable => $permission) {
            $this->{$variable} = $admin->hasPermission($permission);
        }
    }

    /**
     * Applies the current model on the selected Admin.
     *
     * @param \app\models\db\Admin $admin the Admin instance
     * @param bool $save if true, the Admin instance will be saved
     * @return bool true, if the Admin instance was saved
     */
    public function applyChanges($admin, $save = true)
    {
        if (!$this->validate()) {
            return false;
        }

        foreach (static::permissionMap() as $variable => $permission) {
            $admin->setPermission($permission, $this->{$variable});
        }

        return $save ? $admin->save(true, ['permissions', 'updated_at']) : false;
    }
}
