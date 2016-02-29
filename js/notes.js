function enc(s) {
    return encodeURIComponent(s);
}

function addSecret() {
    return "&s=" + enc($("#secret").val());
}

function loadNote(noteId) {
    $.getJSON("services.php?n=" + noteId + addSecret(), function (json) {
        if (json.hasOwnProperty("status")) {
            $(".message").html(json.message).addClass("callout").addClass("warning");
        } else {
            CKEDITOR.replace("editor");
            $("#editor").val(json.text);
            $(".last-saved").html(new Date(json.modified.date));
        }
    });
}

function saveNote(noteId, text) {
    $.ajax({
        type: "POST",
        url: "services.php",
        data: "n=" + enc(noteId) + "&text=" + enc(text) + addSecret(),
        dataType: "json",
        success: function(json) {
            if (json.status === "ok") {
                $(".last-saved").fadeOut("fast");
                $(".save").fadeOut("fast");
                $(".last-saved").html(new Date());
                $(".last-saved").fadeIn("slow");
                $(".save").fadeIn("slow");
            } else {
                $(".message").html(json.message).addClass("callout").addClass("warning");
            }
        }
    });
}

$(document).ready(function() {
    //CKEDITOR.instances.editor.on("saveSnapshot", function(e) { });

    FastClick.attach(document.body);

    loadNote($("#noteId").val());

    $(".save").on("click", function() {
        saveNote($("#noteId").val(), CKEDITOR.instances.editor.getData());
    });

    var offset = $(".scroller-save").offset();  
    $(window).scroll(function () {  
        var scrollTop = $(window).scrollTop();
        if (offset.top < scrollTop) {
            $(".scroller-save").addClass("scroller");
        } else {
            $(".scroller-save").removeClass("scroller");
        }
    });  
});
