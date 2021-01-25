<?php

use \app\components\helpers\GmailApi;
use yii\helpers\Json;

/**
 * @var \yii\base\View $this
 */
?>

<script>
    /**
     * Google popup, to handle frontend token refresh on backend, which is retrieved via Oauth
     * Default Google popup doesn't support it.
     *
     * @param buttonPlaceId
     * @constructor
     */
    function TokenRetriever(buttonPlaceId) {
        var HANDLE_URL = '<?=(new GmailApi())->getConfig('redirect_uri')?>';
        var LANG_BUTTON = <?=Json::encode(\Yii::t('data', 'gmail-store.button'))?>;
        var LANG_SUCCESS = <?=Json::encode(\Yii::t('data', 'gmail-store.success'))?>;
        var authBtn = null;
        var authUrl = null;
        var childWindow = null;

        function PopupCenter(url, title, w, h) {
            // Fixes dual-screen position                         Most browsers      Firefox
            var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
            var dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;

            var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            var systemZoom = width / window.screen.availWidth;
            var left = (width - w) / 2 / systemZoom + dualScreenLeft;
            var top = (height - h) / 2 / systemZoom + dualScreenTop;
            var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w / systemZoom + ', height=' + h / systemZoom + ', top=' + top + ', left=' + left);

            // Puts focus on the newWindow
            if (window.focus) newWindow.focus();

            return newWindow;
        }

        jQuery(document).ready(function(){
            window.setGoogleAuthCode = function(code) {
                jQuery.post(HANDLE_URL, {'type': 'store', 'code': code}, function(response) {
                    var msg = response.error ? response.error : LANG_SUCCESS;
                    if (!response.error && response.email) {
                        $('#email_address').val(response.email);
                        $('#email_address_fake').val(response.email)
                    }
                    alert(msg);
                });
                childWindow.close();
            };

            //get code from google oauth popup
            var code = (new URLSearchParams(document.location.search)).get('code');

            if (code !== null) {
                window.opener.setGoogleAuthCode(code);
            } else {
                jQuery.post(HANDLE_URL, {type: 'get-auth-url'}, function (response) {
                    $(buttonPlaceId).html('<a class="btn btn-primary get-token" href="javascript:;">' + LANG_BUTTON + '</button>');
                    authUrl = response['auth-url'];
                    authBtn = $(buttonPlaceId).find('.get-token');
                    authBtn.click(function () {
                        childWindow = PopupCenter(authUrl, "Auth window", 700, 800);
                    });
                });
            }
        });
    }
</script>

<div id="gauthbuttonplace"></div>
<?php $this->registerJs("new TokenRetriever('#gauthbuttonplace');") ?>
