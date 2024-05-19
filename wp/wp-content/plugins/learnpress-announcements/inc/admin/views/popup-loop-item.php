<?php
/**
 * Admin popup loop item course view.
 *
 * @since 3.0.0
 */
?>

<li id="course-<?php the_ID(); ?>" class="lp-course-item" data-text="<?php echo strtolower(str_replace(' ','-', get_the_title())); ?>"
    data-type="lp_course" data-id="<?php the_ID(); ?>">
    <label>
        <input type="checkbox" value="<?php the_ID(); ?>">
        <span class="lp-item-text">
            <?php echo get_the_title() ? get_the_title() : __( 'No Title', 'learnpress-announcements' ) ?>
        </span>
    </label>
</li>
