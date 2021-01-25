/* eslint-disable */
/**
 *
 */
function MobileDetect () {
    
}

MobileDetect.Android = function() {
    return navigator.userAgent.match(/Android/i);
};

MobileDetect.BlackBerry = function() {
    return navigator.userAgent.match(/BlackBerry/i);
};

MobileDetect.iOS = function() {
    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
};

MobileDetect.Opera = function() {
    return navigator.userAgent.match(/Opera Mini/i);
};

MobileDetect.Windows = function() {
    return navigator.userAgent.match(/IEMobile/i) || navigator.userAgent.match(/WPDesktop/i);
};

MobileDetect.any = function() {
    return (MobileDetect.Android() || MobileDetect.BlackBerry() || MobileDetect.iOS() || MobileDetect.Opera() || MobileDetect.Windows());
};
