    $(".container.mainBody img").scrollview();

    $.imageBrowser = function (options) {

        // scroll master default settings
        var defaults = {
            viewingPage: "#view",
            connectorPage: "backend/router.php",
            mapPage: "backend/router.php",
            previewAllPage: "backend/router.php",
            currentHash: "",
            currentHashPieces: null,
            keyCodes: {nextKeyCode: 39, previousKeyCode: 37}
        };
        var settings = $.extend({}, defaults, options);
        var init = function () {

            settings.currentHash = window.location.hash;
            settings.currentHashPieces = settings.currentHash.split("|");


            loadChapterMap();


            $('body').on('click', '.nextTrigger,.prevTrigger,.imageBrowserTrigger', function () {

                $('.triggerWrap').removeClass('hidden');

                $(this).hide();
                loadPageImage($(this).attr("href"));

                $(this).show();

            });

            if (settings.currentHashPieces[0] === "#view") {
                loadPageImage(settings.currentHash);
            } else {
                getPreviewContent();
            }

            onKeyPressListener();
        };

        var getPreviewContent = function () {

            $.get(settings.previewAllPage, "action=previewAll", function (theResponse) {

                var previewContentFinal = "";

                theResponse = $.parseJSON(theResponse);

                $.each(theResponse, function (key, val) {

                    var previewContent = "<section class='contentPreview'>";
                    var imageFile = key.replace(/ /g, '%20') + "/" + val.settings.cover;

                    previewContent += "<div class='contentPreviewImageWrap'><img src=images/" + (imageFile) + " /></div>";
                    previewContent += "<div class='contentPreviewContent'><h3>" + key + "</h3>";

                    $.each(val['chapters'], function (subKey, subVal) {

                        var subLink = "#view|" + key + "|" + subVal + "|0";
                        previewContent += "<p><a class='imageBrowserTrigger' href='" + subLink + "'>" + subVal + "</a></p>";
                    });

                    previewContentFinal += previewContent + "</div></section>";

                });
                $('.chapterPreviewContent').html(previewContentFinal);
            });


        };

        var isViewingPage = function () {

            if (settings.currentHash !== "" && settings.currentHashPieces[0]) {
                return settings.currentHashPieces[0] === settings.viewingPage;
            }

            return false;
        };

        var loadChapterMap = function () {

            $.get(settings.mapPage, "action=read", function (theResponse) {
                $.each(theResponse, function (key, val) {
                    $(".BrowseChapters .dropdown-menu-chapters").append("<li><a class='imageBrowserTrigger' href='" + val + "'>" + key + "</a></li>");
                });
            });

        };

        var loadPageImage = function (pagesHash) {

            if (!isViewingPage) {
                return false;
            }

            // clear body of page
            var fields = pagesHash;
            var fieldPieces = fields.split("|");

            // this might be unecessary
            window.location.hash = pagesHash;
            if (fieldPieces.length === 4) {

                $('.triggerWrap').removeClass('hidden');
                $(".missingImageWrap").hide();

                var fieldsClean = "action=" + (fieldPieces[0].replace("#", "")) + "&folder=" + fieldPieces[1] + "&sub_folder=" + fieldPieces[2] + "&index=" + fieldPieces[3];
                $.get(settings.connectorPage, fieldsClean, function (theResponse) {

                    theResponse = theResponse[0];
                    var finalImage = "images/" + fieldPieces[1] + "/" + fieldPieces[2] + "/" + theResponse.currentImage;
                    var percentComplete = Math.round(((parseInt(fieldPieces[3]) + 1) / parseInt(theResponse.chapterCount)) * 100);

                    $(".completeBar").css({width: percentComplete + "%"});
                    $(".prevTrigger").attr("href", theResponse.previousHash);
                    $(".nextTrigger").attr("href", theResponse.nextHash);

                    $(".mainBody .row").fadeOut(250, function () {
                        window.scrollTo(0, 0);
                        $(".mainBody .row").html("<div class='row'><img src='" + finalImage + "' /></div>")
                                .attr("href", theResponse.nextHash).fadeIn();
                    });
                });
            } else {
                $(".missingImageWrap").show();
            }

        };


        /**
         * Event type method which listens
         * for when the user presses buttons
         * @returns {undefined}
         */
        var onKeyPressListener = function () {

            document.onkeydown = function (evt) {
                evt = evt || window.event;

                // right key code
                if (evt.keyCode === settings.keyCodes.nextKeyCode) {
                    $('.nextTrigger').trigger('click');
                }
                if (evt.keyCode === settings.keyCodes.previousKeyCode) {
                    $('.prevTrigger').trigger('click');
                }
            };

        };

        init();


    };
    $(function () {
        $.imageBrowser();

        $('.toggleNavButton').on('click', function () {
            $('.navbar.navbar-default').fadeToggle(0);
            $(".toggleNavButton").toggleClass("menuHidden");
        });
    });
