    $(document).ready(function () {
    $("#social-icons-jqueryanime li").each(function () {
    $("a strong", this).css("opacity", "0");
    });
    $("#social-icons-jqueryanime li").hover(function () {
    $(this).stop().fadeTo(500, 1).siblings().stop().fadeTo(500, 0.2);
    $("a strong", this).stop().animate({
    opacity: 1,
    top: "-15px"
    }, 300);
    }, function () {
    $(this).stop().fadeTo(500, 1).siblings().stop().fadeTo(500, 1);
    $("a strong", this).stop().animate({
    opacity: 0,
    top: "-1px"
    }, 300);
    });
    });
