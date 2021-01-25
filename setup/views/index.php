<?php
/* @var $environments string[] */
/* @var $selectedEnvironment string */
/* @var $serverGroups string[] */
/* @var $selectedGroup string */

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Setup</title>
    <?php static::renderViewFile(dirname(__FILE__) . '/css.tpl'); ?>
    <script>
        function customRadio(name) {
            var checked = document.querySelector('input[type="radio"][name="' + name + '"]:checked');
            var otherinput = document.querySelector('input:not([type="radio"])[name="' + name + '"]');
            if (checked && checked.hasAttribute('data-custom')) {
                otherinput.disabled = false;
            } else {
                otherinput.disabled = true;
            }
        }

        document.addEventListener("change", function(e) {
            if (e.target.type === 'radio') {
                customRadio(e.target.name);
            }
        });
    </script>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Setup</h1>
    </div>
    <hr>

    <div class="content">

        <?php
            if ($posted) {
                if (count($errors['environment']) || count($errors['group'])) {
                ?>
                    <div class="alert alert-danger" role="alert">Setup Failed</div>
                <?php
                } elseif (count($warnings['environment']) || count($warnings['group'])) {
                ?>
                    <div class="alert alert-warning" role="alert">Setup completed with warnings</div>
                <?php
                } else {
                ?>
                    <div class="alert alert-success" role="alert">Setup completed successfuly</div>
                <?php
                }
                if ($writablePaths !== false) {
                    // attempted to set writable paths
                    $successPaths = array_keys(array_filter($writablePaths));
                    if (count($successPaths)) {
                        ?>
                        <div class="alert alert-info" role="alert"><?= self::PATHS_INFO ?>
                            <ul>
                            <?php foreach ($successPaths as $path) { ?>
                                <li><?= self::encode($path) ?></li>
                            <?php } ?>
                            </ul>
                            <?= self::PATHS_HELP ?>
                        </div>
                        <?php
                    } elseif (!count($writablePaths)) {
                        ?>
                        <div class="alert alert-warning" role="alert"><strong><?= self::PATHS_NONE ?></strong><br/><?= self::PATHS_HELP ?></div>
                        <?php
                    }
                }
            }
        ?>

        <form method="POST" class="form-horizontal">
            <?php
                $validation = '';
                if (!empty($warnings['environment'])) {
                    $validation = 'has-warning';
                }
                if (!empty($errors['environment'])) {
                    $validation = 'has-error';
                }
            ?>
            <div class="form-group <?= $validation ?>">
                <label class="col-sm-2 control-label">Environment</label>
                <div class="col-sm-10">
                    <?php $custom = true; ?>
                    <?php foreach ($environments as $i => $environment) { ?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="environment" id="environment-<?= $i ?>" value="<?= self::encode($environment) ?>" <?= $environment === $selectedEnvironment ? 'checked' : '' ?>>
                            <?php if ($environment === $selectedEnvironment) {
                                $custom = false;
                            } ?>
                            <?= self::encode($environment) ?>
                        </label>
                    </div>
                    <?php } ?>

                    <div class="radio">
                        <label>
                            <input type="radio" name="environment" id="environment-custom" data-custom value="" <?= $custom ? 'checked' : '' ?>>
                            Custom:
                        </label>
                        <input type="text" name="environment" value="<?= $custom ? self::encode($selectedEnvironment) : '' ?>">
                    </div>
                </div> <!-- col-sm-10 -->
                <div class="help-block col-sm-10 col-sm-offset-2">
                    <?php
                        foreach ($errors['environment'] as $error) {
                            echo '<div class="text-danger">';
                            echo self::encode($error);
                            echo '</div>';
                        }
                        foreach ($warnings['environment'] as $warning) {
                            echo '<div class="text-warning">';
                            echo self::encode($warning);
                            echo '</div>';
                        }
                    ?>
                </div> <!-- help-block -->
            </div> <!-- form-group -->

            <?php
                $validation = '';
                if (!empty($warnings['group'])) {
                    $validation = 'has-warning';
                }
                if (!empty($errors['group'])) {
                    $validation = 'has-error';
                }
            ?>
            <div class="form-group <?= $validation ?>">
                <label class="col-sm-2 control-label">Server process group</label>
                <div class="col-sm-10">
                    <?php $custom = true; ?>
                    <?php foreach ($serverGroups as $i => $group) { ?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="group" id="group-<?= $i ?>" value="<?= self::encode($group) ?>" <?= $group === $selectedGroup ? 'checked' : '' ?>>
                            <?php if ($group === $selectedGroup) {
                                $custom = false;
                            } ?>
                            <?= self::encode($group) ?>
                        </label>
                    </div>
                    <?php } ?>

                    <div class="radio">
                        <label>
                            <input type="radio" name="group" id="group-custom" data-custom value="" <?= ($custom && $selectedGroup !== '') ? 'checked' : '' ?>>
                            Custom:
                        </label>
                        <input type="text" name="group" value="<?= $custom ? self::encode($selectedGroup) : '' ?>">
                    </div>

                    <div class="radio">
                        <label>
                            <input type="radio" name="group" id="group-none" value="" <?= ($custom && $selectedGroup === '') ? 'checked' : '' ?>>
                            None (don't change file permissions)
                        </label>
                    </div>
                </div> <!-- col-sm-10 -->
                <div class="help-block col-sm-10 col-sm-offset-2">
                    <?php
                        foreach ($errors['group'] as $error) {
                            echo '<div class="text-danger">';
                            echo self::encode($error);
                            echo '</div>';
                        }
                        foreach ($warnings['group'] as $warning) {
                            echo '<div class="text-warning">';
                            echo self::encode($warning);
                            echo '</div>';
                        }
                    ?>
                </div> <!-- help-block -->
            </div> <!-- form-group -->

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-default">Submit</button>
                </div>
            </div>
        </form>

    </div>

    <script>
        customRadio('environment');
        customRadio('group');
    </script>

    <hr>
</div>
</body>
</html>
