$(function () {

    /*
     * --------------------------
     * Set up all our required plugins
     * --------------------------
     */

    /* Datepicker */
    $(document).ajaxComplete(function () {
        $('#DatePicker').remove();
        var $div = $("<div>", {id: "DatePicker"});
        $("body").append($div);
        $div.DateTimePicker({
            dateTimeFormat: Attendize.DateTimeFormat,
            dateSeparator: Attendize.DateSeparator
        });

    });

    /* Responsive sidebar */
    $(document.body).on('click', '.toggleSidebar', function (e) {
        $('html').toggleClass('sidebar-open-ltr');
        e.preventDefault();
    });

    /* Scroll to top */
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.totop').fadeIn();
        } else {
            $('.totop').fadeOut();
        }
    });

    $(".totop").click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 200);
    });


    /*
     * --------------------
     * Ajaxify those forms
     * --------------------
     *
     * All forms with the 'ajax' class will automatically handle showing errors etc.
     *
     */
    $('form.ajax').ajaxForm({
        delegation: true,
        beforeSubmit: function (formData, jqForm, options) {

            $(jqForm[0])
                .find('.error.help-block')
                .remove();
            $(jqForm[0]).find('.has-error')
                .removeClass('has-error');

            var $submitButton = $(jqForm[0]).find('input[type=submit]');
            toggleSubmitDisabled($submitButton);


        },
        uploadProgress: function (event, position, total, percentComplete) {
            $('.uploadProgress').show().html('Uploading Images - ' + percentComplete + '% Complete...    ');
        },
        error: function (data, statusText, xhr, $form) {

            // Form validation error.
            if (422 == data.status) {
                processFormErrors($form, $.parseJSON(data.responseText));
                return;
            }

            showMessage('Whoops!, it looks like the server returned an error.');

            var $submitButton = $form.find('input[type=submit]');
            toggleSubmitDisabled($submitButton);

            $('.uploadProgress').hide();
        },
        success: function (data, statusText, xhr, $form) {

            switch (data.status) {
                case 'success':

                    if ($form.hasClass('reset')) {
                        $form.resetForm();
                    }

                    if ($form.hasClass('closeModalAfter')) {
                        $('.modal, .modal-backdrop').fadeOut().remove();
                    }

                    var $submitButton = $form.find('input[type=submit]');
                    toggleSubmitDisabled($submitButton);

                    if (typeof data.message !== 'undefined') {
                        showMessage(data.message);
                    }

                    if (typeof data.runThis !== 'undefined') {
                        eval(data.runThis);
                    }

                    if (typeof data.redirectUrl !== 'undefined') {
                        window.location.href = data.redirectUrl;
                    }

                    break;

                case 'error':
                    processFormErrors($form, data.messages);
                    break;

                default:
                    break;
            }

            $('.uploadProgress').hide();
        },
        dataType: 'json'
    });


    /*
     * --------------------
     * Create a simple way to show remote dynamic modals from the frontend
     * --------------------
     *
     * E.g :
     * <a href='/route/to/modal' class='loadModal'>
     *  Click For Modal
     * </a>
     *
     */
    $(document.body).on('click', '.loadModal, [data-invoke~=modal]', function (e) {

        var loadUrl = $(this).data('href'),
            cacheResult = $(this).data('cache') === 'on',
            $button = $(this);

        $('.modal').remove();
        $('.modal-backdrop').remove();
        $('html').addClass('working');

        $.ajax({
            url: loadUrl,
            data: {},
            localCache: cacheResult,
            dataType: 'html',
            success: function (data) {
                hideMessage();

                $('body').append(data);

                var $modal = $('.modal');

                $modal.modal({
                    'backdrop': 'static'
                });

                $modal.modal('show');

                $modal.on('hidden.bs.modal', function (e) {
                    // window
                    location.hash = '';
                });

                $('html').removeClass('working');

                $('select[name=assigned_role]').on('change', function (e) {
                    var select = $(e.currentTarget);
                    var data = {
                        "assigned_role": select.val(),
                        "user_id": select.data('user')
                    };

                    $.post(select.data('update-url'), data)
                        .done(function (data) {
                            if (typeof data.message !== 'undefined') {
                                showMessage(data.message);
                            }
                        }).fail(function (data) {
                        showMessage(Attendize.GenericErrorMessages);
                    });
                });

                $('input[name=can_manage_events]').on('click', function (e) {
                    var canManageEvents = $(e.currentTarget);
                    var isChecked = canManageEvents.is(':checked');

                    var data = {
                        "checked": isChecked,
                        "user_id": canManageEvents.data('user')
                    };

                    $.post(canManageEvents.data('update-url'), data)
                        .done(function (data) {
                            if (typeof data.message !== 'undefined') {
                                showMessage(data.message);
                            }
                        }).fail(function (data) {
                        showMessage(Attendize.GenericErrorMessages);
                    });
                });

                function findDropdownBtns(parent) {
                    if (!parent) parent = document;
                    return Array.from(
                        parent.querySelectorAll("[data-id=dropdown]")
                    );
                }

                function dropdownBtnsRemoveEventListeners(el) {
                    el.removeEventListener(
                        "click",
                        (event) =>
                            handleUserActionBtnClick(
                                event,
                                dropdownBtnsRemoveEventListeners.bind(
                                    undefined,
                                    el
                                )
                            ),
                        false
                    );
                }

                findDropdownBtns(document).forEach((el) => {
                    el.addEventListener("click", toggleDropdown, false);
                    findUserActionBtns(el.parentElement).forEach(function (el) {
                        el.addEventListener(
                            "click",
                            (event) =>
                                handleUserActionBtnClick(
                                    event,
                                    dropdownBtnsRemoveEventListeners.bind(
                                        undefined,
                                        el
                                    )
                                ),
                            false
                        );
                    });
                });

                function toggleDropdown(event) {
                    var target = event.target;
                    var dropdownContent =
                        target.parentElement.querySelector(".dropdown-content");
                    var isVisible =
                        dropdownContent.hasAttribute("hidden") || false;
                    if (isVisible) {
                        dropdownContent.removeAttribute("hidden");
                    } else {
                        dropdownContent.setAttribute("hidden", true);
                    }
                }

                function findUserActionBtns(parent) {
                    if (!parent) parent = document;
                    return Array.from(
                        parent.querySelectorAll("button[name=user_action]")
                    );
                }

                function findUserActionBtnByAction(parent, action) {
                    return parent.querySelector(
                        "button[data-action=" + action + "]"
                    );
                }

                function handleUserActionBtnClick(
                    event,
                    onRemoveCb = function () {}
                ) {
                    var target = event.target;
                    console.log("click", event, target.nodeName);

                    if (target.nodeName !== "BUTTON") return;

                    var action = target.dataset.action;
                    var href = target.dataset.href;

                    var onSuccess = function () {};

                    var request;
                    switch (action) {
                        case "send_invitation_message":
                            request = new Request(href, {
                                method: "GET",
                            });
                            break;

                        case "delete":
                            request = new Request(href, {
                                method: "DELETE",
                            });
                            onSuccess = function () {
                                target.setAttribute("hidden", true);
                                var parent = target.parentElement;
                                findUserActionBtnByAction(
                                    parent,
                                    "send_invitation_message"
                                ).setAttribute("hidden", true);
                                findUserActionBtnByAction(
                                    parent,
                                    "restore"
                                ).removeAttribute("hidden");
                                findUserActionBtnByAction(
                                    parent,
                                    "force_delete"
                                ).removeAttribute("hidden");
                            };
                            break;

                        case "force_delete":
                            request = new Request(href, {
                                method: "DELETE",
                            });
                            onSuccess = function () {
                                var buttonWrapper = target.parentElement;
                                userActionBtnsRemoveEventListeners(
                                    buttonWrapper
                                );
                                onRemoveCb();
                                var dropdownContent =
                                    buttonWrapper.parentElement;
                                var dropdownWrapper =
                                    dropdownContent.parentElement;
                                var td = dropdownWrapper.parentElement;
                                var tr = td.parentElement;
                                tr.parentElement.removeChild(tr);
                            };
                            break;

                        case "restore":
                            request = new Request(href);
                            onSuccess = function () {
                                target.setAttribute("hidden", true);
                                var parent = target.parentElement;
                                findUserActionBtnByAction(
                                    parent,
                                    "send_invitation_message"
                                ).removeAttribute("hidden", true);
                                findUserActionBtnByAction(
                                    parent,
                                    "force_delete"
                                ).setAttribute("hidden", true);
                                findUserActionBtnByAction(
                                    parent,
                                    "delete"
                                ).removeAttribute("hidden");
                            };
                            break;
                    }

                    request.headers.set(
                        "X-CSRF-TOKEN",
                        document
                            .querySelector('meta[name="_token"]')
                            .getAttribute("content")
                    );

                    fetch(request)
                        .then(function (response) {
                            if (response.ok) return response;
                            else {
                                throw {
                                    message: "response is not ok",
                                    response,
                                };
                            }
                        })
                        .then(function (res) {
                            return res.json();
                        })
                        .then(function (data) {
                            if (typeof data.message !== "undefined") {
                                showMessage(data.message);
                            }

                            onSuccess();
                        })
                        .catch(function (err) {
                            console.error(err);

                            err.response
                                .json()
                                .then(function (data) {
                                    if (data.message) {
                                        showMessage(data.message);
                                    } else {
                                        throw "missing message";
                                    }
                                })
                                .catch(function () {
                                    showMessage(Attendize.GenericErrorMessages);
                                });
                        });
                }

                function userActionBtnsRemoveEventListeners(parent) {
                    findUserActionBtns(parent).forEach(function (el) {
                        el.removeEventListener(
                            "click",
                            handleUserActionBtnClick,
                            false
                        );
                    });
                }

            }
        }).done().fail(function (data) {
            $('html').removeClass('working');
            showMessage(lang("whoops_and_error", {"code": data.status, "error": data.statusText}));
        });

        e.preventDefault();
    });

    /*
     * ------------------------------------------------------------
     * A slightly hackish way to close modals on back button press.
     * ------------------------------------------------------------
     */
    $(window).on('hashchange', function (e) {
        $('.modal').modal('hide');
    });


    /*
     * -------------------------------------------------------------
     * Simple way for any type of object to be deleted.
     * -------------------------------------------------------------
     *
     * E.g markup:
     * <a data-route='/route/to/delete' data-id='123' data-type='objectType'>
     *  Delete This Object
     * </a>
     *
     */
    $('.deleteThis').on('click', function (e) {

        /*
         * Confirm if the user wants to delete this object
         */
        if ($(this).data('confirm-delete') !== 'yes') {
            $(this).data('original-text', $(this).html()).html('Click To Confirm?').data('confirm-delete', 'yes');

            var that = $(this);
            setTimeout(function () {
                that.data('confirm-delete', 'no').html(that.data('original-text'));
            }, 2000);

            return;
        }

        var deleteId = $(this).data('id'),
            deleteType = $(this).data('type'),
            route = $(this).data('route');

        $.post(route, deleteType + '_id=' + deleteId)
            .done(function (data) {

                if (typeof data.message !== 'undefined') {
                    showMessage(data.message);
                }

                if (typeof data.redirectUrl !== 'undefined') {
                    window.location.href = data.redirectUrl;
                }

                switch (data.status) {
                    case 'success':
                        $('#' + deleteType + '_' + deleteId).fadeOut();
                        break;
                    case 'error':
                        /* Error */
                        break;

                    default:
                        break;
                }
            }).fail(function (data) {
            showMessage(Attendize.GenericErrorMessages);
        });

        e.preventDefault();
    });


    $(document.body).on('click', '.pauseTicketSales', function (e) {

        var ticketId = $(this).data('id'),
            route = $(this).data('route');

        $.post(route, 'ticket_id=' + ticketId)
            .done(function (data) {

                if (typeof data.message !== 'undefined') {
                    showMessage(data.message);
                }

                switch (data.status) {
                    case 'success':
                        setTimeout(function () {
                            document.location.reload();
                        }, 300);
                        break;
                    case 'error':
                        /* Error */
                        break;

                    default:
                        break;
                }
            }).fail(function (data) {
            showMessage(Attendize.GenericErrorMessages);
        });


        e.preventDefault();
    });

    /**
     * Toggle checkboxes
     */
    $(document.body).on('click', '.check-all', function (e) {
        var toggleClass = $(this).data('check-class');
        $('.' + toggleClass).each(function () {
            this.checked = $(this).checked;
        });
    });

    /*
     * ------------------------------------------------------------
     * Toggle hidden content when a.show-more-content is clicked
     * ------------------------------------------------------------
     */
    $(document.body).on('click', '.show-more-options', function (e) {

        var toggleClass = !$(this).data('toggle-class')
            ? '.more-options'
            : $(this).data('toggle-class');


        if ($(this).hasClass('toggled')) {
            $(this).html($(this)
                .data('original-text'));

        } else {

            if (!$(this).data('original-text')) {
                $(this).data('original-text', $(this).html());
            }
            $(this).html(!$(this).data('show-less-text') ? 'Show Less' : $(this).data('show-less-text'));
        }

        $(this).toggleClass('toggled');

        /*
         * ?
         */
        if ($(this).data('clear-field')) {
            $($(this).data('clear-field')).val('');
        }

        $(toggleClass).slideToggle();
        e.preventDefault();
    });


    /*
     * Sort by trigger
     */
    $('select[name=sort_by_select]').on('change', function () {
        $('input[name=sort_by]').val($(this).val()).closest('form').submit();
    });

    /**
     * Custom file inputs
     */
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

        input.trigger('fileselect', [
            numFiles,
            label
        ]);
    });

    $(document.body).on('fileselect', '.btn-file :file', function (event, numFiles, label) {
        var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;
        if (input.length) {
            input.val(log);
        } else {
            if (log) {
                console.log(log);
            }

        }
    });

    /**
     * Scale the preview iFrames when changing the design of organiser/event pages.
     */
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

        var target = $(e.target).attr("href");

        if ($(target).hasClass('scale_iframe')) {

            var $iframe = $('iframe', target);
            var iframeWidth = $('.iframe_wrap').innerWidth();
            var iframeHeight = $('.iframe_wrap').height();

            $iframe.css({
                width: 1200,
                height: 1400
            });

            var iframeScale = (iframeWidth / 1200);
            $iframe.css({
                '-webkit-transform': 'scale(' + iframeScale + ')',
                '-ms-transform': 'scale(' + iframeScale + ')',
                'transform': 'scale(' + iframeScale + ')',
                '-webkit-transform-origin': '0 0',
                '-ms-transform-origin': '0 0',
                'transform-origin': '0 0',
            });
        }
    });

    $(document.body).on('click', '.markPaymentReceived', function (e) {

        var orderId = $(this).data('id'),
            route = $(this).data('route');

        $.post(route, 'order_id=' + orderId)
            .done(function (data) {

                if (typeof data.message !== 'undefined') {
                    showMessage(data.message);
                }

                switch (data.status) {
                    case 'success':
                        setTimeout(function () {
                            document.location.reload();
                        }, 300);
                        break;
                    case 'error':
                        /* Error */
                        break;

                    default:
                        break;
                }
            }).fail(function (data) {
            showMessage(Attendize.GenericErrorMessages);
        });
        e.preventDefault();
    });


});

function changeQuestionType(select)
{
    var select = $(select);
    var selected = select.find(':selected');

    if (selected.data('has-options') == '1') {
        $('#question-options').removeClass('hide');
    } else {
        $('#question-options').addClass('hide');
    }
}



function addQuestionOption()
{
    var tbody = $('#question-options tbody');
    var questionOption = $('#question-option-template').html();

    tbody.append(questionOption);
}

function removeQuestionOption(removeBtn)
{
    var removeBtn = $(removeBtn);
    var tbody = removeBtn.parents('tbody');

    if (tbody.find('tr').length > 1) {
        removeBtn.parents('tr').remove();
    } else {
        alert(lang("at_least_one_option"));
    }
}

function processFormErrors($form, errors)
{
    $.each(errors, function (index, error) {
        var $input = $('input[name^="' + index + '"]', $form);

        // Fix for description wysiwyg form elements
        if (index === 'description') {
            $input = $('.CodeMirror', $form)
        }

        // Try and render a better error message for checkboxes in a table
        if (index.indexOf('[]') > -1) {
            var $formCombinedErrors = $input.closest('form').find('.form-errors');
            // $input.addClass('has-error');
            if ($formCombinedErrors.is(':visible') === false) {
                $formCombinedErrors.append('<div class="help-block error">' + error + '</div>')
                    .removeClass('hidden')
                    .addClass('has-error');
            }
            return false;
        }

        if ($input.prop('type') === 'file') {
            $('#input-' + $input.prop('name')).append('<div class="help-block error">' + error + '</div>')
                .parent()
                .addClass('has-error');
        } else {
            $input.after('<div class="help-block error">' + error + '</div>')
                .parent()
                .addClass('has-error');
        }
    });

    var $submitButton = $form.find('input[type=submit]');
    toggleSubmitDisabled($submitButton);
}



/**
 *
 * @param elm $submitButton
 * @returns void
 */
function toggleSubmitDisabled($submitButton) {

    if ($submitButton.hasClass('disabled')) {
        $submitButton.attr('disabled', false)
            .removeClass('disabled')
            .val($submitButton.data('original-text'));
        return;
    }

    $submitButton.data('original-text', $submitButton.val())
        .attr('disabled', true)
        .addClass('disabled')
        .val('Working...');
}

/**
 *
 * @returns {{}}
 */
$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

/**
 * Replaces a parameter in a URL with a new parameter
 *
 * @param url
 * @param paramName
 * @param paramValue
 * @returns {*}
 */
function replaceUrlParam(url, paramName, paramValue) {
    var pattern = new RegExp('\\b(' + paramName + '=).*?(&|$)')
    if (url.search(pattern) >= 0) {
        return url.replace(pattern, '$1' + paramValue + '$2');
    }
    return url + (url.indexOf('?') > 0 ? '&' : '?') + paramName + '=' + paramValue
}

/**
 * Shows users a message.
 * Currently uses humane.js
 *
 * @param string message
 * @returns void
 */
function showMessage(message) {
    humane.log(message, {
        timeoutAfterMove: 3000,
        waitForMove: true
    });
}

function showHelp(message) {
    humane.log(message, {
        timeout: 12000
    });
}

function hideMessage() {
    humane.remove();
}
