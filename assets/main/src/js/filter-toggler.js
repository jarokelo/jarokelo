$(function() {
    var filterToggler = {
        init: function() {
            this.bindEvents();
        },
        bindEvents: function() {
            $(document).on('click', '.report-list--filter', this.toggle);
            $(document).on('click', '.front-filter--search-icon-link', this.toggle);
        },
        toggle: function(e) {
            e.preventDefault();
            $('#front-report-search').slideToggle();
        }
    };

    filterToggler.init();
});
