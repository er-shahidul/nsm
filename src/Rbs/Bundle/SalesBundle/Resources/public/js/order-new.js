var Order = function()
{
    var agent = $("#order_agent").val();

    function bindItemChangeEvent(collectionHolder) {
        collectionHolder.find('tr').each(function(index, elm){
            $(elm).find('select').change(function(){
                findStockItem($(this).val(), index);

                $("#order_orderItems_" + index + "_remove").click(function () {
                    deleteOrderItemHandler(collectionHolder, index);
                });

            }).trigger('change');
        });
    }

    function deleteOrderItemHandler(collectionHolder, index)
    {
        if (collectionHolder.find('tr').length == 1) {
            bootbox.alert("Minimum One Item Require.");
            return false;
        }
        $('#order-item-'+index).remove();
        totalAmountCalculate();
    }

    function addItemForm($collectionHolder) {

        if ($('#order_agent').val() == '') {
            toastr.error("Please select an agent.");
            return false;
        }

        if ($('#order_depo').val() == '') {
            toastr.error("Please select a depo.");
            return false;
        }

        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var $newForm = prototype.replace(/__name__/g, index);

        $collectionHolder.data('index', index + 1);
        //var $newFormLi = $('<div></div>').append(newForm);
        $collectionHolder.append($newForm);

        $("#order_orderItems_" + index + "_item").change(function () {
            var item = $(this).val();
            findStockItem(item, index);
        });

        $("#order_orderItems_" + index + "_remove").click(function () {
            deleteOrderItemHandler($collectionHolder, index);
        });

        App.integerMask($collectionHolder.find('tr:eq('+index+')').find('.quantity'));
    }

    function findStockItem(item, index) {
        var collectionHolder = $('.order-item-list');
        if (item == "") {
            setOrderItemValue(index, 0, 0, false, 0, '');
            totalAmountCalculate();
            return false;
        }

        // Check Duplicate Item Select
        if (isItemExits(collectionHolder, item, index)) {
            var selectedItem = $('#order-item-'+index).find('select');
            toastr.error(selectedItem.find(":selected").text() + " Item Already Selected.");
            selectedItem.val("").trigger('change');
            return false;
        }

        Metronic.blockUI({
            target: collectionHolder,
            animate: true,
            overlayColor: 'black'
        });

        $.ajax({
            type: "post",
            url: Routing.generate('find_stock_item_depo_ajax', {
                item: item,
                agent: $('#order_agent').val(),
                depo: $('#order_depo').val(),
                order: $('#order_id').val()
            }),
            //data: "item=" + item + "&agent=" + $('#order_agent').val() + "&depoId=" + $('#order_depo').val() + "&orderId=" + $('#order_id').val() ? $('#order_id').val() : '',
            dataType: 'json',
            success: function (response) {
                var onHand = response.onHand;
                var onHold = response.onHold;
                var available = response.available;
                var price = response.price;
                var itemUnit = response.itemUnit;

                setOrderItemValue(index, onHand, onHold, available, price, itemUnit, false);
                Metronic.unblockUI(collectionHolder);
            },
            error: function(){
                Metronic.unblockUI(collectionHolder);
            }
        });
    }

    function totalAmountCalculate() {
        var subTotal = 0;
        var totalAmount = 0;
        $('.total_price').each(function () {
            subTotal = parseFloat($(this).val());
            if (subTotal) {
                totalAmount += subTotal;
            }

        });

        $("#order_totalAmount").val(totalAmount.toFixed(2));
    }

    function recalculateItemPriceOnEdit() {
        setTimeout(function(){
            //$('.order-item-list tbody .quantity').each(totalPriceCalculation);
        }, 100);
    }

    function totalPriceCalculation() {
        var row = $(this).closest('td').parent('tr');
        var price = parseFloat(row.find('.price').val());
        var quantity = parseFloat(row.find('.quantity').val());
        if (!price) { price = 0; }
        if (!quantity) { quantity = 0; }
        row.find('.total_price').val((price * quantity).toFixed(2));
        totalAmountCalculate();
    }

    function setOrderItemValue(index, onHand, onHold, availableOnDemand, price, itemUnit, itemQty){
        var row = $('#order-item-'+index);

        var stockAvailableInfo = 'Available On Demand';

        if (!availableOnDemand) {
            stockAvailableInfo = parseInt(onHand) - parseInt(onHold);
        }
        row.find('.item-price input').val(price);
        row.find('.stock-available').text(stockAvailableInfo);
        if (itemQty) {
            row.find('.quantity').val(itemQty);
        }
        row.find('.item-unit').text(itemUnit);
    }

    function isItemExits(collectionHolder, item, index)
    {
        var found = false;
        collectionHolder.find('tbody tr').not('#order-item-'+index).each(function(i, el){
            if (item == $(el).find('select').val()) {
                found = true;
                return false;
            }
        });

        return found;
    }

    function newOrder()
    {
        var $collectionHolder;
        var $addTagLink = $('#add_order_item');
        var agentElm = $('#order_agent');
        var depoElm = $('#order_depo');
        $collectionHolder = $('tbody.tags');
        $collectionHolder.data('index', $collectionHolder.find(':input').length);
        bindItemChangeEvent($collectionHolder);
        $addTagLink.on('click', function(e) {
            e.preventDefault();
            addItemForm($collectionHolder);
        });

        depoElm.change(function () {
            $collectionHolder.find('tr').remove();
            if (depoElm.val() != '' && agentElm.val() != '') {
                $addTagLink.trigger('click');
            }
        });

        agentElm.change(function () {

            $collectionHolder.find('tr').remove();
            var agent = $(this).val();
            if (agent == false) {
                $('.hide_button').hide();
            } else {
                Metronic.blockUI({
                    target: null,
                    animate: true,
                    overlayColor: 'black'
                });
                $.ajax({
                    type: "post",
                    url: Routing.generate('find_agent_ajax'),
                    data: "agent=" + agent,
                    dataType: 'json',
                    success: function (response) {
                        var item_type_prototype = response.item_type_prototype;
                        $collectionHolder.data('prototype', item_type_prototype);
                        Metronic.unblockUI();
                        if (depoElm.val() != '' && agentElm.val() != '') {
                            $addTagLink.trigger('click');
                        }
                    },
                    error: function(){
                        Metronic.unblockUI();
                    }
                });
                $('.hide_button').show();
            }
        });

        $('.order-item-list tbody').on("click keyup", ".quantity", (totalPriceCalculation));
        recalculateItemPriceOnEdit();
    }

    function filterInit(){
        if (!$('#external_filter_container').length) {
            $('<div id="external_filter_container">' +
                'Filter: <div id="order-status"></div>' +
                '<div id="order-payment-status"></div>' +
                '<div id="order-delivery-status"></div>' +
                '<div id="order-agent"></div>' +
                '</div>').appendTo('#order_datatable_filter');
        }
        $("#order_datatable").dataTable().yadcf([

                {
                    column_number: 1,
                    filter_type: 'text',
                    filter_container_id: "order-agent",
                    filter_reset_button_text: false,
                    filter_default_label: "Agent Id"
                },
                {
                    column_number: 4,
                    data: ["PENDING", "PROCESSING", "COMPLETE", "CANCEL", "HOLD"],
                    filter_container_id: "order-status",
                    filter_reset_button_text: false,
                    filter_default_label: "Order State"
                },
                {
                    column_number: 5,
                    data: ["PENDING", "PARTIALLY_PAID", "PAID"],
                    filter_container_id: "order-payment-status",
                    filter_reset_button_text: false,
                    filter_default_label: "Payment State"
                },
                {
                    column_number: 6,
                    data: ["PENDING", "PARTIALLY_SHIPPED", "SHIPPED", "HOLD"],
                    filter_container_id: "order-delivery-status",
                    filter_reset_button_text: false,
                    filter_default_label: "Delivery State"
                }
            ]
        );

        var orderFilterContainer = $('#order_datatable_filter');
        // Add class to select to match with theme
        orderFilterContainer.find('select').addClass("form-control");

        // Remove global search box
        orderFilterContainer.addClass('pull-right').find('label').remove();
        // Remove Individual Filter Inputs
        //$('.dataTables_scrollHead').find('table thead tr').eq(1).remove();

        // Humanize Filter Option's Text
        setTimeout(function(){
            orderFilterContainer.find('select option').each(function(){
                $(this).text($(this).text().replace('_', ' '));
            });
        },500);
    }

    function init()
    {
        newOrder();
        formValidateInit();
    }

    function formValidateInit()
    {
        var form = $('form[name=order]');
        if (!form.length) return;

        form.submit(function(){
            var isFormValid = true,
                orderItem = $('#orderItems'),
                qtyError = false,
                priceError = false;


            if ($('#order_agent').val() == '') {
                toastr.error("Please select an agent");
                isFormValid = false;
            }

            if (isFormValid && $('#order_refSMS').val() == '') {
                toastr.error("Please select a reference SMS");
                isFormValid = false;
            }

            if (isFormValid && $('#order_depo').val() == '') {
                toastr.error("Please select a depo");
                isFormValid = false;
            }

            if (isFormValid && orderItem.find('tr').length == 0) {
                toastr.error("Please add minimum an item");
                isFormValid = false;
            }

            orderItem.find('tr').each(function(index, e){
                var elm = $(e);
                var item = elm.find('td:eq(0)');
                var qty = elm.find('td:eq(1)');
                var stock = elm.find('td:eq(3)').text();
                var price = elm.find('td:eq(4)');

                item.removeClass('has-error');
                qty.removeClass('has-error');
                if (item.find('select').val() == '') {
                    item.addClass('has-error');
                    isFormValid = false;
                }

                if (!parseFloat(price.find('input').val())) {
                    price.addClass('has-error');
                    isFormValid = false;
                    priceError = true;
                }

                if (
                    qty.find('input').val() == '' ||
                    parseInt(qty.find('input').val()) == 0 ||
                    (stock != 'Available On Demand' && (parseInt(qty.find('input').val()) > parseInt(stock)))
                ) {
                    qty.addClass('has-error');
                    isFormValid = false;
                    qtyError = true;
                }

            });

            if (qtyError) {
                toastr.error("Invalid Item Quantity");
            }

            if (priceError) {
                toastr.error("Invalid Price");
            }

            return isFormValid;
        });
    }

    function OrderStateFormat(data, type, row, meta){
        return data;
    }

    function OrderPaymentFormat(data, type, row, meta){
        return data;
    }

    function paymentConfirmationOnModal() {
        $("#ajaxSummeryView").on('click', '.payment-action-buttons button',function (event) {
            event.preventDefault();
            // var setDepositedAmount = document.getElementById("amount").innerHTML;
            var setDepositedAmount = $(this).closest('tr').find('.deposit_amount').val();
            var element = $(this);
            var current_tr = $(element).closest('tr');
            var paymentId = $(this).val();
            var isVerified = $(this).data('id') == 'payment-amount-verified';
            if(isVerified) {

                var setActualAmount = prompt("Actual Amount:", setDepositedAmount);

                if (setActualAmount != null || setActualAmount != "") {
                    $.ajax({
                        type: "get",
                        url: Routing.generate('payment_amount_verified', {
                            id: paymentId,
                            verified: isVerified,
                            actualAmount: setActualAmount,
                            depositedAmount: setDepositedAmount
                        }),
                        dataType: 'json',
                        success: function (response) {
                            $(element).closest('tr').find('.payment-action-buttons').html(response.message);
                            $(current_tr).find('.actual-amount-new').html(response.actualAmount);
                            if (!isVerified) {
                                setTimeout(function () {
                                    $(element).closest('tr').remove();
                                }, 5000);
                            }
                        }
                    });
                }
            }else{
                $.ajax({
                    type: "get",
                    url: Routing.generate('payment_amount_verified', {
                        id: paymentId,
                        verified: isVerified,
                        actualAmount: setActualAmount,
                        depositedAmount: setDepositedAmount
                    }),
                    dataType: 'json',
                    success: function (response) {
                        $(element).closest('tr').find('.payment-action-buttons').html(response.message);
                        if (!isVerified) {
                            setTimeout(function () {
                                $(element).closest('tr').remove();
                            }, 5000);
                        }
                    }
                });
            }

        });
    }

    return {
        init: init,
        filterInit: filterInit,
        formValidateInit: formValidateInit,
        OrderStateFormat: OrderStateFormat,
        OrderPaymentFormat: OrderPaymentFormat,
        PaymentConfirmationOnModal: paymentConfirmationOnModal
    }
}();