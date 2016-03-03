function enc(s) {
    return encodeURIComponent(s);
}

function addSecret() {
    return "&s=" + enc(getSecret());
}

function loadTemplate(templateId) {
    var notesEditorTemplate = $("#" + templateId).html();
    var notesEditorHtml = Handlebars.compile(notesEditorTemplate);
    $("#content").html(notesEditorHtml);
}

function applyEditorEvents() {
    $(".save").on("click", function() {
        saveNote(CKEDITOR.instances.editor.getData());
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

    CKEDITOR.instances.editor.document.on("keyup", function(e) {
        if ($(".save").hasClass("success")) {
            $(".save").removeClass("success").addClass("warning");
        }
    });
}

function loadNote(noteId, callback) {
    $.getJSON("services.php?a=load-note&n=" + noteId + addSecret(), function (json) {
        if (json.hasOwnProperty("status")) {
            $(".message").html(json.message).addClass("callout").addClass("warning");
        } else {
            loadTemplate("notes-editor");

            CKEDITOR.replace("editor");

            $("#editor").val(json.text);
            console.log("json.modified.date: " + json.modified.date);
            $(".last-saved").html(new Date(json.modified.date));

            CKEDITOR.instances.editor.on("instanceReady", function() {
                callback();
            });
        }
    });
}

function saveNote(text) {
    $.ajax({
        type: "POST",
        url: "services.php",
        data: "a=save-note&n=" + enc(getNoteId()) + "&text=" + enc(text) + addSecret(),
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

var init = function() {
    "use strict";
    switch(getAction()) {
        case "note":
            var noteId = getNoteId();
            var secret = undefined !== getSecret() ? getSecret() : "";
            if (undefined === noteId || noteId === null) {
                window.location = "#/note/home/" + secret;
            }
            break;
        default:
            window.location.hash = "#/note/home/";
            break;
    }
};

function getHashVars() {
    "use strict";
    var hash = window.location.hash;
    hash = hash.replace(/^#/, "");
    return hash.split("/");
}

function getAction() {
    "use strict";
    return getHashVars()[1];
}

function getNoteId() {
    "use strict";
    return getHashVars()[2];
}

function getSecret() {
    "use strict";
    return getHashVars()[3];
}

$(document).ready(function() {
    init();
    $(window).on("hashchange", function() {
        init();
    });

    FastClick.attach(document.body);

    // Note: CKEDITOR is initialized in here. Events are attached in here.
    loadNote(getNoteId(), applyEditorEvents);

    $(document).bind("keydown", "ctrl+s", function() {
        saveNote(CKEDITOR.instances.editor.getData());
    });
});
