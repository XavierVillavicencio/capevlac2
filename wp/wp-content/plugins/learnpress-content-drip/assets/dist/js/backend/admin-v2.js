/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*******************************************!*\
  !*** ./assets/src/js/backend/admin-v2.js ***!
  \*******************************************/
/**
 * It updates the drip settings for a course
 * @param childElement - The child element of the table.
 * @param courseID - The ID of the course.
 */
const updateSettings = (childElement, courseID) => {
  const submit = async (dataSettings, btn) => {
    const ele = document.getElementById('content-drip-settings-message');
    try {
      const response = await wp.apiFetch({
        path: 'lp/content-drip/v1/update-settings',
        method: 'POST',
        data: {
          courseID: courseID,
          dataSettings: dataSettings
        }
      });
      btn.classList.remove('loading');
      const {
        status,
        message
      } = response;
      if (status === 'error') {
        throw new Error(message || 'Error');
      }

      // window.location.reload(true);
    } catch (error) {
      ele.insertAdjacentHTML('beforeend', `<div class="lp-ajax-message error" style="display:block">${error.message || 'Error: Query lp/content-drip/v1/update-settings'}</div>`);
    }
  };
  const listButtonUpdate = document.querySelectorAll('.learn-press-update-drip-items');
  if (listButtonUpdate.length === 0) return;
  listButtonUpdate.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      btn.classList.add('loading');
      let dataSettings = [];

      //get settings
      childElement.forEach(function (ele) {
        let detailSettings = {};

        //get id
        const elemTitle = ele.querySelector('td.title-item-content-drip');
        const id = elemTitle.dataset.id;
        const elePrerequisite = ele.querySelector('select.drip-prerequisite-items');
        const eleDelayType = ele.querySelector('select.delay-type');

        //get prerequisite
        if (elePrerequisite !== null) {
          const options = elePrerequisite.options;
          const valueElePrerequisite = [];
          for (var i = 0, iLen = elePrerequisite.options.length; i < iLen; i++) {
            opt = options[i];
            if (opt.selected) {
              valueElePrerequisite.push(opt.value);
            }
          }
          detailSettings.prerequisite = valueElePrerequisite;
        } else {
          detailSettings.prerequisite = [];
        }

        //get type
        if (eleDelayType !== null) {
          detailSettings.type = eleDelayType.value;
        }

        //type == specific
        const eleDelayDatePicker = ele.querySelector('input.delay-specific-datetimepicker');
        if (eleDelayDatePicker !== null) {
          detailSettings.date = eleDelayDatePicker.value;
        }
        //end specific

        //type = interval
        const eleDelayTime = ele.querySelector('input.delay-interval-0');
        if (eleDelayTime !== null) {
          detailSettings.interval = eleDelayTime.value;
        }
        const eleDelayTimeType = ele.querySelector('select.delay-interval-1');
        if (eleDelayTimeType !== null) {
          detailSettings.interval_type = eleDelayTimeType.value;
        }

        //end interval
        dataSettings.push({
          id: id,
          settings: detailSettings
        });
      });
      submit(dataSettings, btn);
    });
  });
};

/**
 * It adds an event listener to a button that, when clicked, sends an AJAX request to the server to
 * reset the drip settings for a course
 * @param childElement - The child element of the parent element.
 * @param courseID - The ID of the course.
 */
const resetSetings = (childElement, courseID) => {
  const submit = async (dataReset, btn) => {
    const ele = document.getElementById('content-drip-settings-message');
    try {
      const response = await wp.apiFetch({
        path: 'lp/content-drip/v1/reset-settings',
        method: 'POST',
        data: {
          courseID: courseID,
          dataReset: dataReset
        }
      });
      btn.classList.remove('loading');
      const {
        status,
        message
      } = response;
      if (status === 'error') {
        throw new Error(message || 'Error');
      }
      window.location.reload(true);
    } catch (error) {
      ele.insertAdjacentHTML('beforeend', `<div class="lp-ajax-message error" style="display:block">${error.message || 'Error: Query lp/content-drip/v1/reset-settings'}</div>`);
    }
  };
  const listButtonReset = document.querySelectorAll('.learn-press-reset-drip-items');
  if (listButtonReset.length === 0) return;
  listButtonReset.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      btn.classList.add('loading');
      const dataReset = [];
      childElement.forEach(function (ele) {
        //get id
        const elemTitle = ele.querySelector('td.title-item-content-drip');
        const id = elemTitle.dataset.id;
        dataReset.push(id);
      });
      submit(dataReset, btn);
    });
    ``;
  });
};
const saveCourse = () => {
  //gutenberg publish course
  const inputEnable = document.querySelector('#lp_contentdrip_course_data input#_lp_content_drip_enable');
  if (inputEnable !== null) {
    let timeOut;
    inputEnable.addEventListener('change', function (e) {
      if (e.target.checked) {
        document.addEventListener('click', function (btn) {
          if (btn.target.classList.contains('editor-post-publish-button')) {
            timeOut = setInterval(function () {
              if (document.querySelector('div.edit-post-meta-boxes-area.is-normal.is-loading') !== null) {
                let timoutMetabox = setInterval(function () {
                  if (!document.querySelector('div.edit-post-meta-boxes-area').classList.contains('is-loading')) {
                    clearInterval(timoutMetabox);
                    clearInterval(timeOut);
                    window.location.reload(true);
                  }
                }, 1000);
              }
            }, 1000);
          }
        });
      }
    });
  }
  const btn = document.querySelector('#lp_contentdrip_course_data a.save-post-ctd');
  if (btn === null) return;
  btn.addEventListener('click', function (e) {
    e.preventDefault();
    let btnSubmit;
    if (document.querySelector('#publishing-action input#publish') !== null) {
      btnSubmit = document.querySelector('#publishing-action input#publish');
    } else {
      btnSubmit = document.querySelector('button.editor-post-publish-button__button');
    }
    if (btnSubmit !== null) {
      btnSubmit.click();
    }
  });
};
document.addEventListener('DOMContentLoaded', function () {
  saveCourse();
  const tableElem = document.querySelector('table.learnpress_page_content-drip-items');
  if (tableElem === null) return;
  const childElement = tableElem.querySelectorAll('tbody#the-list tr');
  if (childElement.length === 0) return;
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const courseID = urlParams.get('course-id');
  updateSettings(childElement, courseID);
  resetSetings(childElement, courseID);
});
/******/ })()
;
//# sourceMappingURL=admin-v2.js.map