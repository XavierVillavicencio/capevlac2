'use strict';

(function ($) {

    $(document).ready(function () {

        /* Find comment active */
        var hash = window.location.hash,
            $anchor = $(hash),
            index = 0;

        if ($anchor.length) {
            index = $anchor.closest('.lp-announcement-item').index();
        }

        $('#lp-announcements').accordion({
            header: '.title',
            heightStyle: 'content',
            icons: {
                collapsible: true,
                header: 'dashicons dashicons-plus',
                activeHeader: 'dashicons dashicons-minus'
            },
            collapsible: true,
            active: index
        });

        $('.announcement_item').click(function () {
            $('#cancel-comment-reply-link').click();
            var element_id = $(this).attr('id'),
                announcement_id = element_id.replace('announcement_item_', ''),
                hidden_announcement_field = $('#lp_comment_from_announcement_' + announcement_id),
                comment_post_ID_input = hidden_announcement_field.parent('form').find('input[name="comment_post_ID"]');
            if (comment_post_ID_input.val() !== announcement_id) {
                comment_post_ID_input.val(announcement_id);
            }
        });
    });

})(jQuery);