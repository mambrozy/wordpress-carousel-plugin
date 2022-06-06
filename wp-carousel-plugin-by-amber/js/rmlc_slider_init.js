jQuery(document).ready(function($) {
    $("#rmlc_logo_carousel div").jCarouselLite({
        auto: parseInt(setting['interval']),
        speed: parseInt(setting['speed']),
        vertical: parseBool(setting['vertical']),
    });
});
function parseBool(str) {
    if (str.toLowerCase() === 'true')
        return true;
    else
        return false;
}