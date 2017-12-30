var Delivery = function()
{
    function filterInit(){
        if (!$('#external_filter_container').length) {
            $('<div id="external_filter_container">' +
                '<div><input class="form-control input-small" placeholder="Order ID"></div>' +
                '<div><input class="form-control date-picker" placeholder="Order Date"></div>' +
                '<div id="agent-filter"></div>' +
                '<button class="btn green pull-right payment-filter-btn">Filter</button>' +
                '</div>').appendTo('#delivery_datatable_filter');
        }

        var table = $('#delivery_datatable').DataTable();
        var filterContainer = $('#external_filter_container');
        $('.date-picker').datepicker({
            autoclose: true,
            todayBtn: "linked",
            format: 'dd-mm-yyyy'
        });

        // Filter Button Action - Filter Payment
        $('.payment-filter-btn').on('click', function(e){
            var date = filterContainer.find('input:eq(1)').datepicker("getDate");
            date = date ? moment(date).format('DD-MM-YYYY') : '';
            table
                .columns(0).search(filterContainer.find('input:eq(0)').val())
                .columns(1).search(date)
                .draw();
        });

        // Enter key trigger
        filterContainer.find('input').on('keyup', function(e){
            if (e.keyCode == 13) {
                $('.payment-filter-btn').trigger('click');
            }
        });

        // Set datatable state value
        var datatableSaveState = table.state();
        filterContainer.find('input:eq(0)').val(datatableSaveState.columns[0].search.search);
        filterContainer.find('input:eq(1)').val(datatableSaveState.columns[1].search.search);

        var orderFilterContainer = $('#delivery_datatable_filter');
        // Add class to select to match with theme
        orderFilterContainer.find('select').addClass("form-control");

        // Remove global search box
        orderFilterContainer.addClass('pull-right').find('label').remove();
        // Remove Individual Filter Inputs
        $('.dataTables_scrollHead').find('table thead tr').eq(1).remove();
    }

    function formValidateInit()
    {
        var oneValidValue = false;
        $('.orderItems').find('tr').each(function(index, e){
            var elm = $(e);
            var deliver = elm.find('.deliver');

            deliver.removeClass('has-error');

            var deliveryQtq = deliver.find('input').val();

            if (parseInt(deliveryQtq) > 0) {
                oneValidValue = true;
            }

        });

        return oneValidValue;
    }

    function orderItemRemainingHandle()
    {
        $('.orderItems').find('.deliver-qty').blur(function(){
            var elm = $(this).parents('tr');

            var qty = parseInt(elm.find('.item-qty').text());
            var deliveryQtq = parseInt($(this).val());
            var remain = elm.find('.remain');
            var check = elm.find('[type=checkbox]');

            if (check.length && !check.is(':checked')) {
                return;
            }

            if (deliveryQtq !== 0 && (!deliveryQtq || (qty - deliveryQtq) < 0)) { // Invalid value
                remain.text(qty);
                $(this).val(0);
                toastr.error('Invalid value OR delivered quantity is greater then Order quantity');
            } else {
                remain.text(qty - deliveryQtq);
            }

        }).blur();
    }

    function orderProgressHandle()
    {
        var container = $('#process-actions');
        var loadingDiv = $('.modal-body').find('.portlet');

        container.find('button').on('click', function(){
            var that = $(this);
            bootbox.confirm('Are you sure?', function(result) {
                if (result) {

                    Metronic.blockUI({
                        target: loadingDiv,
                        animate: true,
                        overlayColor: 'black'
                    });

                    $.get(that.attr('data-route'), function(){

                        that.closest('td').text(moment().format('h:mm a'));
                        var nextBtn = container.find('td button:eq(0)');
                        if (nextBtn) {
                            nextBtn.attr('disabled', false);
                        }

                        if (that.attr('data-action') == 'finish-loading') {
                            $('#save-delivery').attr('disabled', false);
                        }

                        Metronic.unblockUI(loadingDiv);

                    }, 'json').fail(function() {
                        Metronic.unblockUI(loadingDiv);
                        toastr.error('Server error');
                    });
                }
            });
        });
    }

    function saveDelivery()
    {
        $('#deliveryView').on('shown.bs.modal', function (){
            setTimeout(function(){
                orderItemRemainingHandle();
                orderProgressHandle();
                $('#process-actions span').tooltip();
                App.integerMask($('#delivery-item-form').find('.orderItems .deliver-qty'));
            }, 500);
        });

        $('body').on('click', '#save-delivery', function(){
            var loadingDiv = $('.modal-body').find('.portlet');
            if (formValidateInit()) {
                bootbox.confirm('Are you sure?', function(result) {
                    if (result) {
                        Metronic.blockUI({
                            target: loadingDiv,
                            animate: true,
                            overlayColor: 'black'
                        });
                        $.post(Routing.generate('delivery_save', {id:$('#delivery-id').val()}), $('#delivery-item-form').serialize())
                            .done(function(){
                                toastr.success('Order Delivery Saved Successfully. Please wait while page reload.');
                                // location.reload();
                                window.location.href= Routing.generate('vehicle_info_load_list')
                            })
                            .fail(function(){
                                toastr.error('Server error. Contact with System Admin');
                                Metronic.unblockUI(loadingDiv);
                            });
                    }
                });
            } else {
                toastr.error('Please enter at least one quantity');
            }

            return false;
        });
    }

    function init()
    {
        saveDelivery();
    }

    return {
        init: init,
        filterInit: filterInit,
        formValidateInit: formValidateInit
    }
}();