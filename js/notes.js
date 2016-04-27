var favicon = new Favico({
    bgColor: '#da8851',
    animation: 'popFade'
});
var badgeCount = 0;

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

prevtime = parseInt(new Date().getTime());
// Waits x milliseconds before performing search.
threshold = 1500;
curval = "";
t = null;

prevtime2 = parseInt(new Date().getTime());
// Waits x milliseconds before performing search.
threshold2 = 1500;
curval2 = "";
t2 = null;
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
        curtime = parseInt(new Date() . getTime());
        next = prevtime + threshold;
        prevtime = curtime;
        if(curtime < next) {
            clearTimeout(t);
            t = setTimeout(function() {
                badgeCount = badgeCount + 1;
                favicon.badge(badgeCount);
                if ($(".save").hasClass("success")) {
                    $(".save").removeClass("success").addClass("warning");

                    // TODO: This line enables auto-save
                    $(".save").click();
                }
            }, threshold);
            return;
        }
    });


    CKEDITOR.instances.editor.on("afterCommandExec", function (event) {
        var command = event.data.name;
        console.log("Command executed: " + command);
        curtime2 = parseInt(new Date() . getTime());
        next2 = prevtime2 + threshold2;
        prevtime2 = curtime2;
        if(curtime2 < next2) {
            clearTimeout(t2);
            t2 = setTimeout(function() {
                badgeCount = badgeCount + 1;
                favicon.badge(badgeCount);
                if ($(".save").hasClass("success")) {
                    $(".save").removeClass("success").addClass("warning");

                    // TODO: This line enables auto-save
                    $(".save").click();
                }
            }, threshold2);
            return;
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
            $(".last-saved").html(moment(json.modified.date).format("LLL"));
            $("title").html("#" + noteId);

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
                resetBadge();
                $(".last-saved").fadeOut("fast");
                $(".save").fadeOut("fast");
                $(".last-saved").html(moment().format("LLL"));
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

function resetBadge() {
    badgeCount = 0;
    favicon.reset();
}

var init = function() {
    "use strict";
    resetBadge();
    switch(getAction()) {
        case "note":
            var noteId = getNoteId();
            var secret = undefined !== getSecret() ? getSecret() : "";
            if (undefined === noteId || noteId === null) {
                window.location = "#/note/home/" + secret;
            }
            loadNote(getNoteId(), applyEditorEvents);
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

    $(document).bind("keydown", "ctrl+s", function() {
        saveNote(CKEDITOR.instances.editor.getData());
    });
});
