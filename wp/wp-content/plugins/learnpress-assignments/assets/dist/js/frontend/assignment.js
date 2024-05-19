/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**********************************************!*\
  !*** ./assets/src/js/frontend/assignment.js ***!
  \**********************************************/
/**
 * Single Assignment functions
 *
 * @author ThimPress
 * @package
 * @version 4.0.0
 * @author Nhamdv - Code is poetry
 */

(function ($) {
  !Number.prototype.AssignmenttoTime && (Number.prototype.AssignmenttoTime = function (thisSettings) {
    const MINUTE_IN_SECONDS = 60,
      HOUR_IN_SECONDS = 3600,
      DAY_IN_SECONDS = 24 * 3600;
    let seconds = this + 0,
      str = '',
      singularDayText = ' day left',
      pluralDayText = ' days left';
    if (typeof thisSettings.singularDayText != 'undefined') {
      singularDayText = thisSettings.singularDayText;
    }
    if (typeof thisSettings.pluralDayText != 'undefined') {
      pluralDayText = thisSettings.pluralDayText;
    }
    if (seconds > DAY_IN_SECONDS) {
      const days = Math.ceil(seconds / DAY_IN_SECONDS);
      str = days + (days > 1 ? pluralDayText : singularDayText);
    } else {
      let hours = Math.floor(seconds / HOUR_IN_SECONDS),
        minutes = 0;
      seconds = hours ? seconds % (hours * HOUR_IN_SECONDS) : seconds;
      minutes = Math.floor(seconds / MINUTE_IN_SECONDS);
      seconds = minutes ? seconds % (minutes * MINUTE_IN_SECONDS) : seconds;
      if (hours && hours < 10) {
        hours = '0' + hours;
      }
      if (minutes < 10) {
        minutes = '0' + minutes;
      }
      if (seconds < 10) {
        seconds = '0' + seconds;
      }
      str = hours + ':' + minutes + ':' + seconds;
    }
    return str;
  });
  function LPAssignment(settings) {
    const self = this,
      thisSettings = $.extend({}, settings),
      $timeElement = $('.assignment-countdown .progress-number'),
      callbackEvents = new LP.Event_Callback(this);
    let remainingTime = thisSettings.remainingTime,
      timerCountdown = null;
    function timeCountdown() {
      stopCountdown();
      const overtime = thisSettings.remainingTime <= 0,
        isCompleted = -1 !== settings.status.indexOf('finished');
      if (isCompleted) {
        return;
      }
      if (overtime) {
        $('form.save-assignment').off('submit.learn-press-confirm');
        if (settings.uploaded) {
          return;
        }
        callbackEvents.callEvent('finish');
        return;
      }
      thisSettings.remainingTime--;
      timerCountdown = setTimeout(timeCountdown, 1000);
    }
    function stopCountdown() {
      timerCountdown && clearTimeout(timerCountdown);
    }
    function initCountdown() {
      thisSettings.watchChange('remainingTime', function (prop, oldVal, newVal) {
        remainingTime = newVal;
        onTick.apply(self, [oldVal, newVal]);
        return newVal;
      });
    }
    function onTick(oldVal, newVal) {
      callbackEvents.callEvent('tick', [newVal]);
      if (newVal <= 0) {
        stopCountdown();
        callbackEvents.callEvent('finish');
      }
    }
    function showTime() {
      if (remainingTime < 0) {
        remainingTime = 0;
      }
      if (typeof thisSettings != 'undefined') {
        $timeElement.html(remainingTime.AssignmenttoTime(thisSettings));
      }
    }
    function submit() {
      $('form.save-assignment').trigger('submit');
    }
    function init() {
      if (thisSettings.onTick) {
        self.on('tick', thisSettings.onTick);
      }
      if (thisSettings.onFinish) {
        self.on('finish', thisSettings.onFinish);
      }
      initCountdown();
      timeCountdown();
    }
    this.on = callbackEvents.on;
    this.off = callbackEvents.off;
    if (thisSettings.totalTime > 0) {
      this.on('tick.showTime', showTime);
      this.on('finish.submit', submit);
    }
    this.getRemainingTime = function () {
      return remainingTime;
    };
    init();
  }
  $(document).ready(function () {
    if (typeof lpAssignmentSettings !== 'undefined' && $('.assignment-countdown').length > 0) {
      window.lpAssignment = new LPAssignment(lpAssignmentSettings);
    }
    if ($('.save-assignment').length > 0) {
      let which;
      $('button').on('click', function () {
        which = $(this).attr('id');
      });
      $('.save-assignment').on('submit', function (e) {
        let file = document.getElementById("_lp_upload_file");
        if (file) {
          let file_amount = ~~$('#assignment-file-amount-allow').text();
          let max_file_size = ~~$('#assignment-file-amount-allow').attr('max-file-size');
          if (file.files.length > file_amount) {
            alert('Maximum amount of files you can upload more: ' + file_amount);
            return false;
          } else {
            let over = false;
            for (let i = 0; i < file.files.length; i++) {
              if (file.files[i].size > max_file_size * 1024 * 1024) over = true;
            }
            if (over) {
              alert('File size must be less than ' + max_file_size + ' MB');
              return false;
            }
          }
        }
        if (which == 'assignment-button-right') {
          const question = $('#assignment-button-right').data('confirm');
          const ok = confirm(question);
          if (ok) {
            return true;
          }
          e.preventDefault();
          return false;
        } else if (which == 'assignment-button-left') {
          const question = $('#assignment-button-left').data('confirm');
          const ok = confirm(question);
          if (ok) {
            return true;
          }
          e.preventDefault();
          return false;
        }
        $(this).append('<input type="hidden" name="controls-button" value="Send" /> ');
        return true;
      });
    }
    $('.assignment_action_icon').on('click', function () {
      const attOrder = $(this).attr('order');
      const question = $(this).data('confirm');
      $('.lp-assignment-buttons .learn-press-message').remove();
      const ok = confirm(question);
      const assignmentID = $(this).closest('form.save-assignment').find('input[name="assignment-id"]').val();
      if (ok) {
        const allowAmount = $('#assignment-file-amount-allow').text();
        $.ajax({
          type: 'post',
          url: lpAssignmentSettings.root + 'learnpress/v1/assignments/delete-submit-file',
          beforeSend(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', lpAssignmentSettings.nonce);
            $('.assignmen-file').append('<div class="lp-assignment__overlay"></div>');
          },
          data: {
            id: assignmentID,
            fileId: attOrder
          },
          success(res) {
            if (res.data.status == 200) {
              $('.lp-assignment__overlay').remove();
              const newAllowAmount = parseInt(allowAmount) + 1;
              $('#assignment-uploaded-file-' + attOrder).hide();
              $('#_lp_upload_file').prop('disabled', false);
              $('#assignment-file-amount-allow').text(newAllowAmount);
              $('.learn-press-assignment-uploaded').before('<div class="learn-press-message success" style="float:left; width:100%; margin-top:10px "> ' + res.message + '</div>').fadeIn(slow);
            } else {
              $('.lp-assignment__overlay').remove();
              $('.learn-press-assignment-uploaded').before('<div class="learn-press-message error" style="float:left; width:100%; margin-top:10px "> ' + res.message + '</div>');
            }
          }
        });
      } else {
        return false;
      }
    });
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=assignment.js.map