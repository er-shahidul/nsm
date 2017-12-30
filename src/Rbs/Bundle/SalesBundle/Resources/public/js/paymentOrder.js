var Payment = function()
{
    function filterInit(allowAgentSearch){

        if (!$('#external_filter_container').length) {
            $('<div id="external_filter_container">' +
                '<div id="date-filter"><div class="input-group input-daterange">' +
                '<input type="text" class="form-control input-small" value="">' +
                '<span class="input-group-addon">to</span>' +
                '<input type="text" class="form-control input-small" value="" style="margin-left: 0">' +
                '</div></div>' +
                '<div id="agent-filter"></div>' +
                '<button class="btn green pull-right payment-filter-btn">Filter</button>' +
                '</div>').appendTo('#payment_datatable_filter');
        }

        var table = $('#payment_datatable').DataTable();

        // Bangla hack to fix datatable break layout
        $(window).trigger('resize');
        $(window).trigger('resize');

        $('.input-daterange').datepicker({
            autoclose: true,
            todayBtn: "linked",
            format: 'dd-mm-yyyy'
        });

        if (allowAgentSearch) {
            $("#agent-filter").select2({
                placeholder: "Agents",
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: Routing.generate('agent_search'),
                    dataType: 'json',
                    quietMillis: 250,
                    data: function (term, page) {
                        return {
                            q: term
                        };
                    },
                    results: function (data) {
                        return {results: data};
                    },
                    cache: true
                }
            }).on('change', function(){
                //table.columns(1).search($(this).val()).draw();
            }).prev().addClass('form-control input-medium').css('vertical-align', 'initial');

        }

        // Filter Button Action - Filter Payment
        $('.payment-filter-btn').on('click', function(){
            var startDate = $('#date-filter input:eq(0)').datepicker("getDate");
            var endDate = $('#date-filter input:eq(1)').datepicker("getDate");

            var fromDate = moment($('#date-filter input:eq(0)').datepicker("getDate")).format('DD-MM-YYYY');
            var toDate = moment($('#date-filter input:eq(1)').datepicker("getDate")).format('DD-MM-YYYY');

            if (startDate && endDate) {
                table.columns(0).search(fromDate+'--'+toDate);
            } else {
                table.columns(0).search('');
            }
            if (allowAgentSearch) {
                table.columns(1).search($("#agent-filter").val());
            }
            table.draw();
        });

        var orderFilterContainer = $('#payment_datatable_filter');
        // Add class to select to match with theme
        orderFilterContainer.find('select').addClass("form-control");

        // Remove global search box
        orderFilterContainer.addClass('pull-right').find('label').remove();
        // Remove Individual Filter Inputs
        $('.dataTables_scrollHead').find('table thead tr').eq(1).remove();
    }

    function handleAgentChange()
    {
        if (!$("#payment_agent").length) {
            return;
        }
        $("#payment_agent").change(function () {
            var ordersElm = $('#payment_orders');
            var agent = $(this).val();
            if (agent == '') {
                ordersElm.find('option').remove();
                return fales;
            }
            $.ajax({
                type: "post",
                url: Routing.generate('partial_payment_orders', {id: agent}),
                dataType: 'json',
                success: function(data) {
                    ordersElm.find('option').remove();
                    for (var i=0; i < data.length; i++) {
                        var arr = data[i];
                        ordersElm.append('<option value="'+arr['id']+'">'+arr['text']+'</option>');
                    }
                },
                error: function(){
                    toastr.error('Server Error');
                }
            });
        });
    }

    function init()
    {
        handleAgentChange();
    }

    return {
        init: init,
        filterInit: filterInit
    }
}();