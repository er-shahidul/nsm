$(".datepicker-year").datepicker( {
    format: "yyyy",
    viewMode: "years",
    minViewMode: "years"
});
$(".datepicker-month").datepicker( {
    format: "mm",
    viewMode: "months",
    minViewMode: "months"
});

$('#itemReportExcel').click(function (url) {
console.log(url);
    setTimeout(function(){
        $('#item_report').attr('action', url);
    }, 1000);

    $.ajax({
        form: 'item_report',
        url: '/report/upozilla_wise_excel',
        dataType: 'form html'
    });
});

$('#yearlyItemReportExcel').click(function (url) {

    setTimeout(function(){
        $('#yearly_item_report').attr('action', url);
    }, 1000);

    $.ajax({
        form: 'yearly_item_report',
        url: '/report/yearly_item_excel',
        dataType: 'form html'
    });
});