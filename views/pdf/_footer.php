<?php
use app\assets\PdfAsset;

/* @var \yii\web\View $this */

$asset = PdfAsset::register($this);
?>
<div class="footer">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="100"><b>Web cím:</b></td>
            <td width="150"><b>E-mail cím:</b></td>
            <td><b>Postacím:</b></td>
            <td rowspan="2" valign="top" align="right"><img src="<?= $asset->sourcePath . '/images/logo.svg' ?>" width="120"></td>
        </tr>
        <tr>
            <td>myproject.hu</td>
            <td>kerdes@myproject.hu</td>
            <td>Cím</td>
        </tr>
    </table>
</div>