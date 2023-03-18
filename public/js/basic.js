function reload() {
    window.location.reload();
}
function redirect(url) {
    window.location.href = url;
}

function ScrollToTop() {
    window.addEventListener('DOMContentLoaded', () => {

    if (getCookie("scroll") !== null) {
        document.querySelector(".scrolling").scrollTop = getCookie("scroll");

        setCookie("scroll", null);
    }

    document.querySelectorAll("form").forEach(function(form) {
        form.addEventListener("submit", function() {
            setCookie("scroll", document.querySelector(".scrolling").scrollTop);
        });
    });
}
    );
}


function getCookie(name) {
    var cookies = document.cookie.split("; ");
    for (var i = 0; i < cookies.length; i++) {
        var parts = cookies[i].split("=");
        if (parts[0] === name) {
            return decodeURIComponent(parts[1]);
        }
    }
    return null;
}

function setCookie(name, value) {
    var expires = new Date();
    expires.setTime(expires.getTime() + (365 * 24 * 60 * 60 * 1000)); // Set the expiration time to one year from now.
    document.cookie = name + "=" + encodeURIComponent(value) + "; expires=" + expires.toUTCString() + "; path=/";
}

ScrollToTop();