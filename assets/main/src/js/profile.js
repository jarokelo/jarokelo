/* global $, site, autosize */
(function($, site) {
    'use strict';

    return $.extend(site, {
        Profile: {
            init: function() {
                $(document)
                    .on('change', '#form-profile-filter select, #form-user-city-district #user-city_id, #form-user-city-district #user-district_id', function() {
                        $(this).closest('form').submit();
                    })
                    .on('ajaxComplete', function(event, res) {
                        if ($('#au-profile-picture-container').length === 0) {
                            return false;
                        }
                        var response = $.parseJSON(res.responseText);
                        var userImgChange = response['UserInfoChangeForm[image_file_name]'];

                        if (userImgChange) {
                            $('.profile__user .profile__avatar img').attr('src', userImgChange[0].thumbnailUrl);
                            return true;
                        }
                        return false;
                    });
            }
        }
    });
})(jQuery, site || {});
$(document).ready(function() {
    site.Profile.init();
});
