var Area = function()
{
    function areaChangeAction()
    {
        $("#rsm_swap_areaNew").change(function () {
            var locationId = $(this).val();
            $.ajax({
                type: "post",
                url: Routing.generate('get_users_by_location', {id: locationId}),
                dataType: 'json',
                success: function (data) {
                    var ordersElm = $("#rsm_swap_userChange");
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
        areaChangeAction();
    }

    return {
        init: init
    }
}();

Area.init();