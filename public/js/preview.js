

//onload
function imagePreview() {
    window.addEventListener('DOMContentLoaded', () => {
    var new_tweet_image = document.getElementById("new_tweet_image");
    if (new_tweet_image == null) return;
    new_tweet_image.addEventListener("change", function() {
        // change text in label
        var preview = document.getElementById("preview");
        var label = document.getElementById("label_image");
        var reader = new FileReader();
        reader.readAsDataURL(new_tweet_image.files[0]);
        reader.onload = function() {
            preview.innerHTML = "";
            var image = document.createElement("img");
            label.innerHTML = ""
            image.src = reader.result;
            image.onclick = () => {reload()};
            image.className = "w-8 h-8 rounded-lg object-contain bg-black cursor-pointer object-cover";
            preview.appendChild(image);

        }

    });
});
}