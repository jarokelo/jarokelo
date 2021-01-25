/* global $, admin */
(function($, admin) {
    var fieldMap = [
        {'selector': '#statuschange-solutiondate-kvdate', 'status': [8], 'hidden': true},
        {'selector': '#statuschange-reason', 'status': [6], 'hidden': true},
        {'selector': '#statuschange-comment', 'status': [5, 6], 'hidden': true},
        {'selector': '#hidden-status-desc', 'status': [0, 1], 'hidden': true}
    ];
    return $.extend(admin, {
        Helper: {
            queryHtmlContent: function($this, e, successCallback, errorCallback) {
                var url = $this.data('url');
                var $target = $($this.data('target'));
                var extra = $this.data('extra') || '';
                $.ajax({
                    method: 'get',
                    url: url + extra,
                    success: function(data) {
                        if (data && data.success) {
                            if (data.html) {
                                $target.html(data.html);
                            }
                            if (successCallback) {
                                successCallback(data);
                            }
                        } else if (errorCallback) {
                            errorCallback(data);
                        }
                    }
                });
                if (e) {
                    e.preventDefault();
                }
                return false;
            },
            postData: function($this, data, e, successCallback, errorCallback) {
                var url = $this.data('url');
                $.ajax({
                    method: 'post',
                    url: url,
                    data: data,
                    success: function(respData) {
                        if (respData && respData.success) {
                            if (successCallback) {
                                successCallback(respData);
                            }
                        } else if (errorCallback) {
                            errorCallback(respData);
                        }
                    }
                });
                if (e) {
                    e.preventDefault();
                }
                return false;
            },
            statusChange: function(val) {
                for (var i = 0; i < fieldMap.length; ++i) {
                    var field = fieldMap[i];
                    var inArray = field.status.indexOf(Number(val)) !== -1;
                    if ((inArray && !field.hidden) || (!inArray && field.hidden)) {
                        continue;
                    }
                    var $field = $(field.selector).parent();
                    if (field.hidden) {
                        $field.removeClass('hidden');
                    } else {
                        $field.addClass('hidden');
                    }
                    field.hidden = !field.hidden;
                }
            }
        }
    });
})($, admin || {});
