var Area = function()
{
    function areaChangeAction()
    {
        $('select.zilla-selector, select.thana-selector').on('change', function(e){
            e.preventDefault();

            var level = $(this).hasClass('zilla-selector') ? 3 : 4;
            var appendTo = level === 3 ? 'thana-selector' : 'union-selector';
            var param = 'id='+$(this).val();
            if (level === 3) {
                $('select.union-selector, select.thana-selector').select2('data', null);
            } else {
                $('select.union-selector').select2('data', null);
            }

            if (!$(this).val()) {
                return;
            }
            var el = $(this).closest(".portlet").children(".portlet-body");
            Metronic.blockUI({
                target: el,
                animate: true,
                overlayColor: 'black'
            });

            var userId = document.getElementById('user_id_hidden').value;
            $.ajax({
                url: Routing.generate('location_filter_update', {id:userId}),
                data: param,
                success: function(html){
                    $("select."+appendTo).html(html);
                    $('#user_upozilla').select2('destroy').select2();
                    Metronic.unblockUI(el);
                },
                error: function(){
                    Metronic.alert('Server Error');
                    Metronic.unblockUI(el);
                }
            });
        });
    }

    function userOrAgent()
    {
        $("#user_userType").change(function () {
            var x = document.getElementById('user_level2');
            x.style.visibility = 'hidden';

            if($(this).val() == 'AGENT'){
                x.style.visibility = 'visible';
            }else {
                x.style.visibility = 'hidden';
            }
        });
    }

    function init()
    {
        areaChangeAction();
        userOrAgent();
    }

    return {
        init: init
    }
}();

Area.init();