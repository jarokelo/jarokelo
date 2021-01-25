<?php
/* @var \yii\web\View $this */
/* @var \app\models\db\Report $model */

// source: https://github.com/mpdf/mpdf-examples/blob/master/example18_headers_method_4.php
?>
    <!-- defines the headers/footers - this must occur before the headers/footers are set -->
<!--mpdf
<htmlpageheader name="myHTMLHeader1">
    <div class="header">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td><?= $model->getShortUrl() ?></td>
            <td align="right"><?= $model->getUniqueName() ?></td>
        </tr>
        </table>
    </div>
</htmlpageheader>
mpdf-->
<!-- set the headers/footers - they will occur from here on in the document -->
<!--mpdf
<sethtmlpageheader name="myHTMLHeader1" page="O" value="on" show-this-page="1" />
mpdf-->