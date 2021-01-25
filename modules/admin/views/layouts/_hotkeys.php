<!-- Hotkeys Modal -->
<div class="modal fade" id="hotkeysModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('admin', 'hotkeys.title') ?></h4>
            </div>
            <div class="modal-body">
                <h4><?= Yii::t('admin', 'hotkeys.general') ?></h4>
                <div class="row">
                    <div class="col-md-1"><kbd>?</kbd></div>
                    <div class="col-md-7"><?= Yii::t('admin', 'hotkeys.general.help') ?></div>
                </div>
                <h4><?= Yii::t('admin', 'hotkeys.report.view') ?></h4>
                <div class="row">
                    <div class="col-md-1"><kbd>s</kbd></div>
                    <div class="col-md-7"><?= Yii::t('admin', 'hotkeys.report.view.changestatus') ?></div>
                </div>
                <div class="row">
                    <div class="col-md-1"><kbd>e</kbd></div>
                    <div class="col-md-7"><?= Yii::t('admin', 'hotkeys.report.view.edit') ?></div>
                </div>
                <div class="row">
                    <div class="col-md-1"><kbd>m</kbd></div>
                    <div class="col-md-7"><?= Yii::t('admin', 'hotkeys.report.view.send') ?></div>
                </div>
                <div class="row">
                    <div class="col-md-1"><kbd>u</kbd></div>
                    <div class="col-md-7"><?= Yii::t('admin', 'hotkeys.report.view.upload') ?></div>
                </div>
                <div class="row">
                    <div class="col-md-1"><kbd>x</kbd></div>
                    <div class="col-md-7"><?= Yii::t('admin', 'hotkeys.report.view.delete') ?></div>
                </div>
                <div class="row">
                    <div class="col-md-1"><kbd>o</kbd></div>
                    <div class="col-md-7"><?= Yii::t('admin', 'hotkeys.report.view.open') ?></div>
                </div>
                <h4><?= Yii::t('admin', 'hotkeys.report.update') ?></h4>
                <div class="row">
                    <div class="col-md-1"><kbd>c</kbd></div>
                    <div class="col-md-7"><?= Yii::t('admin', 'hotkeys.report.update.compare') ?></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?= Yii::t('label', 'generic.close') ?></button>
            </div>
        </div>
    </div>
</div>
