$(function() {
    var search = {
        init: function() {
            this.cacheDOM();
            this.bindEvents();
            this.toggleFields();
        },
        cacheDOM: function() {
            this.selectorStatusDesktop = '#reportsearch-status';
            this.selectorStatusMobile = '#reportsearch-status-mobile';
            this.selectorCityIdDesktop = '#report-list-container #reportsearch-city_id';
            this.selectorCityIdMobile = '#reportsearch-city_id-mobile';
            this.selectorDistrictIdDesktop = '#reportsearch-district_id';
            this.selectorDistrictIdMobile = '#reportsearch-district_id-mobile';
        },
        bindEvents: function() {
            $(window).on('resize', this.toggleFields);
            $(document).on('ajaxComplete', this.toggleFields);
        },
        toggleFields: function() {
            var windowWidth = window.innerWidth;
            if (windowWidth < 1080) {
                $(search.selectorStatusMobile).prop('disabled', false);
                $(search.selectorCityIdMobile).prop('disabled', false);
                $(search.selectorDistrictIdMobile).prop('disabled', false);
                $(search.selectorStatusDesktop).prop('disabled', 'disabled');
                $(search.selectorCityIdDesktop).prop('disabled', 'disabled');
                $(search.selectorDistrictIdDesktop).prop('disabled', 'disabled');
            } else {
                $(search.selectorStatusDesktop).prop('disabled', false);
                $(search.selectorCityIdDesktop).prop('disabled', false);
                $(search.selectorDistrictIdDesktop).prop('disabled', false);
                $(search.selectorStatusMobile).prop('disabled', 'disabled');
                $(search.selectorCityIdMobile).prop('disabled', 'disabled');
                $(search.selectorDistrictIdMobile).prop('disabled', 'disabled');
            }
        }
    };

    search.init();
});
