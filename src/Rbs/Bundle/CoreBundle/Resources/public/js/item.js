var Item = function()
{
    function formValidateInit()
    {
        var form = $('#item-price-form');
        if (!form.length) return;

        var focused = false;
        var isFormValid = true;
        form.find('tbody.items tr').each(function(index, e){
            var elm = $(e);
            var amountElm = elm.find('.amount');
            var amount = parseFloat(amountElm.val());

            elm.removeClass('has-error');

            if (amountElm.val() == '' || !amount) {
                elm.addClass('has-error');
                isFormValid = false;

                if (!focused) {
                    amountElm.focus();
                    focused = true;
                }
            }
        });

        return isFormValid;
    }

    function saveItemPrice()
    {
        $('body').on('click', '#save-item-price', function(){
            var loadingDiv = $('.modal-body').find('.portlet');
            var form = $('#item-price-form');
            if (formValidateInit()) {
                Metronic.blockUI({
                    target: loadingDiv,
                    animate: true,
                    overlayColor: 'black'
                });
                $.post(form.attr('action'), form.serialize())
                    .done(function(){
                        toastr.success('Item Price Update Successfully');
                        Metronic.unblockUI(loadingDiv);
                        $('#price-set-modal').modal('hide');

                        if ($('#item_price_log_datatable').length) {
                            $('#item_price_log_datatable').DataTable().draw();
                        }
                    })
                    .fail(function(){
                        toastr.error('Server error. Contact with System Admin');
                        Metronic.unblockUI(loadingDiv);
                    });
            } else {
                toastr.clear();
                toastr.error('Invalid Price');
            }

            return false;
        }).on('click', '#set-default-price', function(){
            var amount = 0;
            $('.amount').each(function(index, el){
                if (index == 0) amount = $(this).val();

                $(this).val(amount);
            });
        });
    }

    function init()
    {
        saveItemPrice();
    }

    return {
        init: init
    }
}();

Item.init();