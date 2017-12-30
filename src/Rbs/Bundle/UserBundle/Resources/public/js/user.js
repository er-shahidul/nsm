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
                param += '&upozila_id='+$('#user_upozilla').val();
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

            $.ajax({
                url: Routing.generate('location_filter'),
                data: param,
                success: function(html){
                    $("select."+appendTo).html(html).select2('destroy').select2();
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

    // Not Implemented
    // TODO
    function userFormValidation() {

        var form3 = $('form[name=user]');
        var error3 = $('.alert-danger', form3);
        var success3 = $('.alert-success', form3);

        form3.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            rules: {
                'user[profile][fullName]': {
                    minlength: 2,
                    required: true
                },
                'user[email]': {
                    required: true,
                    email: true
                },
                'user[profile][cellphone]': {
                    required: true
                }
            },

            messages: { // custom messages for radio buttons and checkboxes
                membership: {
                    required: "Please select a Membership type"
                },
                service: {
                    required: "Please select  at least 2 types of Service",
                    minlength: jQuery.validator.format("Please select  at least {0} types of Service")
                }
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                console.log(error);
                console.log(element);
                if (element.parent(".input-group").size() > 0) {
                    error.insertAfter(element.parent(".input-group"));
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) {
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                console.log(event);
                success3.hide();
                error3.show();
                Metronic.scrollTo(error3, -200);
            },

            highlight: function (element) { // hightlight error inputs
                console.log('hight');
                console.log(element);
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                console.log('unhi');
                console.log(element);
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                console.log('success');
                console.log(label);
                label
                    .closest('.form-group').removeClass('has-error'); // set success class to the control group
            },

            submitHandler: function (form) {
                success3.show();
                error3.hide();
                form[0].submit(); // submit the form
            }

        });

    }

    return {
        init: init,
        userFormValidation: userFormValidation
    }
}();

Area.init();