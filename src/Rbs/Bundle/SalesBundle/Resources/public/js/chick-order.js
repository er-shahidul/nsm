var ChickOrder = function()
{
    function ManageInit() {
        $('.chick-order-region-summary').each(function(){
            var currentRegion = $(this);
            var totalElm = $(this).find('.total-qty');
            $(this).find('.qty').keyup(function(){
                calcTotalOfCurrentRegion(currentRegion, totalElm);
                calTotalOfAllRegion();
            });

            calcTotalOfCurrentRegion(currentRegion, totalElm);
        });
        calTotalOfAllRegion();

        $("input[name=save]").click(function (e) {
            e.preventDefault();
            save(false);
        });

        $("input[name=save-and-deliver]").click(function (e) {
            e.preventDefault();

            bootbox.confirm('Are you sure?', function(res){
                if (res) {
                    save(true);
                }
            });
        });

        $('#chick-order-manage-form').submit(function(e){
            e.preventDefault();
            save(false);
        });

        initGotoViewPage();
    }

    function save(sendToDeliver){
        var form = $('#chick-order-manage-form');
        Metronic.blockUI({
            target: form,
            animate: true,
            overlayColor: 'black'
        });

        $('input[type=submit]').attr('disabled', true);

        $.ajax({
            type: "post",
            url: Routing.generate('order_manage_chick_save'),
            data: form.serialize() + '&deliver=' + sendToDeliver,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (sendToDeliver) {
                        toastr.success('Please wait, redirecting to view page.');
                        window.location.href = getChickOrderViewPageUrl();
                    } else {
                        toastr.success('Save Successfully');
                        $('input[type=submit]').attr('disabled', false);
                    }
                } else {
                    toastr.error('Service Error: ' + response.message);
                    $('input[type=submit]').attr('disabled', false);
                }
                Metronic.unblockUI(form);
            },
            error: function(){
                Metronic.unblockUI(form);
                $('input[type=submit]').attr('disabled', false);
            }
        });
    }

    function calcTotalOfCurrentRegion(currentRegion, totalElm) {
        var total = 0;
        currentRegion.find('.qty').each(function(){
            var val = $(this).is('td') ? $(this).text() : $(this).val();
            if (!val) val = 0;
            total += parseInt(val);
        });
        totalElm.text(total);
    }

    function getChickOrderViewPageUrl() {
        return Routing.generate('order_manage_chick_summary', {'date': $('form.chick-order-filter').find('input[type=text]').val(), 'item': $('form.chick-order-filter').find('select').val()})
    }

    function initGotoViewPage() {
        $('.go-to-view-page').click(function(){
            if ($('form.chick-order-filter').find('input[type=text]').val() == '') {
                toastr.error('Please select a date');
                return false;
            }

            window.location.href = getChickOrderViewPageUrl();
        });
    }

    function calTotalOfAllRegion() {
        var total = 0;
        $('.total-qty').each(function(){
            total += parseInt($(this).text());
        });
        $('.grand-total span').text(total);
    }

    return {
        ManageInit: ManageInit
    }
}();