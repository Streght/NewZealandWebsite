function displayImg(link, src) {

    document.getElementById('copyright').style.display = 'none';

    var img = new Image(),
            overlay = document.getElementById('overlay');

    img.onload = function () {
        overlay.innerHTML = '';
        overlay.appendChild(img);
    };

    img.src = src.replace('src', link.getAttribute("href"));

    $(overlay).fadeIn("slow");
}