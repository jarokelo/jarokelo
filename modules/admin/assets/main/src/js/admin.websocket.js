/* global $, admin, webSocketUrl */
(function($, admin) {
    var websocket = new WebSocket(webSocketUrl);
    var lockedReports;
    /*
     Other people will see that the report is being edited
     */
    var addTooltip = function(obj, userName) {
        obj.tooltip({
            'title': 'Currently edited by ' + userName
        });
    };
    var lockReportForOthers = function() {
        if (!$('.update-report-form').length) {
            return;
        }
        var reportId = $('#reportId').val();
        var adminId = $('#adminId').val();
        setTimeout(function() {
            websocket.send(JSON.stringify({
                event: 'lock',
                adminId: adminId,
                reportId: reportId
            }));
        }, 1000);
    };
    var disableEditFunctionOnReportViewPage = function(counter, eventType) {
        if (counter === 0) {
            return;
        }
        var reportInfo;
        while (counter--) {
            reportInfo = lockedReports[counter];
            // Disable "Edit report" button
            var $btn = $('[data-report-id="' + reportInfo.reportId + '"]');
            if (eventType !== 'unlock') {
                $btn.attr('disabled', true);
                $btn.data('href', $btn.attr('href')).removeAttr('href');
                addTooltip($btn, reportInfo.adminName);
            } else {
                $btn.attr('disabled', false);
                $btn.attr('href', $btn.data('href'));
                $btn.tooltip('destroy');
            }
        }
    };
    var disableEditFunctionOnReportListPage = function(counter, eventType) {
        if (counter === 0) {
            return;
        }
        var reportInfo;
        while (counter--) {
            reportInfo = lockedReports[counter];
            var $row = $('[data-report-id="' + reportInfo.reportId + '"]');
            var $link = $row.find('a');
            if (eventType !== 'unlock') {
                $row.addClass('is-disabled');
                $link.data('href', $link.attr('href')).removeAttr('href');
                addTooltip($row, reportInfo.adminName);
            } else {
                $row.removeClass('is-disabled');
                $link.attr('href', $link.data('href'));
                $row.tooltip('destroy');
            }
        }
    };
    /*
     Get locked reports, and update the current page if it's affected
     */
    var getLockedReports = function() {
        if ($('#report-grid').length || $('#new').length || $('.report-view-page').length) {
            websocket.onmessage = function(event) {
                // get messages
                var message = JSON.parse(event.data);
                lockedReports = [];
                if (message.hasOwnProperty('lockedReports')) {
                    if (message.lockedReports.length) {
                        var i = message.lockedReports.length;
                        while (i--) {
                            var item = message.lockedReports[i];
                            lockedReports.push(item);
                        }
                    }
                } else {
                    lockedReports.push(message);
                }
                if ($('.report-view-page').length) {
                    disableEditFunctionOnReportViewPage(lockedReports.length, message.event);
                } else {
                    disableEditFunctionOnReportListPage(lockedReports.length, message.event);
                }
            };
        }
    };
    return $.extend(admin, {
        Websocket: {
            init: function() {
                lockReportForOthers();
                getLockedReports();
            }
        }
    });
})($, admin || {});
$(document).ready(function() {
    admin.Websocket.init();
});
