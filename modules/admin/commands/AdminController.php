<?php

namespace app\modules\admin\commands;

use app\models\db\Admin;
use app\modules\admin\models\AdminForm;

use yii\console\Controller;

/**
 * Console controller for creating Superadmins.
 *
 * @package app\modules\admin\commands
 */
class AdminController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'create';

    /**
     * Creates a new Admin, with Superadmin privileges.
     */
    public function actionCreate()
    {
        $admin = new AdminForm();
        $admin->setScenario('create');
        $admin->status = Admin::STATUS_SUPER_ADMIN;
        $admin->email = $this->prompt($admin->getAttributeLabel('email'));
        $admin->last_name = 'Super';
        $admin->first_name = 'Admin';
// @codingStandardsIgnoreStart
        `/bin/stty -echo`;
// @codingStandardsIgnoreEnd

        $admin->password = $this->prompt($admin->getAttributeLabel('password'));
        $this->stdout("\n");

        $admin->password_repeat = $this->prompt($admin->getAttributeLabel('password_repeat'));
        $this->stdout("\n");

// @codingStandardsIgnoreStart
        `/bin/stty echo`;
// @codingStandardsIgnoreEnd

        if (!$admin->save()) {
            foreach ($admin->errors as $attribute => $errors) {
                foreach ($errors as $error) {
                    $this->stdout("{$error}\n");
                }
            }
        }
    }
}
