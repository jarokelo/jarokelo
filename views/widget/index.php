<?php

use app\components\helpers\Link;

if (count($reports) == 0) {
    echo $this->render('@app/views/_snippets/_no-reports-found.php', ['link' => Link::to(Link::REPORTS)]);
}

foreach ($reports as $report) {
    echo $this->render('//report/_card', [
        'report' => $report,
        'wideOnMobile' => true,
    ]);
}
