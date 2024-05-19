/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*************************************************!*\
  !*** ./assets/src/js/admin/admin-assignment.js ***!
  \*************************************************/
/**
 * Admin Assignment JS
 *
 * @version 4.0.0
 * @author Nhamdv - Code is poetry
 */
(function ($) {
  function _ready() {
    $('input[type=radio][name=_lp_course_result]').on('change', function (e) {
      if (e.target.value !== 'evaluate_final_assignment') {
        $('.lp-metabox-evaluate-assignment').remove();
      }
    });
    $('.lp-metabox-evaluate-radio').on('click', function (e) {
      e.preventDefault();
      const _this = $(this),
        courseId = $(this).data('id'),
        text = _this.text();
      $('.lp-metabox-evaluate-assignment').remove();
      _this.text(_this.data('loading'));
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'lp_assignment_update_evalute_assignments',
          course_id: courseId
        }
      }).done(function (response) {
        _this.closest('label').append(response.message);
        _this.text(text);
      });
    });
    if ($('.lp-meta-box--assignments').length > 0) {
      $('#_lp_mark').on('change', function () {
        $('._lp_mark_field').find('.learn-press-tip-floating').remove();
        const maxMark = parseFloat($('#_lp_mark').val());
        const passGrade = parseFloat($('#_lp_passing_grade').val());
        if (maxMark < passGrade) {
          $('._lp_mark_field').append('<div class="learn-press-tip-floating">' + lpAssignment.invalid_mark + '</div>');
          $('#_lp_mark').val(passGrade);
        }
      });
      $('#_lp_passing_grade').on('change keydown paste input', function () {
        $('._lp_passing_grade_field').find('.learn-press-tip-floating').remove();
        const maxMark = parseFloat($('#_lp_mark').val());
        const passGrade = parseFloat($('#_lp_passing_grade').val());
        if (passGrade > maxMark) {
          $('._lp_passing_grade_field').append('<div class="learn-press-tip-floating">' + lpAssignment.invalid_mark_grade + '</div>');
          $('#_lp_passing_grade').val(maxMark);
        }
      });
    }
    $('.admin_page_assignment-student .wp-list-table td.column-actions .send-mail').on('click', function (e) {
      e.preventDefault();
      const _self = $(this),
        _actions = _self.parent('.assignment-students-actions'),
        _userId = _actions.data('user_id'),
        _assignmentId = _actions.data('assignment_id');
      if (confirm(lpAssignment.resend_evaluated_mail)) {
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'lp_assignment_send_evaluated_mail',
            user_id: _userId,
            assignment_id: _assignmentId
          }
        }).done(function (response) {
          const _data = LP.parseJSON(response);
          alert(_data.message);
        });
      }
    });
    $('.admin_page_assignment-student .wp-list-table td.column-actions .delete').on('click', function (e) {
      e.preventDefault();
      const _self = $(this),
        _actions = _self.parent('.assignment-students-actions'),
        _userId = _actions.data('user_id'),
        _assignmentId = _actions.data('assignment_id');
      if (confirm(lpAssignment.delete_submission)) {
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'lp_assignment_delete_submission',
            user_id: _userId,
            assignment_id: _assignmentId
          }
        }).done(function (response) {
          const _data = LP.parseJSON(response);
          if (_data.status === 'fail') {
            alert(_data.message);
          }
          location.reload();
        });
      }
    });
    $('.admin_page_assignment-student .wp-list-table td.column-actions .reset').on('click', function (e) {
      e.preventDefault();
      const _self = $(this),
        _actions = _self.parent('.assignment-students-actions'),
        _userItemId = _actions.data('user-item-id');
      if (confirm(lpAssignment.reset_result)) {
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'lp_assignment_reset_result',
            user_item_id: _userItemId
          }
        }).complete(function (response) {
          const _data = LP.parseJSON(response);
          if (_data.status === 'fail') {
            alert(_data.message);
          }
          location.reload();
        });
      }
    });
  }
  $(document).ready(_ready);
})(jQuery);
/******/ })()
;
//# sourceMappingURL=admin-assignment.js.map