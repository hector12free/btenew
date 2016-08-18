function showToast(msg) {
    $('#toast').removeClass('error').text(msg).fadeIn(400).delay(3000).fadeOut(400);
}

function showError(msg) {
    $('#toast').addClass('error').text(msg).fadeIn(400).delay(3000).fadeOut(400);
}

window.$E = function(tag) {
    return $(document.createElement(tag || 'div'));
};

String.prototype.format = function () {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function (match, number) {
        return typeof args[number] != 'undefined' ? args[number] : match;
    });
};

