//Change Menu
$("#changeMenu select[name='menus']").on("change", function() {
    window.location.href = root_dir + "admin/navigation/" + $(this).val();
});

//Validate Delete Menu
$("#changeMenu input[name='deleteMenu']").click(function() {
    if(!confirm("Are you sure you want to delete this menu?")) {
        event.preventDefault();
    }
});

//Validate Create Menu
$("#createMenu input[type='submit']").click(function() {
    if($("#createMenu input[name='menuName']").val() == "") {
        $("#createMenu #message").text("Menu name is missing");
        
        event.preventDefault();
        return
    }
});

//validate Add Menu
$("#addItem input[type='submit']").click(function() {
    if($("#addItem input[name='itemName']").val() == "") {
        $("#addItem #message").text("Item name is missing");
        
        event.preventDefault();
        return
    }
    
    if($("#addItem input[name='itemSlug']").val() == "") {
        $("#addItem #message").text("Item slug is missing");
        
        event.preventDefault();
        return
    }
});

//Edit navigation
$(".navigationTreeWrap").on("click", "input[name='edit']", function() {
    var id = $(this).closest("li").find("input[name='hId']").first().val();
    var postsHtml = $("#addItem select[name='posts']").html();
    
    $("input[name='edit']").attr("disabled", true);
    $("input[name='saveTree']").attr("disabled", true);
    
    $("body").prepend(
        "<div class='editor formBlock column' data-id='" + id + "' style='width: 50%; max-height: 50%; position: absolute; top: 50%; transform: translateY(-50%); background: #fff; z-index: 9999; margin: auto; left: 0; right: 0; box-shadow: 1px 1px 10px -5px #000; max-width: 90%;'>" +
            "<h2 class='greyHeader'>Edit navigation <span id='editorClose' style='height: 29px; width: 29px; text-align: center; cursor: pointer; float: right; background: #f44236; color: #fff; border-radius: 100%;'>X</span></h2>" +
            "<div style='height: 100%;'>" +
                "<form id='editNavigation'>" +
                    "<p>" + 
                        "<label>Choose Existing Post</label>" +
                        "<select name='posts'>" +
                            postsHtml +
                        "</select>" +
                    "</p>" +
                    "<p>" +
                        "<label>Name</label>" +
                        "<input type='text' name='sName' value='" + $(this).closest("li").find("input[name='hName']").first().val() + "'>" +
                    "</p>" + 
                    "<p>" +
                        "<label>Image</label>" +
                        "<input type='text' name='sImage' value='" + $(this).closest("li").find("input[name='hImage']").first().val() + "' style='width: calc(100% - 340px) !important;'>" +
                        "<input type='button' name='imageSelector' value='Choose File' style='padding: 0.5em; margin-left: 4px;'>" +
                    "</p>" + 
                    "<p>" +
                        "<label>URL Slug</label>" +
                        "<input type='text' name='sSlug' value='" + $(this).closest("li").find("input[name='hSlug']").first().val() + "'>" +
                    "</p>" + 
                "</form>" +
            "</div>" +
        "</div>"
    );
});

//Save Changes on Editor Close
$("body").on("click", "#editorClose", function() {    
    var id = $(".editor").attr("data-id");
    
    if($(".editor input[name='sName']").val() == "") {
        $(".editor input[name='sName']").val($("div#edit" + id + " input[name='hName']").val());
    }
    
    $("div#edit" + id + " input[name='hName']").val($(".editor input[name='sName']").val());
    $("div#edit" + id + " input[name='hSlug']").val($(".editor input[name='sSlug']").val());
    $("div#edit" + id + " input[name='hImage']").val($(".editor input[name='sImage']").val());
    
    $("input[name='edit']").attr("disabled", false);
    $("input[name='saveTree']").attr("disabled", false);
    $(".editor").remove();
});

//Delete Item
$(".navigationTreeWrap").on("click", "input[name='delete']", function() {
    if(confirm("This item and all sub categories will be deleted.")) {
        $(this).closest("li").find("input[name='hDelete']").val(1);
        $(this).closest("li").css("display", "none");
    }
}); 

//Select Image
$("body").on("click", ".editor input[name='imageSelector']", function() {
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        skin: "snapcms",
        relative_urls: false,
        remove_script_host: true,
        oninsert: function(args) {
            var image = args.files[0].url;

            $(".editor input[name='sImage']").val(image);
        }
    });
});

$("body").on("click", "#addItem input[name='imageSelector']", function() {
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        skin: "snapcms",
        relative_urls: false,
        remove_script_host: true,
        oninsert: function(args) {
            var image = args.files[0].url;

            $("#addItem input[name='itemImage']").val(image);
        }
    });
});

//Select Existing Page
$("#addItem select[name='posts']").on("change", function() {
    var url = $(this).children("option:selected").attr("data-url");
    var type = $(this).children("option:selected").attr("data-type");
    var name = $(this).children("option:selected").attr("data-name");
    var fullurl = (type == "pages" ? "" : type + "/") + url;
    
    $("#addItem input[name='itemName']").val(name);
    $("#addItem input[name='itemSlug']").val(fullurl);
});

$("body").on("change", "select[name='posts']", function() {
    var url = $(this).children("option:selected").attr("data-url");
    var type = $(this).children("option:selected").attr("data-type");
    var name = $(this).children("option:selected").attr("data-name");
    var fullurl = (type == "pages" ? "" : type + "/") + url;
    
    $("#editNavigation input[name='sName']").val(name);
    $("#editNavigation input[name='sSlug']").val(fullurl);
});

//Save Tree
$("input[name='saveTree']").click(function() {
    var navTree = [];
    var index = 0;
    var menuId = $("#addItem input[name='menuId']").val();
    
    $(".navigationTree li").each(function() {
        navTree[index] = {
            id: $(this).find("input[name='hId']").first().val(),
            name: $(this).find("input[name='hName']").first().val(),
            image: $(this).find("input[name='hImage']").first().val(),
            slug: $(this).find("input[name='hSlug']").first().val(),
            parent: $(this).find("input[name='hParent']").first().val(),
            delete: $(this).find("input[name='hDelete']").first().val(),
            level: $(this).find("input[name='hLevel']").first().val(),
            position: $(this).find("input[name='hPosition']").first().val()
        };
        
        index++;
    });
    
    //console.log(navTree); return;
    
    $.ajax({
        url: root_dir + "admin/scripts/saveNavigation.php",
        method: "POST",
        dataType: "json",
        data: ({menuId, navTree}),
        success: function(data) {
            if(data[0] == 1) {
                window.location.reload();
            }
            else {
                $(".treemessage").text(data[1]);
            }
        }
    })
});

//Sortable
$(function() {
    $(".navigationTree").sortable({
        connectWith: ".navigationTree",
        stop: function() {
            //Recalculate levels and positions
            //Loop through each ul and get level
            var level = 0;

            $(".navigationTree").each(function() {
                level = $(this).parents("ul")['length'];
                $(this).children("li").find("input[name='hLevel']").first().val(level);

                //Loop through each child li and set position and parent
                var position = 0;
                var parent = $(this).attr("id").split("parent")[1];

                $(this).children("li").each(function() {
                    $(this).find("input[name='hPosition']").first().val(position);
                    $(this).find("input[name='hParent']").first().val(parent);

                    position++;
                });
            });
        }
    });
});