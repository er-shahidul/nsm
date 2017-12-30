var ItemPriceLog = function()
{
    function filterInit(){
        if (!$('#external_filter_container').length) {
            $('<div id="external_filter_container">' +
                'Filter: <div id="order-status"></div>' +
                '</div>').appendTo('#item_price_log_datatable_filter');


        }
        $("#item_price_log_datatable").dataTable().yadcf([
                {
                    column_number: 0,
                    select_type: 'select2',
                    select_type_options: {
                        containerCssClass: 'form-control input-medium'
                    },
                    data: locationList,
                    filter_container_id: "order-status",
                    filter_reset_button_text: false,
                    filter_default_label: "Select a District"
                }
            ]
        );

        //$('#item_price_log_datatable_filter').find('select').select2();

        var orderFilterContainer = $('#item_price_log_datatable_filter');
        // Add class to select to match with theme
        orderFilterContainer.find('select').addClass("form-control");

        // Remove global search box
        orderFilterContainer.addClass('pull-right').find('label').remove();
    }

    function init()
    {

    }

    return {
        init: init,
        filterInit: filterInit
    }
}();

ItemPriceLog.init();