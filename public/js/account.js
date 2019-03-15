var NavDisplay = document.getElementById("account_nav");
var navLink = stickerDisplay.getElementsByClassName("navLink");
var new_display = document.getElementById("new_display");
for (var i = 0; i < navLink.length; i++) {
    navLink[i].addEventListener("click", function() {
        active_photo = document.getElementsByClassName("active2");
        active_photo[0].className = active_photo[0].className.replace(" active2", "");
        this.className += " active2";
        new_display.src = this.src;
    });
}