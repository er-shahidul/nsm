$.xhrPool = [];
$.xhrPool.queue = [];
$.xhrPool.abortAll = function() {
    if (!$.xhrPool) {
        return;
    }
    $($.xhrPool.queue).each(function(idx, jqXHR) {
        jqXHR.abort();
    });
    $.xhrPool.queue = [];
};

$.ajaxSetup({
    beforeSend: function(jqXHR) {
        $.xhrPool.queue.push(jqXHR);
    },
    complete: function(jqXHR) {
        var index = $.xhrPool.indexOf(jqXHR);
        if (index > -1) {
            $.xhrPool.queue.splice(index, 1);
        }
    }
});

var App = function() {

    function initDeleteButton()
    {
        $('body').on('click', '.delete-list-btn', function(){
            var url = $(this).attr('href');
            bootbox.confirm("Are you sure?", function(result) {
                if (result) {
                    var deleteForm = $('form#delete-form');
                    if (deleteForm.length) {
                        deleteForm.attr('action', url).submit();
                    } else {
                        document.location.href = url;
                    }
                }
            });

            return false;
        });
    }

    function initConfirmationButton()
    {
        $('body').on('click', '.confirmation-btn', function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            var msg = $(this).attr('data-title') != '' ? $(this).attr('data-title') : 'Are you sure?';
            bootbox.confirm(msg, function(result) {
                if (result) {
                    document.location.href = url;
                }
            });

            return false;
        });
    }

    var handleMultiSelect = function() {
        if (!$().multiSelect) {
            return;
        }
        $("select[multiple=multiple]").multiSelect({selectableOptgroup: true});
    };

    function handleAjaxModal()
    {
        var modals = [];
        $('div.modal').each(function(index, modal){
            modals.push(modal);
        });

        if (!modals.length) return false;

        $(modals).on('hidden.bs.modal', function (e) {
            $(this).removeData('bs.modal');
            if ($(e.target).attr('role') == 'dialog') {
                return false;
            }
            $(this).find('.modal-content').html('<div class="modal-body">'+
                '<img src="/assets/global/img/loading-spinner-grey.gif" alt="" class="loading">' +
                '<span> &nbsp;&nbsp;Loading... </span></div>');
            try{
                $.xhrPool.abortAll();
            } catch(e){}
        });

        // Datepicker Init on Modal
        $(modals).on('shown.bs.modal', function (e) {
            setTimeout(function(){
                $('div.modal').find('.date-picker').datepicker({
                    format: "dd-mm-yyyy",
                    autoclose: true,
                    todayBtn: "linked"
                }).datepicker('setDate', new Date());
            }, 200);
        });
    }

    function init()
    {
        $('body').on('click', '.order-cancel-modal-action, .confirmation-btn', function(e){
            e.preventDefault();
            if ($('.payment-action-buttons button').length){
                toastr.error("Please Approve or Reject payment first.");
                e.stopImmediatePropagation();
            }
        });

        initDeleteButton();
        initConfirmationButton();
        handleMultiSelect();
        handleAjaxModal();

        $('body').on('click', '.order-cancel-modal-action', function(e){
            e.stopPropagation();
            $('.main-content').slideUp();
            $('.action-content-cancel').slideDown();
        }).on('click', '.order-hold-modal-action', function(){
            $('.main-content').slideUp();
            $('.action-content-hold').slideDown();
        }).on('click', '.cancel-submit', function(){
            $('.order-hold-modal-action, .action-content-cancel').slideUp();
            $('.main-content').slideDown();
        }).on('submit', '.action-content-form', function(){
            $(this).find('input[type=submit], button[type=submit]').attr('disabled', true);
        });

        if (jQuery().datepicker) {
            $('.date-picker, .input-daterange').datepicker({
                rtl: Metronic.isRTL(),
                //orientation: "left",
                autoclose: true,
                format: "dd-mm-yyyy",
                todayBtn: "linked"
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        initIntergerMask($('.input-mask-number'));
        initAmountMask($('.input-mask-amount'));
        initPhoneMask($('.input-mask-phone'));
    }

    function initAmountMask(el){
        $(el).inputmask({
            alias: "numeric",
            placeholder: "0",
            autoGroup: !0,
            digits: 2,
            digitsOptional: !1,
            clearMaskOnLostFocus: !1,
            prefix: "",
            groupSeparator: "",
            removeMaskOnSubmit: false
        });
    }

    function initIntergerMask(el){
        $(el).inputmask("integer", {removeMaskOnSubmit: false});
    }

    function initPhoneMask(el){
        $(el).inputmask("+8801999999999", {removeMaskOnSubmit: false, placeholder: '+8801xxxxxxxxx'});
    }

    return {
        init: init,
        phoneMask: initPhoneMask,
        integerMask: initIntergerMask,
        amountMask: initAmountMask
    }
}();

function resolveAgentName(data, type, row, meta)
{
    return row.fullName;
}
