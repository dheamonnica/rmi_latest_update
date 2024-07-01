<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

<script type="text/javascript">
    ;(function ($, window, document) {
        // var sorter = $('#sortable').rowSorter({
        var startDate;
        var endDate;

        //let dataString = "fromDate=&toDate=";
        //dateToDateSearch(dataString);
        $(document).ready(function () {
            $('#daterangepicker').daterangepicker(
                {
                    startDate: moment().subtract('days', 6),
                    endDate: moment(),
                    showDropdowns: false,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 30,
                    timePicker12Hour: false,
                    ranges: {
                        '{{ trans('app.today') }}': [moment(), moment()],
                        '{{ trans('app.yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '{{ trans('app.last_7_days') }}': [moment().subtract(6, 'days'), moment()],
                        '{{ trans('app.last_30_day') }}': [moment().subtract(29, 'days'), moment()],
                        '{{ trans('app.this_month') }}': [moment().startOf('month'), moment()],
                        '{{ trans('app.last_month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                        '{{trans('app.last_12_month')}}': [moment().startOf('month').subtract(12, 'month'), moment().endOf('month')],
                        '{{trans('app.this_year')}}': [moment().startOf('year'), moment()],
                        '{{trans('app.last_year')}}': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                    },
                    opens: 'left',
                    buttonClasses: ['btn btn-default'],
                    cancelClass: 'btn-small',
                    format: 'DD/MM/YYYY',
                    separator: ' to ',
                },
                function (start, end) {
                    //console.log("Callback has been called!");
                    $('#daterangepicker span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
                    $('#getFromDate').val(start.format('YYYY-MM-DD'));
                    $('#getToDate').val(end.format('YYYY-MM-DD'));

                    startDate = start.format('YYYY-MM-DD');
                    endDate = end.format('YYYY-MM-DD');
                    //Filter Variable Values
                    let customer = $('#customerId').val();
                    let shop = $('#shopId').val();
                    let orderNumber = $('#orderNumber').val();
                    let orderStatus = $('#orderStatus').val();
                    //console.log(window.location.hostname)
                    let dataString = "customerId=" + customer + "&shopId=" + shop + "&orderNumber=" + orderNumber +
                        "&orderStatus=" + orderStatus + "&fromDate=" + startDate + "&toDate=" + endDate;
                    //Data Table Reset After Ajax:
                    dataTableResetting(dataString);
                    //Get Chart Data Via Ajax:
                    let ajaxUrl = '{{route('admin.sales.getMoreForChart')}}';
                    $.ajax({
                        url:ajaxUrl+'/?'+dataString,
                        contentType: 'application/json',
                        success:function (response){
                            generate.clear();
                            generate.destroy();
                            //console.log(generate);
                            chartFormatData = chartDataFormat(response.data);
                            //generate.update(salesCtx, chartFormatData)
                            generate = new Chart(salesCtx, chartFormatData);
                            ///addData(generate, chartFormatData);
                        }
                    });
                }
            );
            //Set the initial state of the picker label
            $('#daterangepicker span').html(moment().subtract('days', 7).format('D MMMM YYYY') + ' - ' + moment().format('D MMMM YYYY'));
            $('#getFromDate').val(moment().subtract('days', 7).format('YYYY-MM-DD'));
            $('#getToDate').val(moment().format('YYYY-MM-DD'));


            /*Chart to Image Download*/
            /*document.getElementById("downloadOrder").addEventListener('click', function(){
                /!*Get image of canvas element*!/
                var url_base64jp = document.getElementById("salesReport").toDataURL("image/jpg");
                /!*get download button (tag: <a></a>) *!/
                var a =  document.getElementById("downloadOrder");
                /!*insert chart image url to download button (tag: <a></a>) *!/
                a.href = url_base64jp;
            });*/


        });
        ///Calling Chart Function to manipulate:

    }(window.jQuery, window, document));
</script>