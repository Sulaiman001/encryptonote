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
            console.log("json.modified.date: " + json.modified.date);
            $(".last-saved").html(new Date(json.modified.date));

            CKEDITOR.instances.editor.on("instanceReady", function() {
                CKEDITOR.instances.editor.document.on("keyup", function(e) {
                    if ($(".save").hasClass("success")) {
                        $(".save").removeClass("success").addClass("warning");
                    }
                });
            });
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
                if ($(".save").hasClass("warning")) {
                    $(".save").removeClass("warning").addClass("success");
                }
            } else {
                $(".message").html(json.message).addClass("callout").addClass("warning");
            }
        }
    });
}

$(document).ready(function() {
    //console.log(CKEDITOR.instances.editor.getData());

    FastClick.attach(document.body);

    // Note: CKEDITOR is initialized in here. Events are attached in here.
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
