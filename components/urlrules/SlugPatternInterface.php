<?php

namespace app\components\urlrules;

interface SlugPatternInterface
{
    const CITY_SLUG = '<citySlug:[\w\d\-]+>';
    const INSTITUTION_SLUG = '<institutionSlug:[\w\d\-]+>';
    const DISTRICT_SLUG = '<districtSlug:[\w\d\-]+>';
    const REPORT_SLUG = '<reportSlug:[\w\d\-]+>';
    const DRAFT_SLUG = '<from_id:\d+>';
    const PR_PAGE_SLUG = '<slug:[\w\d\-]+>';
}
