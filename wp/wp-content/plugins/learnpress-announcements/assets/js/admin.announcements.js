"use strict";
(function ($) {
    $.fn.lpListAnnouncements = function (opts) {
        var $self = $(this),
            defaults = {},
            options = $.extend(defaults, $self.data(), opts);

        var LPListAnnouncements = {
            $el: $self,
            options: options,
            $btnCreate: $(".lp-add-announcement", $self),
            $btnListCourses: $(".lp-select-courses", $self),
            $popup: $(".lp-courses-popup-window", $self),
            $buttonAdd: $(".lp-add-item", $self).not(".close"),
            $buttonAddClose: $(".lp-add-item.close", $self),
            $buttonClose: $(".close-modal", $self),
            $checkedAll: $(".chk-checkall", $self),
            $listItem: $(".lp-list-items", $self),
            $inputValue: $self.next('.rwmb-text[type="hidden"]'),
            $listCourseSelected: $(".lp-list-course-select", $self),
            $templateCourse: $(".lp-course-item-select.lp-hidden", $self),
            $templateItem: $(".lp_announcement-item.lp-hidden", $self),
            $tbody: $(".list-announcements tbody", $self),

            init: function () {
                var _this = this;

                _this.events();

                _this.sendMail();
            },

            events: function () {
                var _this = this;

                $(window).resize(function () {
                    _this.calculatorPopup();
                });

                /* Create Announcement */
                _this.$btnCreate.click(function (event) {
                    event.preventDefault();

                    _this.createAnnouncement();
                });

                $(".lp-closebtn", _this.$el).click(function () {
                    _this.alertClose();
                });

                _this.$listCourseSelected.on(
                    "click",
                    ".lp-remove-course",
                    function () {
                        $(this).closest(".lp-course-item-select").remove();
                    }
                );

                $(".lp-send-mail, #lp-display-comment", _this.$el)
                    .change(function () {
                        var checked = $(this).prop("checked");

                        if (checked) {
                            $(this).val("on");
                        } else {
                            $(this).val("off");
                        }
                    })
                    .trigger("change");

                /* Toggle Popup */
                _this.$popup.on("toggleActive", function () {
                    _this.$popup.toggleClass("active");

                    if (_this.$popup.hasClass("active")) {
                        _this.open();
                    } else {
                        _this.close();
                    }
                });

                _this.$btnListCourses.click(function () {
                    _this.$popup.trigger("toggleActive");
                });

                $(document).on("keydown", function (event) {
                    if (
                        event.keyCode === 27 &&
                        _this.$popup.hasClass("active")
                    ) {
                        _this.$popup.trigger("toggleActive");
                    }
                });

                _this.$buttonAddClose.click(function (event) {
                    event.preventDefault();

                    _this.$listCourseSelected.trigger("selectedCourses");
                    _this.$popup.trigger("toggleActive");
                });

                _this.$buttonClose.click(function (event) {
                    event.preventDefault();

                    _this.$popup.trigger("toggleActive");
                });

                /* Search Announcement */

                _this.$el
                    .on("search", ".lp-course-search", function () {
                        var search = $(this).val().trim().toUpperCase();

                        $(".lp-course-item", _this.$listItem).each(function () {
                            var text = $(this).data("text").toUpperCase();

                            if (text.indexOf(search) === -1) {
                                $(this).addClass("lp-hidden");
                            } else {
                                $(this).removeClass("lp-hidden");
                            }
                        });
                    })
                    .on("change, keydown", ".lp-course-search", function () {
                        var $this = $(this);

                        setTimeout(function () {
                            $this.trigger("search");
                        }, 100);
                    });

                /* Change Data */

                _this.$buttonAdd.click(function () {
                    event.preventDefault();

                    _this.changeData();
                });

                _this.$listItem.change(function () {
                    var ids = [],
                        titles = [];

                    $("input[type=checkbox]", this).each(function () {
                        if ($(this).prop("checked")) {
                            ids.push($(this).val());
                            titles.push($(this).next(".lp-item-text").text());
                        }
                    });

                    _this.$listItem.data({
                        ids: ids,
                        titles: titles,
                    });

                    if (ids.length) {
                        _this.$buttonAdd
                            .text(
                                _this.$buttonAdd.data("text") +
                                    " (+" +
                                    ids.length +
                                    ")"
                            )
                            .removeAttr("disabled");
                        _this.$buttonAddClose
                            .text(
                                _this.$buttonAddClose.data("text") +
                                    " (+" +
                                    ids.length +
                                    ")"
                            )
                            .removeAttr("disabled");
                    } else {
                        _this.$buttonAdd
                            .text(_this.$buttonAdd.data("text"))
                            .attr("disabled", "disabled");
                        _this.$buttonAddClose
                            .text(_this.$buttonAddClose.data("text"))
                            .attr("disabled", "disabled");
                    }
                });

                _this.$checkedAll.change(function () {
                    var checked = $(this).prop("checked");
                    $("ul.lp-list-items li.lp-course-item")
                        .not(".lp-hidden, .ld_selected")
                        .find('input[type="checkbox"]')
                        .each(function () {
                            $(this).prop("checked", checked);
                        });

                    _this.$listItem.trigger("change");
                });

                /* Render Course Selected */
                _this.$listCourseSelected.on("selectedCourses", function () {
                    var ids = _this.$listItem.data("ids"),
                        titles = _this.$listItem.data("titles");

                    $.each(ids, function (index, item) {
                        var $template = _this.$templateCourse
                                .clone()
                                .removeClass("lp-hidden"),
                            url =
                                ajaxurl.split("admin-ajax.php")[0] +
                                "post.php?post=" +
                                item +
                                "&action=edit";

                        $template.attr({
                            id: "lp_course-item-" + item,
                            "data-id": item,
                        });

                        $("a", $template)
                            .text(titles[index].trim())
                            .attr("href", url);
                        _this.$listCourseSelected.append($template);
                    });
                });

                /* Remove Item */

                _this.$tbody
                    .on("click", ".lp-remove", function (event) {
                        var $this = $(this);

                        event.preventDefault();

                        _this.$el.trigger("selectRemove");

                        $.ajax({
                            url: ajaxurl,
                            type: "POST",
                            data: {
                                action: "lp_remove_announcement",
                                course_id: _this.$el.data("id"),
                                post_id: $this
                                    .closest(".lp_announcement-item")
                                    .data("id"),
                            },
                            complete: function () {
                                $this.closest(".lp_announcement-item").remove();
                            },
                        });
                    })
                    .on(
                        "change",
                        '.item-checkbox input[type="checkbox"]',
                        function () {
                            var checked = $(this).prop("checked"),
                                $item = $(this).closest(
                                    ".lp_announcement-item"
                                );

                            if (checked) {
                                $item.addClass("remove");
                            } else {
                                $item.removeClass("remove");
                            }

                            _this.$el.trigger("selectRemove");
                        }
                    );

                _this.$el
                    .on(
                        "click",
                        ".remove-items-announcements",
                        function (event) {
                            event.preventDefault();

                            $(".remove", _this.$tbody)
                                .find(".lp-remove")
                                .trigger("click");

                            $(this)
                                .text($(this).data("title"))
                                .removeClass("active");

                            $(".lp-check-all-items", _this.$el).prop(
                                "checked",
                                false
                            );
                        }
                    )
                    .on("selectRemove", function () {
                        var count = $(".remove", _this.$tbody).length,
                            $btnRemove = $(
                                ".remove-items-announcements",
                                _this.$el
                            ),
                            text = $btnRemove.data("title");

                        if (count) {
                            $btnRemove
                                .text(text + " (+" + count + ")")
                                .addClass("active");
                        } else {
                            $btnRemove.text(text).removeClass("active");
                        }
                    })
                    .on("update", function () {
                        var ids = [];

                        $(".lp_announcement-item", _this.$tbody)
                            .not(".lp-hidden")
                            .each(function () {
                                if ($(this).attr("data-id")) {
                                    ids.push($(this).attr("data-id"));
                                }
                            });

                        ids = ids.join(",");

                        _this.$inputValue.val(ids);
                    });

                $(".lp-check-all-items", _this.$el).change(function () {
                    var checked = $(this).prop("checked");

                    $(".lp_announcement-item", _this.$tbody)
                        .not(".lp-hidden")
                        .find('.item-checkbox input[type="checkbox"]')
                        .each(function () {
                            $(this).prop("checked", checked);
                            $(this).trigger("change");
                        });
                });
            },

            createAnnouncement: function () {
                var _this = this,
                    $form = $(".lp-form-add-announcement", _this.$el),
                    $title = $(".lp-title", $form),
                    $content = $(".lp-content", $form),
                    title = $title.val(),
                    sendMail = $(".lp-send-mail", $form).prop("checked"),
                    nonce = _this.$btnCreate.data("nonce"),
                    displayComment = $("#lp-display-comment", _this.$el).prop(
                        "checked"
                    ),
                    content = $content.val(),
                    postID = _this.$el.data("id"),
                    postsID = [postID];

                if (_this.$btnCreate.hasClass("lp-ajax-loading")) {
                    return;
                }

                if (title === "" && content === "") {
                    _this.alertOpen();

                    return;
                }

                _this.alertClose();

                _this.$btnCreate.addClass("lp-ajax-loading");
                $form.addClass("lp-overlay");

                $(".lp-course-item-select", _this.$listCourseSelected)
                    .not(".lp-hidden")
                    .each(function () {
                        postsID.push($(this).data("id"));
                    });

                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: "lp_create_announcement",
                        send_mail: sendMail,
                        nonce: nonce,
                        title: title,
                        content: content,
                        display_comment: displayComment,
                        post_id: postID,
                        posts_id: postsID.join(","),
                    },
                    type: "POST",
                    success: function (response) {
                        if (response && response.status === 'success') {
                            try {
                                const title = response.data.title;

                                _this.$listItem.data({
                                    ids: [response.data.id],
                                    titles: [title],
                                    sendEmail: [1],
                                });

                                _this.changeData();
                                _this.renderAnnouncement(response);
                            } catch (e) {
								console.log(e);
							}
                        }
                    },
                    complete: function () {
                        $form.removeClass("lp-overlay");
                        $title.val("");
                        $content.val("");
                        $(".lp-course-item-select", _this.$listCourseSelected)
                            .not(".lp-hidden")
                            .remove();
                        _this.$btnCreate.removeClass("lp-ajax-loading");
                    },
                });
            },

            alertOpen: function () {
                var _this = this;

                $(".lp-alert", _this.$el).removeClass("hidden");
            },

            alertClose: function () {
                var _this = this;

                $(".lp-alert", _this.$el).addClass("hidden");
            },

            sendMail: function () {
                var _this = this;

                _this.$el.on("click", ".lp-send", function (event) {
                    var $self = $(this),
                        course_id = _this.getUrlParameter("post");

                    event.preventDefault();

                    if (
                        $(this).hasClass("lp-ajax-loading") ||
                        $(this).hasClass("lp-active")
                    ) {
                        return;
                    }

                    $self.addClass("lp-ajax-loading");
                    $.ajax({
                        url: ajaxurl,
                        data: {
                            action: "send_mail_announcements",
                            announcement_id: $self
                                .closest(".lp_announcement-item")
                                .attr("data-id"),
                            course_id: course_id,
                        },
                        type: "POST",
                        success: function (response) {
                            if (response !== "Success") {
                                // window.alert(response);
                            } else {
                                // $self.addClass('lp-active');
                            }

                            console.log("Send mail is the success");
                        },
                        error: function () {
                            console.log(
                                "OOP! Something has gone wrong, please try again."
                            );
                        },
                        complete: function () {
                            $self.removeClass("lp-ajax-loading");
                        },
                    });
                });
            },

            getUrlParameter: function (sParam) {
                var sPageURL = decodeURIComponent(
                        window.location.search.substring(1)
                    ),
                    sURLVariables = sPageURL.split("&"),
                    sParameterName;

                for (var i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split("=");

                    if (sParameterName[0] === sParam) {
                        return sParameterName[1] === undefined
                            ? true
                            : sParameterName[1];
                    }
                }
            },

            open: function () {
                var _this = this;

                _this.loadCourse();
                _this.calculatorPopup();
            },

            close: function () {
                var _this = this;

                $(".lp-course-search", _this.$el).val("");
                _this.$listItem.empty();
                _this.$buttonAdd
                    .text(_this.$buttonAdd.data("text"))
                    .attr("disabled", "disabled");
                _this.$buttonAddClose
                    .text(_this.$buttonAddClose.data("text"))
                    .attr("disabled", "disabled");
                _this.$checkedAll
                    .prop("checked", false)
                    .attr("disabled", "disabled");
            },

            changeData: function () {
                var _this = this;

                _this.$inputValue.trigger("change");
                _this.$el.trigger("update");
            },

            renderAnnouncement: function (data) {
                var _this = this,
                    $template = _this.$templateItem
                        .clone()
                        .removeClass("lp-hidden"),
                    url =
                        ajaxurl.split("admin-ajax.php")[0] +
                        "post.php?post=" +
                        data.data.id +
                        "&action=edit";

                $(".section-item-input .lp-item-name", $template).val(
                    data.data.title
                );
                $(".section-item-icon a", $template).attr("href", url);
                $(".lp-edit", $template).attr("href", url);
                $(".lp-date", $template).text(data.data.date);
                $template.attr("data-id", data.data.id);

                _this.$tbody.prepend($template);
            },

            calculatorPopup: function () {
                var _this = this,
                    $box = $(".lp-modal-search-items", _this.$popup);

                $box.css("margin-top", "");
                $("article", _this.$popup).css("max-height", "");

                var heightBox = $box.height(),
                    wh = $(window).height();

                $("article", _this.$popup).css("max-height", wh - 200);

                if (heightBox < wh) {
                    $box.css({
                        marginTop: (wh - heightBox) / 2 + "px",
                    });
                }
            },

            loadCourse: function () {
                var _this = this,
                    postID = _this.$el.data("id"),
                    postsNotIn = [postID],
                    options = {
                        action: "lp_announcements_lists_course",
                        post_id: postID,
                    };

                _this.$listItem.empty().addClass("lp-ajaxload");

                $(".lp-course-item-select", _this.$listCourseSelected)
                    .not(".lp-hidden")
                    .each(function () {
                        var id = $(this).data("id");
                        if (id) {
                            postsNotIn.push(id);
                        }
                    });

                if (postsNotIn.length) {
                    options.post__not_in = postsNotIn.join(",");
                }

                $.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: options,

                    success: function (response) {
                        _this.$listItem.empty().append(response);
                        _this.$checkedAll.removeAttr("disabled");

                        /* Detect announcement is selected */
                        var ids = _this.$inputValue.val();

                        if (ids) {
                            ids = ids.split(",");

                            $.each(ids, function () {
                                var $item = $(
                                    '.lp-announcement-item[data-id="' +
                                        this +
                                        '"]',
                                    _this.$listItem
                                );

                                $item.addClass("ld_selected");

                                $('input[type="checkbox"]', $item).attr(
                                    "disabled",
                                    "disabled"
                                );
                            });
                        }
                    },
                    error: function () {
                        _this.$listItem.append(
                            "<li>OOP! Something has gone wrong, please try again.</li>"
                        );
                    },
                    complete: function () {
                        /* Remove icon ajax loading */
                        _this.$listItem.removeClass("lp-ajaxload");

                        /* Re-calculatorPopup */
                        _this.calculatorPopup();
                    },
                });
            },
        };

        LPListAnnouncements.init();
    };

    $(document).ready(function () {
        $(".lp-wrap-list-announcements").lpListAnnouncements();
    });
})(jQuery);
