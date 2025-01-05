<script type="text/javascript">
    /*************************************
     *** Initialise application plugins ***
     **************************************/
    // var jq214 = jQuery.noConflict(true);
    ;
    (function($, window, document) {
        // console.log($().jquery);
        $(".ajax-modal-btn").hide(); // hide the ajax functional button untill the page load completely

        $('img').on('error', function() {
            $(this).hide();
        });

        // Update the hash into the url when click a tab
        $('.nav a').on('show.bs.tab', function(e) {
            let offset = $(this).offset().top; // Get the offset of the element from the top

            window.location = $(this).attr('href'); // Update the hash into the url

            $(this).offset().top = offset; // Set the offset of the element from the top
        });

        // Activate the tab if the url has any #hash
        $(function() {
            let hash = window.location.hash;
            hash && $('ul.nav a[href="' + hash + '"]').tab('show');
        });

        $(document).ready(function() {
            // show the ajax functional button when the page loaded completely and
            // Remove the href from the modal buttons
            $('.ajax-modal-btn').removeAttr('href').css('cursor', 'pointer').show();

            // Initialise all plugins
            initAppPlugins();
            initDatatables();
            initMassActions();

            // Support for AJAX loaded modal window.
            $('body').on('click', '.ajax-modal-btn', function(e) {
                e.preventDefault();

                apply_busy_filter();

                var url = $(this).data('link');

                if (url.indexOf('#') == 0) {
                    $(url).modal('open');
                } else {
                    $.get(url, function(data) {
                            remove_busy_filter();

                            //Load modal data
                            $('#myDynamicModal').modal().html(data);

                            //Initialize application plugins after ajax load the content
                            if (typeof initAppPlugins == 'function') {
                                initAppPlugins();
                            }
                        })
                        .done(function() {
                            $('.modal-body input:text:visible:first').focus();
                        })
                        .fail(function(response) {
                            if (401 === response.status) {
                                window.location = "{{ route('login') }}";
                            } else {
                                console.log("{{ trans('responses.error') }}");
                            }
                        });
                }
            });

            // Confirmation for actions
            $('body').on('click', '.confirm', function(e) {
                e.preventDefault();

                var form = this.closest("form");
                var url = $(this).attr("href");
                var msg = $(this).data("confirm");
                if (!msg) {
                    msg = "{{ trans('app.are_you_sure') }}";
                }

                $.confirm({
                    title: "{{ trans('app.confirmation') }}",
                    content: msg,
                    type: 'red',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
                        'confirm': {
                            text: '{{ trans('app.proceed') }}',
                            keys: ['enter'],
                            btnClass: 'btn-red',
                            action: function() {
                                apply_busy_filter();

                                if (typeof url != 'undefined') {
                                    location.href = url;
                                } else if (form != null) {
                                    form.submit();
                                    notie.alert(4, "{{ trans('messages.confirmed') }}",
                                        3);
                                }

                                return true;
                            }
                        },
                        'cancel': {
                            text: '{{ trans('app.cancel') }}',
                            action: function() {
                                notie.alert(2, "{{ trans('messages.canceled') }}", 3);
                            }
                        },
                    }
                });
            });

            // Mark all Notifications As Read.
            $('#notifications-dropdown').on('click', function(e) {
                var url = "{{ route('admin.notifications.markAllAsRead') }}";

                $.get(url, function(data) {}).done(function() {
                    $('#notifications-dropdown').find('span.label').text('');
                });
            });

            @if (is_incevio_package_loaded('announcement'))
                // Update announcement read timestamp.
                $('#announcement-dropdown').on('click', function(e) {
                    var url = "{{ route('admin.setting.announcement.read') }}";

                    $.get(url, function(data) {}).done(function() {
                        $('#announcement-dropdown').find('span.label').text('');
                    });
                });
            @endif

        });
    }(window.jQuery, window, document));

    // Common datatable options for ajax loaded tables
    var dataTableOptions = {
        "aaSorting": [],
        "iDisplayLength": {{ getPaginationValue() }},
        "processing": true,
        "serverSide": true,
        "columns": [{
                'data': 'checkbox',
                'name': 'checkbox',
                'orderable': false,
                'searchable': false,
                'exportable': false,
                'printable': false
            },
            {
                'data': 'image',
                'name': 'image',
                'orderable': false,
                'searchable': false
            },
            {
                'data': 'sku',
                'name': 'sku'
            },
            {
                'data': 'title',
                'name': 'title'
            },
            {
                'data': 'condition',
                'name': 'condition',
                'orderable': false,
                'searchable': false
            },
            // {
            //   'data': 'sale_price',
            //   'name': 'sale_price',
            //   'searchable': false
            // },
            // {
            //   'data': 'base_price',
            //   'name': 'base_price',
            //   'searchable': false
            // },
            // {
            //   'data': 'quantity',
            //   'name': 'quantity',
            //   'searchable': false,
            //   'orderable': false
            // },
            {
                'data': 'option',
                'name': 'option',
                'orderable': false,
                'searchable': false,
                'exportable': false,
                'printable': false
            }
        ],
        "initComplete": function(settings, json) {
            // console.log(json);
        },
        "drawCallback": function(settings) {
            $(".massAction, .checkbox-toggle").unbind();
            $(".fa", '.checkbox-toggle').removeClass("fa-check-square-o").addClass('fa-square-o');
            initMassActions();
        },
        "oLanguage": {
            "sInfo": "_START_ to _END_ of _TOTAL_ entries",
            "sLengthMenu": "Show _MENU_",
            "sSearch": "",
            "sEmptyTable": "No data found!",
            "oPaginate": {
                "sNext": '<i class="fa fa-hand-o-right"></i>',
                "sPrevious": '<i class="fa fa-hand-o-left"></i>',
            },
        },
        "aoColumnDefs": [{
            "bSortable": false,
            "aTargets": [-1]
        }],
        "lengthMenu": [
            [10, 25, 50, -1],
            ['10 rows', '25 rows', '50 rows', 'Show all']
        ],
        "dom": 'Bfrtip',
        "buttons": [
            'pageLength', 'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    };

    // Push the expiry_date into the columns
    @if (is_incevio_package_loaded('pharmacy'))
        dataTableOptions.columns.splice(dataTableOptions.columns.length - 1, 0, {
            'data': 'expiry_date',
            'name': 'expiry_date'
        });
    @endif

    //dataTable Options for Stock Transfers
    var dataTableStockTransferOptions = {
        "aaSorting": [],
        "iDisplayLength": {{ getPaginationValue() }},
        "processing": true,
        "serverSide": true,
        scrollCollapse: true,
        scrollX: true,
        "columns": [{
                'data': 'checkbox',
                'name': 'checkbox',
                'orderable': false,
                'searchable': false,
                'exportable': false,
                'printable': false
            },
            {
                'data': 'image',
                'name': 'image',
                'orderable': false,
                'searchable': false
            },
            {
                'data': 'sku',
                'name': 'sku'
            },
            {
                'data': 'title',
                'name': 'title'
            },
            {
                'data': 'expired_date',
                'name': 'expired_date'
            },
            {
                'data': 'movement_number',
                'name': 'movement_number'
            },
            {
                'data': 'shop_from',
                'name': 'shop_from'
            },
            {
                'data': 'shop_to',
                'name': 'shop_to'
            },
            {
                'data': 'transfer_date',
                'name': 'transfer_date'
            },
            {
                'data': 'status',
                'name': 'status'
            },
            {
                'data': 'transfer_qty',
                'name': 'transfer_qty'
            },
            {
                'data': 'approve_by',
                'name': 'approve_by'
            },
            {
                'data': 'approve_date',
                'name': 'approve_date'
            },
            {
                'data': 'updated_by',
                'name': 'updated_by'
            },
            {
                'data': 'updated_date',
                'name': 'updated_date'
            },
            {
                'data': 'option',
                'name': 'option',
                'orderable': false,
                'searchable': false,
                'exportable': false,
                'printable': false
            }
        ],
        "initComplete": function(settings, json) {
            // console.log(json);
        },
        "drawCallback": function(settings) {
            $(".massAction, .checkbox-toggle").unbind();
            $(".fa", '.checkbox-toggle').removeClass("fa-check-square-o").addClass('fa-square-o');
            initMassActions();
        },
        "oLanguage": {
            "sInfo": "_START_ to _END_ of _TOTAL_ entries",
            "sLengthMenu": "Show _MENU_",
            "sSearch": "",
            "sEmptyTable": "No data found!",
            "oPaginate": {
                "sNext": '<i class="fa fa-hand-o-right"></i>',
                "sPrevious": '<i class="fa fa-hand-o-left"></i>',
            },
        },
        "aoColumnDefs": [{
            "bSortable": false,
            "aTargets": [-1]
        }],
        "lengthMenu": [
            [10, 25, 50, -1],
            ['10 rows', '25 rows', '50 rows', 'Show all']
        ],
        "dom": 'Bfrtip',
        "buttons": [
            'pageLength', 'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    };

    //DataTables
    function initDatatables() {
        // Load products
        $('#all-product-table').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.catalog.product.getMore') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'image',
                    'name': 'image',
                    'orderable': false,
                    'searchable': false
                },
                {
                    'data': 'name',
                    'name': 'name'
                },
                {
                    'data': 'general_name',
                    'name': 'general_name'
                },
                {
                    'data': 'licence_number',
                    'name': 'licence_number'
                },
                {
                    'data': 'selling_skuid',
                    'name': 'selling_skuid'
                },
                {
                    'data': 'purchase_price',
                    'name': 'purchase_price'
                },
                {
                    'data': 'min_price',
                    'name': 'min_price'
                },
                {
                    'data': 'max_price',
                    'name': 'max_price'
                },
                {
                    'data': 'inventories_count',
                    'name': 'inventories_count',
                    'searchable': false
                },
                // {
                //   'data': 'type',
                //   'name': 'type',
                //   'orderable': false,
                //   'searchable': false
                // },
                // {
                //   'data': 'gtin',
                //   'name': 'gtin'
                // },
                // {
                //   'data': 'category',
                //   'name': 'category',
                //   'orderable': false,
                //   'searchable': false
                // },
                // {
                //   'data': 'added_by',
                //   'name': 'added_by',
                //   'searchable': false,
                //   'orderable': false
                // },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        let inventoryTblColumns = [{
                'data': 'checkbox',
                'name': 'checkbox',
                'orderable': false,
                'searchable': false,
                'exportable': false,
                'printable': false
            },
            {
                'data': 'image',
                'name': 'image',
                'orderable': false,
                'searchable': false
            },
            {
                'data': 'sku',
                'name': 'sku'
            },
            {
                'data': 'title',
                'name': 'title'
            },
            {
                'data': 'price',
                'name': 'price',
                'searchable': false
            },
            {
                'data': 'option',
                'name': 'option',
                'orderable': false,
                'searchable': false,
                'exportable': false,
                'printable': false
            }
        ];

        @if (config('system_settings.show_item_conditions'))
            // Insert the condition in 5th column
            inventoryTblColumns.splice(4, 0, {
                'data': 'condition',
                'name': 'condition',
                'orderable': false,
                'searchable': false
            });
        @endif

        @if (request()->is('*/inventory/digital*') || request()->is('*/stock/product/digital*'))
            // Insert the download_limit in 5th column
            dataTableOptions.columns.splice(dataTableOptions.columns.length - 1, 0, {
                'data': 'download_limit',
                'name': 'download_limit',
                'orderable': false,
                'searchable': false
            });
        @else
            // Insert the quantity in 5th column
            dataTableOptions.columns.splice(dataTableOptions.columns.length - 1, 0, {
                'data': 'quantity',
                'name': 'quantity',
                'orderable': false,
                'searchable': false
            });
        @endif

        @if (request()->is('*/inventory/auction*')) // Insert the auction base_price in 6th column
            dataTableOptions.columns.splice(dataTableOptions.columns.length - 3, 0, {
                'data': 'bids_count',
                'name': 'bids_count',
                'orderable': false,
                'searchable': false
            });

            dataTableOptions.columns.splice(dataTableOptions.columns.length - 2, 0, {
                'data': 'base_price',
                'name': 'base_price',
                'orderable': false,
                'searchable': false
            });
        @else // Insert the price in 6th column
            dataTableOptions.columns.splice(dataTableOptions.columns.length - 2, 0, {
                'data': 'sale_price',
                'name': 'sale_price',
                'orderable': false,
                'searchable': false
            });
        @endif

        // Load digital inventoris
        @if (request()->is('*/inventory/digital*') || request()->is('*/stock/product/digital*'))
            $('#active_inventory').DataTable($.extend({}, dataTableOptions, {
                "ajax": "{{ route('admin.stock.inventory.getMore', ['status' => 'active', 'type' => 'digital']) }}"
            }));

            $('#inactive_inventory').DataTable($.extend({}, dataTableOptions, {
                "ajax": "{{ route('admin.stock.inventory.getMore', ['status' => 'inactive', 'type' => 'digital']) }}"
            }));
        @elseif (request()->is('*/inventory/auction') || request()->is('*/stock/product/auction*'))
            $('#active_inventory').DataTable($.extend({}, dataTableOptions, {
                "ajax": "{{ route('admin.stock.inventory.getMore', ['status' => 'active', 'type' => 'auction']) }}"
            }));

            $('#inactive_inventory').DataTable($.extend({}, dataTableOptions, {
                "ajax": "{{ route('admin.stock.inventory.getMore', ['status' => 'inactive', 'type' => 'auction']) }}"
            }));

            $('#outOfStock_inventory').DataTable($.extend({}, dataTableOptions, {
                "ajax": "{{ route('admin.stock.inventory.getMore', ['status' => 'outOfStock', 'type' => 'auction']) }}"
            }));
        @else
            // Load physical inventoris
            $('#active_inventory').DataTable($.extend({}, dataTableOptions, {
                "ajax": "{{ route('admin.stock.inventory.getMore', ['status' => 'active', 'type' => 'physical']) }}"
            }));

            $('#inactive_inventory').DataTable($.extend({}, dataTableOptions, {
                "ajax": "{{ route('admin.stock.inventory.getMore', ['status' => 'inactive', 'type' => 'physical']) }}"
            }));

            $('#outOfStock_inventory').DataTable($.extend({}, dataTableOptions, {
                "ajax": "{{ route('admin.stock.inventory.getMore', ['status' => 'outOfStock', 'type' => 'physical']) }}"
            }));

            $('#stockTransfer').DataTable($.extend({}, dataTableStockTransferOptions, {
                "ajax": "{{ route('admin.stock.inventory.getMore', ['status' => 'stockTransfer', 'type' => 'physical']) }}"
            }));
        @endif

        // function handleFilterChange() {
        //     var orderStatusFilter = $('#filter-all-order-table-order-status').val();
        //     var paymentStatusFilter = $('#filter-all-order-table-payment-status').val();

        //     // Use the filter values to construct url to filter data table
        //     var filteredUrl =
        //         "{{ route('admin.order.bulkorder_process', ['paymentStatus' => '0', 'orderStatus' => '0']) }}";
        //     filteredUrl = filteredUrl.replace(/\/[^/]+\/[^/]+$/, `/${paymentStatusFilter}` +
        //         `/${orderStatusFilter}`); // replaces the last two parameters of the url

        //     // Reload the data table with the new url
        //     $('#all-order-table').DataTable().ajax.url(filteredUrl).load();
        //     // $('#all-order-table-full').DataTable().ajax.url(filteredUrl).load();
        // }

        // // On changin filter trigger routes with appropriate filter values
        // $('#filter-all-order-table-order-status, #filter-all-order-table-payment-status').on('change',
        //     handleFilterChange);

        // PAYROLL TABLE
        $('#payroll-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.payroll.getPayrolls') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'position',
                    'name': 'position'
                },
                {
                    'data': 'grade',
                    'name': 'grade'
                },
                {
                    'data': 'sub_grade',
                    'name': 'sub_grade'
                },
                {
                    'data': 'level',
                    'name': 'level'
                },
                {
                    'data': 'take_home_pay',
                    'name': 'take_home_pay'
                },
                {
                    'data': 'basic_sallary',
                    'name': 'basic_sallary'
                },
                {
                    'data': 'operational_allowance',
                    'name': 'operational_allowance'
                },
                {
                    'data': 'position_allowance',
                    'name': 'position_allowance'
                },
                {
                    'data': 'child_education_allowance',
                    'name': 'child_education_allowance'
                },
                {
                    'data': 'transportation',
                    'name': 'transportation'
                },
                {
                    'data': 'quota',
                    'name': 'quota'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at'
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at'
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by'
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));
        // END PAYROLL TABLE

        // DEPARTMENT TABLE
        $('#department-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.department.getDepartments') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'name',
                    'name': 'name'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at'
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at'
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by'
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));
        // END DEPARTMENT TABLE

        // OVERTIME TABLE
        const overtimeTable = $('#overtime-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.overtime.getOvertimes') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'user_id',
                    'name': 'user_id'
                },
                {
                    'data': 'start_time',
                    'name': 'start_time'
                },
                {
                    'data': 'end_time',
                    'name': 'end_time'
                },
                {
                    'data': 'spend_time',
                    'name': 'spend_time'
                },
                {
                    'data': 'status',
                    'name': 'status'
                },
                {
                    'data': 'approved_by',
                    'name': 'approved_by'
                },
                {
                    'data': 'approved_at',
                    'name': 'approved_at'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at'
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at'
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by'
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        @if (!Auth::user()->isAdmin())
            overtimeTable.column('created_by:name').search('{{ Auth::user()->name }}').draw();
        @endif

        // END OVERTIME TABLE

        // ABSENCE TABLE
        const absenceTable = $('#absence-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.absence.getAbsences') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'user_id',
                    'name': 'user_id'
                },
                {
                    'data': 'clock_in',
                    'name': 'clock_in'
                },
                {
                    'data': 'clock_in_img',
                    'name': 'clock_in_img'
                },
                {
                    'data': 'clock_out',
                    'name': 'clock_out'
                },
                {
                    'data': 'clock_out_img',
                    'name': 'clock_out_img'
                },
                {
                    'data': 'branch_loc',
                    'name': 'branch_loc'
                },
                {
                    'data': 'address',
                    'name': 'address'
                },
                {
                    'data': 'total_hours',
                    'name': 'total_hours'
                },
            ],
            order: [[2, 'desc']]
        }));

        @if (!Auth::user()->isAdmin())
            absenceTable.column('user_id:name').search('{{ Auth::user()->name }}').draw();
        @endif

        // END ABSENCE TABLE

        // LOAN TABLE
        var loanTable = $('#loan-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.loan.getLoans') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'id',
                    'name': 'id'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by'
                },
                {
                    'data': 'status',
                    'name': 'status'
                },
                {
                    'data': 'amount',
                    'name': 'amount'
                },
                {
                    'data': 'reason',
                    'name': 'reason'
                },
                {
                    'data': 'approved_by',
                    'name': 'approved_by'
                },
                {
                    'data': 'approved_at',
                    'name': 'approved_at'
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by'
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at'
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        @if (!Auth::user()->isAdmin())
            loanTable.column('created_by:name').search('{{ Auth::user()->name }}').draw();
        @endif
        // END LOAN TABLE

        // LOAN REPORT TABLE
        var loanReportTable = $('#loan-report-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.loan.getDataLoanReportFirst') }}",
            "columns": [{

                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: ''
                },
                {
                    'data': 'created_by',
                    'name': 'created_by',
                    visible: false
                },
                {
                    'data': 'name',
                    'name': 'name'
                },
                {
                    'data': 'sum_amount_loan',
                    'name': 'sum_amount_loan'
                },
                {
                    'data': 'sum_amount_loan_payment',
                    'name': 'sum_amount_loan_payment'
                },
                {
                    'data': 'total_outstanding_balance',
                    'name': 'total_outstanding_balance'
                },
                {
                    'data': 'status',
                    'name': 'status'
                }
            ]
        }));

        // Filter functions
        // function filterByMonthTarget() {
        //     var selectedMonth = $('#monthFilterTarget').val();
        //     loanReportTable.column('month:name').search(selectedMonth).draw();
        // }

        // function filterByWarehouseTarget() {
        //     var selectedMerchant = $('#merchantFilterTarget').val();
        //     tableTargetsReportAdministrator.column('warehouse:name').search(selectedMerchant).draw();
        // }

        // function filterByYearTarget() {
        //     var selectedMerchant = $('#yearFilterTarget').val();
        //     tableTargetsReportAdministrator.column('year:name').search(selectedMerchant).draw();
        // }

        // // Bind filter functions to the change event of filter dropdowns
        // $('#monthFilterLoan').on('change', filterByMonthLoan);
        // $('#merchantFilterLoan').on('change', filterByWarehouseLoan);
        // $('#yearFilterLoan').on('change', filterByYearLoan);


        let additionalDataLoanReportSecond = [];
        $.ajax({
            url: "{{ route('admin.admin.loan.getDataLoanReportSecond') }}",
            method: 'GET',
            success: function(data) {
                additionalDataLoanReportSecond = data.data;
            },
            error: function(xhr, status, error) {
                console.error('Error fetching additionalDataLoanReportSecond:', error);
            }
        });

        // First-level row formatting function
        function formatFirstLevelLoan(dataItem) {
            console.log(dataItem, 'dataItem')
            console.log(additionalDataLoanReportSecond, 'additionalDataLoanReportSecond')
            let relatedDataLoanReportSecond = additionalDataLoanReportSecond.filter(item =>
                item.name == dataItem.user_name && item.user_id == dataItem.created_by_id);

            let formattedDataLoanReportFirst = `
                <div class="table-responsive" >
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>{{ trans('app.form.created_at') }}</th>
                                <th>{{ trans('app.form.total_loan') }}</th>
                                <th>{{ trans('app.form.amount') }}</th>
                                <th>{{ trans('app.form.outstanding_balance') }}</th>
                                <th>{{ trans('app.form.paid_by') }}</th>
                                <th>{{ trans('app.form.updated_by') }}</th>
                                <th>{{ trans('app.form.updated_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            });

            relatedDataLoanReportSecond.forEach(function(row, index) {
                formattedDataLoanReportFirst += `
                    <tr style="${ row.outstanding_balance == 0 ? 'background: cadetblue; color: white;' : '' }">
                        <td>${row.created_at}</td>
                        <td>${formatter.format(row.total_loan)}</td>
                        <td>${formatter.format(row.amount)}</td>
                        <td>${formatter.format(row.outstanding_balance)}</td>
                        <td>${row.created_by_name}</td>
                        <td>${row.updated_by_name ?? ''}</td>
                        <td>${row.updated_at ?? ''}</td>
                    </tr>
                `;
            });

            formattedDataLoanReportFirst += `
                    </tbody>
                </table>
            </div>
        `;

            return formattedDataLoanReportFirst;
        }

        // Handle first-level row expansion
        $('#loan-report-tables tbody').on('click', 'td.dt-control', function(e) {
            let tr = e.target.closest('tr');
            let row = loanReportTable.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                $(tr).removeClass('dt-hasChild');
            } else {
                console.log(row.data(), 'row.data()')
                row.child(formatFirstLevelLoan(row.data())).show();
                $(tr).addClass('dt-hasChild');
            }
        });

        // END LOAN REPORT TABLE

        // LOAN PAYMENT TABLE
        $('#loan-payment-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.loan.getLoanPayments') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'user_id',
                    'name': 'user_id'
                },
                {
                    'data': 'total_loan',
                    'name': 'total_loan'
                },
                {
                    'data': 'amount',
                    'name': 'amount'
                },
                {
                    'data': 'outstanding_balance',
                    'name': 'outstanding_balance'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by'
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by'
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at'
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));
        // END LOAN TABLE

        // TIMEOFF TABLE
        const timeoffTable = $('#timeoff-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.timeoff.getTimeOff') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'created_at',
                    'name': 'created_at'
                },
                {
                    'data': 'warehouse_id',
                    'name': 'warehouse_id'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by'
                },
                {
                    'data': 'month',
                    'name': 'month'
                },
                {
                    'data': 'year',
                    'name': 'year'
                },
                {
                    'data': 'start_date',
                    'name': 'start_date'
                },
                {
                    'data': 'end_date',
                    'name': 'end_date'
                },
                {
                    'data': 'total_days',
                    'name': 'total_days'
                },
                {
                    'data': 'category',
                    'name': 'category'
                },
                {
                    'data': 'type',
                    'name': 'type'
                },
                {
                    'data': 'notes',
                    'name': 'notes'
                },
                {
                    'data': 'status',
                    'name': 'status'
                },
                {
                    'data': 'picture',
                    'name': 'picture'
                },
                {
                    'data': 'approved_by',
                    'name': 'approved_by'
                },
                {
                    'data': 'approved_at',
                    'name': 'approved_at'
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by'
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at'
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));


        @if(!Auth::user()->isAdmin())
            timeoffTable.column('created_by:name').search('{{ Auth::user()->name }}').draw();
        @endif

        function filterByMonthTimeoff() {
            var selectedMonth = $('#monthFilterTimeoff').val()
            // Apply the month filter to the 'month' column
            timeoffTable.column('month:name').search(selectedMonth).draw();
        }

        function filterByYearTimeoff() {
            var selectedYear = $('#yearFilterTimeoff').val();

            // Apply the year filter to the 'year' column (assume the column name is 'year')
            timeoffTable.column('year:name').search(selectedYear).draw();
        }
        
        function filterByStatusTimeoff() {
            var selectedStatus = $('#statusFilterTimeoff').val();

            // Apply the status filter to the 'Status' column (assume the column name is 'Status')
            timeoffTable.column('status:name').search(selectedStatus).draw();
        }

        function filterByMerchantTimeoff() {
            var selectedMerchant = $('#merchantFilterTimeoff').val();

            // Apply the status filter to the 'Status' column (assume the column name is 'Status')
            timeoffTable.column('warehouse_id:name').search(selectedMerchant).draw();
        }

        // Bind the filter and calculation function to the month dropdown change event
        $('#monthFilterTimeoff').on('change', filterByMonthTimeoff);
        $('#yearFilterTimeoff').on('change', filterByYearTimeoff);
        $('#statusFilterTimeoff').on('change', filterByStatusTimeoff);
        $('#merchantFilterTimeoff').on('change', filterByMerchantTimeoff);
        // END TIMEOFF TABLE

        // PAYROLL REPORT TABLE
        $('#payroll-report-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.payroll.getReportPayroll') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'month_name',
                    'name': 'month_name'
                },
                {
                    'data': 'year',
                    'name': 'year'
                },
                {
                    'data': 'full_name',
                    'name': 'full_name'
                },
                {
                    'data': 'position',
                    'name': 'position'
                },
                {
                    'data': 'organization',
                    'name': 'organization'
                },
                {
                    'data': 'basic_sallary',
                    'name': 'basic_sallary'
                },
                {
                    'data': 'position_allowance',
                    'name': 'position_allowance'
                },
                {
                    'data': 'transportation',
                    'name': 'transportation'
                },
                {
                    'data': 'uang_oprational_harian',
                    'name': 'uang_oprational_harian'
                },
                {
                    'data': 'child_education_allowance',
                    'name': 'child_education_allowance'
                },
                {
                    'data': 'bonus_penjualan',
                    'name': 'bonus_penjualan'
                },
                {
                    'data': 'bonus',
                    'name': 'bonus'
                },
                {
                    'data': 'overtime',
                    'name': 'overtime'
                },
                {
                    'data': 'reimburse_etoll_bensin',
                    'name': 'reimburse_etoll_bensin'
                },
                {
                    'data': 'reimburse_pengobatan_sakit',
                    'name': 'reimburse_pengobatan_sakit'
                },
                {
                    'data': 'total_allowance',
                    'name': 'total_allowance'
                },
                {
                    'data': 'total_allowance',
                    'name': 'total_allowance'
                },
                {
                    'data': 'potongan_keterlambatan',
                    'name': 'potongan_keterlambatan'
                },
                {
                    'data': 'potongan_alpha',
                    'name': 'potongan_alpha'
                },
                {
                    'data': 'potongan_absensi',
                    'name': 'potongan_absensi'
                },
                {
                    'data': 'pinjaman',
                    'name': 'pinjaman'
                },
                {
                    'data': 'cicilan',
                    'name': 'cicilan'
                },
                {
                    'data': 'jaminan_pensiun_employee',
                    'name': 'jaminan_pensiun_employee'
                },
                {
                    'data': 'JHT_employee',
                    'name': 'JHT_employee'
                },
                {
                    'data': 'PPH21',
                    'name': 'PPH21'
                },
                {
                    'data': 'total_deduction',
                    'name': 'total_deduction'
                },
                {
                    'data': 'PPH21_payment',
                    'name': 'PPH21_payment'
                },
                {
                    'data': 'take_home_pay',
                    'name': 'take_home_pay'
                },
                {
                    'data': 'telekomunikasi',
                    'name': 'telekomunikasi'
                }
            ]
        }));
        // END PAYROLL REPORT TABLE

        // ORDER TABLE
        var orderTableData = $('#all-order-table').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.order.bulkorder_process', ['paymentStatus' => '0', 'orderStatus' => '0']) }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'po_number_ref',
                    'name': 'po_number_ref'
                },
                {
                    'data': 'order_date',
                    'name': 'order_date',
                    'type': 'date'
                },
                {
                    'data': 'due_date_payment',
                    'name': 'due_date_payment'
                },
                {
                    'data': 'shop_id',
                    'name': 'shop_id'
                },
                {
                    'data': 'customer_name',
                    'name': 'customer_name',
                },
                {
                    'data': 'grand_total',
                    'name': 'grand_total',
                },
                {
                    'data': 'payment_status_name',
                    'name': 'payment_status_name',
                },
                {
                    'data': 'payment_status_id',
                    'name': 'payment_status_id',
                    visible: false
                },
                {
                    'data': 'order_status',
                    'name': 'order_status',
                },
                {
                    'data': 'order_status_id',
                    'name': 'order_status_id',
                    visible: false
                },
                {
                    'data': 'partial_status',
                    'name': 'partial_status',
                    'searchable': false
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ],
            order: [[2, 'desc']] // Sort by the 3rd column (index 2) in ascending order
        }));

         // if isFromPlatform
         @if (!Auth::user()->isAdmin())
         orderTableData.column('shop_id:name').search('{{ Auth::user()->shop_id }}').draw();
         @endif

        function filterByWarehouseOrderTbl() {
            var selectedMerchant = $('#merchantOrderTableFilter').val();

            orderTableData.column('shop_id:name').search(selectedMerchant).draw();
        }

        function filterByCustomerOrderTbl() {
            var selectedCustomer = $('#customerOrderTableFilter').val();

            orderTableData.column('customer_name:name').search(selectedCustomer).draw();
        }

        function filterByStatusOrderTbl() {
            var selectedStatus = $('#statusOrderTableFilter').val();

            orderTableData.column('order_status_id:name').search(selectedStatus).draw();
        }

        function filterByPaymentStatusOrderTbl() {
            var selectedPaymentStatus = $('#paymentStatusOrderTableFilter').val();

            orderTableData.column('payment_status_id:name').search(selectedPaymentStatus).draw();
        }

        function filterByDateRangeOrderTbl() {
            var startDate = $('#startDateOrderTableFilter').val();
            var endDate = $('#endDateOrderTableFilter').val();
            console.log(startDate, endDate);

            // If both dates are selected, filter by range
            if (startDate && endDate) {
                orderTableData.column('order_date:name')
                    .search(startDate + '|' + endDate, true, false)
                    .draw();
            }
        }

        $('#dateRangeOrderTableFilterButton').on('click', filterByDateRangeOrderTbl);
        $('#merchantOrderTableFilter').on('change', filterByWarehouseOrderTbl);
        $('#customerOrderTableFilter').on('change', filterByCustomerOrderTbl);
        $('#statusOrderTableFilter').on('change', filterByStatusOrderTbl);
        $('#paymentStatusOrderTableFilter').on('change', filterByPaymentStatusOrderTbl);
        // END ORDER TABLE

        // Load Order Report list by Ajax
        var tableOrderReport = $('#all-order-table-full').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.order.getOrderReport') }}",
            fixedColumns: {
                leftColumns: 2 // Number of columns you want to fix on the left
            },
            paging: true,
            scrollCollapse: true,
            // scrollY: '400px',
            scrollX: true,
            "columns": [{
                    'data': 'order_number',
                    'name': 'order_number'
                },
                {
                    'data': 'po_number_ref',
                    'name': 'po_number_ref'
                },
                {
                    'data': 'warehouse_name',
                    'name': 'warehouse_name'
                },
                {
                    'data': 'shop_id',
                    'name': 'shop_id',
                    visible: false
                },
                {
                    'data': 'client_name',
                    'name': 'client_name',
                },
                {
                    'data': 'selling_skuid',
                    'name': 'selling_skuid',
                },
                {
                    'data': 'product_name',
                    'name': 'product_name',
                },
                {
                    'data': 'quantity',
                    'name': 'quantity',
                },
                {
                    'data': 'unit_price',
                    'name': 'unit_price',
                },
                {
                    'data': 'purchase_price',
                    'name': 'purchase_price',
                },
                {
                    'data': 'total',
                    'name': 'total',
                },
                {
                    'data': 'discount',
                    'name': 'discount',
                },
                {
                    'data': 'taxrate',
                    'name': 'taxrate',
                },
                {
                    'data': 'Grand_Total',
                    'name': 'Grand_Total',
                },
                {
                    'data': 'created_at',
                    'name': 'created_at',
                    'type': 'date'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by',
                },
                {
                    'data': 'packed_date',
                    'name': 'packed_date'
                },
                {
                    'data': 'packed_by',
                    'name': 'packed_by'
                },
                {
                    'data': 'shipping_date',
                    'name': 'shipping_date'
                },
                {
                    'data': 'shipped_by',
                    'name': 'shipped_by'
                },
                {
                    'data': 'delivery_date',
                    'name': 'delivery_date',
                },
                {
                    'data': 'delivered_by',
                    'name': 'delivered_by'
                },
                {
                    'data': 'paid_date',
                    'name': 'paid_date'
                },
                {
                    'data': 'paid_by',
                    'name': 'paid_by'
                },

                {
                    'data': 'SLA_Order',
                    'name': 'SLA_Order',
                },
                {
                    'data': 'SLA_Packing',
                    'name': 'SLA_Packing',
                },
                {
                    'data': 'SLA_Delivery',
                    'name': 'SLA_Delivery',
                },
                {
                    'data': 'SLA_Payment',
                    'name': 'SLA_Payment',
                },

                {
                    'data': 'due_date_in_days',
                    'name': 'due_date_in_days'
                },
                {
                    'data': 'due_date',
                    'name': 'due_date'
                },
                {
                    'data': 'cancel_date',
                    'name': 'cancel_date'
                },
                {
                    'data': 'cancel_by',
                    'name': 'cancel_by'
                },
                {
                    'data': 'payment_status_name',
                    'name': 'payment_status_name',
                },
                {
                    'data': 'payment_status_id',
                    'name': 'payment_status_id',
                    visible: false
                },
                {
                    'data': 'order_status_name',
                    'name': 'order_status_name',
                },
                {
                    'data': 'order_status_id',
                    'name': 'order_status_id',
                    visible: false
                },
            ],
            order: [[14, 'desc']] // Sort by the 3rd column (index 2) in ascending order
        }));

        // if isFromPlatform
        @if (!Auth::user()->isFromPlatform())
            tableOrderReport.column('shop_id:name').search('{{ Auth::user()->shop_id }}').draw();
        @endif

        function filterByWarehouseOrderTable() {
            var selectedMerchant = $('#merchantOrderReportFilter').val();

            tableOrderReport.column('warehouse_name:name').search(selectedMerchant).draw();
        }

        function filterByCustomerOrderTable() {
            var selectedCustomer = $('#customerOrderReportFilter').val();

            tableOrderReport.column('client_name:name').search(selectedCustomer).draw();
        }

        function filterByStatusOrderTable() {
            var selectedStatus = $('#statusOrderReportFilter').val();

            tableOrderReport.column('order_status_id:name').search(selectedStatus).draw();
        }

        function filterByPaymentStatusOrderTable() {
            var selectedPaymentStatus = $('#paymentStatusOrderReportFilter').val();

            tableOrderReport.column('payment_status_id:name').search(selectedPaymentStatus).draw();
        }

        function filterByDateRangeOrderTable() {
            var startDate = $('#startDateOrderReportFilter').val();
            var endDate = $('#endDateOrderReportFilter').val();
            console.log(startDate, endDate);

            // If both dates are selected, filter by range
            if (startDate && endDate) {
                tableOrderReport.column('created_at:name')
                    .search(startDate + '|' + endDate, true, false)
                    .draw();
            }
        }

        $('#dateRangeOrderReportFilterButton').on('click', filterByDateRangeOrderTable);
        $('#merchantOrderReportFilter').on('change', filterByWarehouseOrderTable);
        $('#customerOrderReportFilter').on('change', filterByCustomerOrderTable);
        $('#statusOrderReportFilter').on('change', filterByStatusOrderTable);
        $('#paymentStatusOrderReportFilter').on('change', filterByPaymentStatusOrderTable);
        // END ORDER REPORT

        // ORDER PAYMENT DOCUMENT REPORT
        var tableOrdePaymentDocReport = $('#payment-doc-table').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.order.getOrderPaymentDocReport') }}",
            scrollCollapse: true,
            scrollX: true,
            "columns": [{
                    'data': 'created_at',
                    'name': 'created_at',
                },
                {
                    'data': 'order_number',
                    'name': 'order_number'
                },
                {
                    'data': 'po_number_ref',
                    'name': 'po_number_ref'
                },
                {
                    'data': 'shop_id',
                    'name': 'shop_id',
                },
                {
                    'data': 'customer_id',
                    'name': 'customer_id',
                },
                {
                    'data': 'doc_SI',
                    'name': 'doc_SI',
                },
                {
                    'data': 'paid_date',
                    'name': 'paid_date',
                },
                {
                    'data': 'doc_faktur_pajak',
                    'name': 'doc_faktur_pajak',
                },
                {
                    'data': 'doc_faktur_pajak_uploaded_at',
                    'name': 'doc_faktur_pajak_uploaded_at',
                },
                {
                    'data': 'sla_faktur_pajak',
                    'name': 'sla_faktur_pajak',
                },
                {
                    'data': 'doc_faktur_pajak_terbayar',
                    'name': 'doc_faktur_pajak_terbayar',
                },
                {
                    'data': 'doc_faktur_pajak_terbayar_uploaded_at',
                    'name': 'doc_faktur_pajak_terbayar_uploaded_at',
                },
                {
                    'data': 'sla_faktur_pajak_terbayar',
                    'name': 'sla_faktur_pajak_terbayar',
                },
                {
                    'data': 'payment_status',
                    'name': 'payment_status',
                },
                {
                    'data': 'order_status_id',
                    'name': 'order_status_id',
                },
                {
                    'data': 'options',
                    'name': 'options',
                },

            ]
        }));

        // if isFromPlatform
        @if (!Auth::user()->isAdmin())
            tableOrdePaymentDocReport.column('shop_id:name').search('{{ Auth::user()->shop_id }}').draw();
        @endif

        function filterByWarehouseOrderReport() {
            var selectedMerchant = $('#merchantOrderPaymentDocReportFilter').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableOrdePaymentDocReport.column('shop_id:name').search(selectedMerchant).draw();
        }

        function filterByCustomerOrderReport() {
            var selectedCustomer = $('#customerOrderPaymentDocReportFilter').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableOrdePaymentDocReport.column('customer_id:name').search(selectedCustomer).draw();
        }

        function filterByStatusOrderReport() {
            var selectedStatus = $('#statusOrderPaymentDocReportFilter').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableOrdePaymentDocReport.column('order_status_id:name').search(selectedStatus).draw();
        }

        function filterByPaymentStatusOrderReport() {
            var selectedPaymentStatus = $('#paymentStatusOrderPaymentDocReportFilter').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableOrdePaymentDocReport.column('payment_status:name').search(selectedPaymentStatus).draw();
        }

        $('#merchantOrderPaymentDocReportFilter').on('change', filterByWarehouseOrderReport);
        $('#customerOrderPaymentDocReportFilter').on('change', filterByCustomerOrderReport);
        $('#statusOrderPaymentDocReportFilter').on('change', filterByStatusOrderReport);
        $('#paymentStatusOrderPaymentDocReportFilter').on('change', filterByPaymentStatusOrderReport);

        // END ORDER PAYMENT DOCUMENT REPORT

        // Load customer list by Ajax
        $('#all-customer-table').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.customer.getMore') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'image',
                    'name': 'image',
                    'orderable': false,
                    'searchable': false
                },
                {
                    'data': 'name',
                    'name': 'name'
                },
                {
                    'data': 'email',
                    'name': 'email'
                },
                {
                    'data': 'coverage_area',
                    'name': 'coverage_area'
                },
                {
                    'data': 'orders_count',
                    'name': 'orders_count',
                    'searchable': false
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        // Load offering list by Ajax
        var tableOffering = $('#offering-table').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.offering.getOfferings') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'product',
                    'name': 'product'

                },
                {
                    'data': 'small_quantity_price',
                    'name': 'small_quantity_price'
                },
                {
                    'data': 'medium_quantity_price',
                    'name': 'medium_quantity_price'
                },
                {
                    'data': 'large_quantity_price',
                    'name': 'large_quantity_price'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at',
                },
                {
                    'data': 'created_by',
                    'name': 'created_by',
                },
                {
                    'data': 'company_name',
                    'name': 'company_name',
                },
                {
                    'data': 'email',
                    'name': 'email',
                },
                {
                    'data': 'phone',
                    'name': 'phone',
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at',
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by',
                },
                {
                    'data': 'status',
                    'name': 'status',
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        // Filter the 'created_by' column with the name of the authenticated user
        @if (!Auth::user()->isAdmin() && !Auth::user()->isMerchant())
            tableOffering.column('created_by:name').search('{{ Auth::user()->name }}').draw();
        @endif

        // Filter by product name
        $('#productFilter').on('change', function() {
            var selectedProduct = $(this).val();
            tableOffering.column('product:name').search(selectedProduct).draw();
        });

        // TARGET DATA
        // Load offering list by Ajax
        var tableTargets = $('#target-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.target.getTargetsTables') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'month',
                    'name': 'month'

                },
                {
                    'data': 'year',
                    'name': 'year'

                },
                {
                    'data': 'hospital_name',
                    'name': 'hospital_name'
                },
                {
                    'data': 'actual_sales',
                    'name': 'actual_sales'
                },
                {
                    'data': 'grand_total',
                    'name': 'grand_total'
                },
                {
                    'data': 'warehouse',
                    'name': 'warehouse'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at',
                },
                {
                    'data': 'created_by',
                    'name': 'created_by',
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at',
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by',
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        // Filter the 'created_by' column with the name of the authenticated user
        @if (!Auth::user()->isAdmin())
            tableTargets.column('created_by:name').search('{{ Auth::user()->name }}').draw();
        @endif

        // Function to calculate the total amount
        function calculateTotalTarget() {
            var total = 0;
            tableTargets.rows({
                search: 'applied'
            }).every(function(rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                var amount = data.grand_total.replace(/[^\d]/g, ''); // Remove non-numeric characters
                total += parseFloat(amount); // Assuming the 'Amount' column is at index 1
            });

            $('#totalAmountTarget').html('Rp. ' + total.toLocaleString('id-ID'));
        }

        function filterByMonthTarget() {
            var selectedMonth = $('#monthFilterTarget').val();

            // Apply the month filter to the 'month' column (assume the column name is 'month')
            tableTargets.column('month:name').search(selectedMonth).draw();
        }

        function filterByWarehouseTarget() {
            var selectedMerchant = $('#merchantFilterTarget').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableTargets.column('warehouse:name').search(selectedMerchant).draw();
        }

        function filterByYearTarget() {
            var selectedMerchant = $('#yearFilterTarget').val();

            // Apply the year filter to the 'year' column (assume the column name is 'year')
            tableTargets.column('year:name').search(selectedMerchant).draw();
        }

        // Initial calculation
        calculateTotalTarget();

        // Bind the filter and calculation function to the month dropdown change event
        $('#monthFilterTarget').on('change', filterByMonthTarget);
        $('#merchantFilterTarget').on('change', filterByWarehouseTarget);
        $('#yearFilterTarget').on('change', filterByYearTarget);

        // Recalculate the total on each table draw
        tableTargets.on('draw', function() {
            calculateTotalTarget();
        });
        // END TARGET DATA

        // TARGET REPORT
        // Load offering list by Ajax
        var tableTargetsReport = $('#target-tables-report').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.target.getTargetsTablesReport') }}",
            "columns": [{
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: ''
                },
                {
                    'data': 'month',
                    'name': 'month'

                },
                {
                    'data': 'year',
                    'name': 'year'

                },
                {
                    'data': 'warehouse',
                    'name': 'warehouse'
                },
                {
                    'data': 'total_target',
                    'name': 'total_target'
                },
                {
                    'data': 'total_selling',
                    'name': 'total_selling'
                },
                {
                    'data': 'rate',
                    'name': 'rate'
                },
                {
                    'data': 'status',
                    'name': 'status'
                },
                {
                    'data': 'shop_id',
                    'name': 'shop_id',
                    visible: false
                },
            ]
        }));

        // filter by warehouse, marketing, and leader
        @if (Auth::user()->role_id === 3 || Auth::user()->role_id === 8 || Auth::user()->role_id === 13)
            tableTargetsReport.column('shop_id:name').search('{{ Auth::user()->shop_id }}').draw();
        @endif

        function filterByMonthTarget() {
            var selectedMonth = $('#monthFilterTarget').val();

            // Apply the month filter to the 'month' column (assume the column name is 'month')
            tableTargetsReport.column('month:name').search(selectedMonth).draw();
        }

        function filterByWarehouseTarget() {
            var selectedMerchant = $('#merchantFilterTarget').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableTargetsReport.column('warehouse:name').search(selectedMerchant).draw();
        }

        function filterByYearTarget() {
            var selectedMerchant = $('#yearFilterTarget').val();

            // Apply the year filter to the 'year' column (assume the column name is 'year')
            tableTargetsReport.column('year:name').search(selectedMerchant).draw();
        }

        // Bind the filter and calculation function to the month dropdown change event
        $('#monthFilterTarget').on('change', filterByMonthTarget);
        $('#merchantFilterTarget').on('change', filterByWarehouseTarget);
        $('#yearFilterTarget').on('change', filterByYearTarget);

        // Fetch additional data via AJAX
        let additionalData = [];
        $.ajax({
            url: "{{ route('admin.admin.target.getTargetsTablesExpand') }}",
            method: 'GET',
            success: function(data) {
                additionalData = data.data;
            },
            error: function(xhr, status, error) {
                console.error('Error fetching additional data:', error);
            }
        });

        function format(dataItem) {
            // Filter the additional data for the relevant shop_id
            // console.log(dataItem, 'dataItem')
            // console.log(additionalData, 'additionalData')
            let relatedData = additionalData.filter(item => item.shop_id == dataItem.shop_id &&
                item.month == dataItem.month && item.year == dataItem.year);

            // Build the table HTML
            let formattedData = `
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Warehouse</th>
                        <th>Client</th>
                        <th>Total Target</th>
                        <th>Total Selling</th>
                        <th>Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
    `;

            // Create our number formatter.
            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',

                // These options are needed to round to whole numbers if that's what you want.
                minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
            });

            relatedData.forEach(function(row) {
                let date = new Date(row.date);
                let month = date.toLocaleString('default', {
                    month: 'long'
                }); // Full month name
                let year = date.getFullYear();

                let achieve = row.actual_sales <= 0 || row.total_target <= 0 ? 0 : (row.actual_sales / row
                    .total_target) * 100;

                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                };
                formattedData += `
                    <tr>
                        <td>${row.month}</td>
                        <td>${row.year}</td>
                        <td>${row.warehouse_name}</td>
                        <td>${row.client_name}</td>
                        <td>${formatter.format(row.total_target)}</td>
                        <td>${formatter.format(row.actual_sales)}</td>
                        <td>${(achieve).toFixed(2)}%</td>
                        <td>${achieve >= 100 ? '<span class="label label-primary">ACHIEVE</span>' : '<span class="label label-danger">FAIL</span>'}</td>
                    </tr>
                `;
            });

            formattedData += `
                        </tbody>
                    </table>
                </div>
            `;

            return formattedData;
        }

        // Handle row expansion
        $('#target-tables-report tbody').on('click', 'td.dt-control', function(e) {
            let tr = e.target.closest('tr');
            let row = tableTargetsReport.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                $(tr).removeClass('dt-hasChild');
            } else {
                // Open this row and show additional data
                row.child(format(row.data())).show();
                $(tr).addClass('dt-hasChild');
            }
        });
        // END TARGET REPORT

        // TARGET REPORT ADMINISTRATOR
        // Initialize the main DataTable
        var tableTargetsReportAdministrator = $('#target-tables-report-administrator').DataTable($.extend({},
            dataTableOptions, {
                "ajax": "{{ route('admin.admin.target.getTargetsTablesReportAdministrator') }}",
                "columns": [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        'data': 'month',
                        'name': 'month'
                    },
                    {
                        'data': 'year',
                        'name': 'year'
                    },
                    {
                        'data': 'total_target',
                        'name': 'total_target'
                    },
                    {
                        'data': 'total_selling',
                        'name': 'total_selling'
                    },
                    {
                        'data': 'rate',
                        'name': 'rate'
                    },
                    {
                        'data': 'status',
                        'name': 'status'
                    },
                ]
            }
        ));

        // Filter functions
        function filterByMonthTarget() {
            var selectedMonth = $('#monthFilterTarget').val();
            tableTargetsReportAdministrator.column('month:name').search(selectedMonth).draw();
        }

        function filterByWarehouseTarget() {
            var selectedMerchant = $('#merchantFilterTarget').val();
            tableTargetsReportAdministrator.column('warehouse:name').search(selectedMerchant).draw();
        }

        function filterByYearTarget() {
            var selectedMerchant = $('#yearFilterTarget').val();
            tableTargetsReportAdministrator.column('year:name').search(selectedMerchant).draw();
        }

        // Bind filter functions to the change event of filter dropdowns
        $('#monthFilterTarget').on('change', filterByMonthTarget);
        $('#merchantFilterTarget').on('change', filterByWarehouseTarget);
        $('#yearFilterTarget').on('change', filterByYearTarget);

        // Fetch additional data for both levels via AJAX
        let additionalDataAdministratorTarget = [];
        $.ajax({
            url: "{{ route('admin.admin.target.getTargetsTablesExpandAdministrator') }}",
            method: 'GET',
            success: function(data) {
                additionalDataAdministratorTarget = data.data;
            },
            error: function(xhr, status, error) {
                console.error('Error fetching additional data:', error);
            }
        });

        let additionalDataAdministratorSecondTarget = [];
        $.ajax({
            url: "{{ route('admin.admin.target.getTargetsTablesExpand') }}",
            method: 'GET',
            success: function(data) {
                additionalDataAdministratorSecondTarget = data.data;
            },
            error: function(xhr, status, error) {
                console.error('Error fetching additional data:', error);
            }
        });

        // First-level row formatting function
        function formatFirstLevelTarget(dataItem) {
            let relatedDataAdministrator = additionalDataAdministratorTarget.filter(item =>
                item.month == dataItem.month && item.year == dataItem.year);

            // console.log(additionalDataAdministrator, 'additionalDataAdministrator first')
            // console.log(dataItem, 'dataItem first')

            let formattedDataAdministrator = `
        <div class="table-responsive" >
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Warehouse</th>
                        <th>Total Target</th>
                        <th>Total Selling</th>
                        <th>Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    `;

            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            });

            relatedDataAdministrator.forEach(function(row, index) {
                let achieve = row.actual_sales <= 0 || row.total_target <= 0 ? 0 : (row.actual_sales / row
                    .total_target) * 100;

                formattedDataAdministrator += `
            <tr class="expanded-second-table" data-parent-id="${dataItem.month}-${dataItem.year}-${row.warehouse_name}" id="expanded-second-table-${index}">
                <td class="dt-control-second"></td>
                <td>${row.month}</td>
                <td>${row.year}</td>
                <td>${row.warehouse_name}</td>
                <td>${formatter.format(row.total_target)}</td>
                <td>${formatter.format(row.actual_sales)}</td>
                <td>${(achieve).toFixed(2)}%</td>
                <td>${achieve >= 100 ? '<span class="label label-primary">ACHIEVE</span>' : '<span class="label label-danger">FAIL</span>'}</td>
            </tr>
        `;
            });

            formattedDataAdministrator += `
                </tbody>
            </table>
        </div>
    `;

            return formattedDataAdministrator;
        }

        let additionalDataAdministratorWarehouseClientTarget = [];
        $.ajax({
            url: "{{ route('admin.admin.target.getTargetsTablesExpandClientAdministrator') }}",
            method: 'GET',
            success: function(data) {
                additionalDataAdministratorWarehouseClientTarget = data.data;
            },
            error: function(xhr, status, error) {
                console.error('Error fetching additional data:', error);
            }
        });

        // Second-level row formatting function
        function formatSecondLevelTarget(dataItem) {
            let relatedDataAdministratorSecond = additionalDataAdministratorSecondTarget
                .filter(item =>
                    item.month == dataItem.month && item.year == dataItem.year && item.warehouse_name == dataItem
                    .warehouse_name);

            console.log(additionalDataAdministratorSecondTarget, 'additionalDataAdministratorSecondTarget kedua')
            console.log(dataItem, 'dataItem kedua')

            // Create our number formatter.
            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',

                // These options are needed to round to whole numbers if that's what you want.
                minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
            });



            let formattedDataAdministratorSecond = `
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Warehouse</th>
                        <th>Client</th>
                        <th>Total Target</th>
                        <th>Total Selling</th>
                        <th>Rate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    `;

            relatedDataAdministratorSecond.forEach(function(row) {
                let achieve = row.actual_sales <= 0 || row.total_target <= 0 ? 0 : (row.actual_sales / row
                    .total_target) * 100;
                formattedDataAdministratorSecond += `
            <tr>
                <td>${row.month}</td>
                <td>${row.year}</td>
                <td>${row.warehouse_name}</td>
                <td>${row.client_name}</td>
                <td>${formatter.format(row.total_target)}</td>
                <td>${formatter.format(row.actual_sales)}</td>
                <td>${(achieve).toFixed(2)}%</td>
                <td>${achieve >= 100 ? '<span class="label label-primary">ACHIEVE</span>' : '<span class="label label-danger">FAIL</span>'}</td>
            </tr>
        `;
            });

            formattedDataAdministratorSecond += `
                </tbody>
            </table>
        </div>
    `;

            return formattedDataAdministratorSecond;
        }

        // Handle first-level row expansion
        $('#target-tables-report-administrator tbody').on('click', 'td.dt-control', function(e) {
            let tr = e.target.closest('tr');
            let row = tableTargetsReportAdministrator.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                $(tr).removeClass('dt-hasChild');
            } else {
                row.child(formatFirstLevelTarget(row.data())).show();
                $(tr).addClass('dt-hasChild');
            }
        });

        // Handle second-level row expansion
        $('#target-tables-report-administrator tbody').on('click', 'td.dt-control-second', function(e) {
            e.stopPropagation(); // Prevent the first-level expansion from being triggered

            // Locate the clicked second-level row
            let secondLevelTr = $(this).closest('tr.expanded-second-table');

            // Retrieve the data-parent-id attribute to identify the parent row
            let parentId = secondLevelTr.data('parent-id');

            // Use the parentId to find the first-level parent data
            let [month, year, warehouseName] = parentId.split('-');

            console.log(additionalDataAdministratorTarget, 'additionalDataAdministratorTarget')

            let parentData = additionalDataAdministratorTarget.find(item =>
                item.month === month && item.year === year && item.warehouse_name === warehouseName);

            // Debugging: Log the relevant elements and data
            console.log(secondLevelTr, 'Second-level clicked row');
            console.log(parentId, 'Parent ID');
            console.log(parentData, 'Parent data for second-level expansion');

            if (!parentData) {
                console.error('Parent data is undefined. Ensure correct row selection.');
                return;
            }

            // Proceed with showing or hiding the child row as needed
            let childRow = secondLevelTr.next('tr.child');
            if (childRow.length && childRow.is(':visible')) {
                childRow.hide();
                secondLevelTr.removeClass('shown');
            } else {
                if (!childRow.length) {
                    let childRowContent = formatSecondLevelTarget(parentData);
                    secondLevelTr.after('<tr class="child"><td colspan="8">' + childRowContent + '</td></tr>');
                    childRow = secondLevelTr.next('tr.child');
                }
                childRow.show();
                secondLevelTr.addClass('shown');
            }
        });
        // END TARGET REPORT ADMINISTRATOR


        // BUDGET
        // Load offering list by Ajax
        var tableBudgets = $('#budget-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.budget.getBudgets') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'date',
                    'name': 'date'

                },
                {
                    'data': 'month',
                    'name': 'month'

                },
                {
                    'data': 'year',
                    'name': 'year'

                },
                {
                    'data': 'requirement',
                    'name': 'requirement'
                },
                {
                    'data': 'category',
                    'name': 'category'
                },
                {
                    'data': 'qty',
                    'name': 'qty'
                },
                {
                    'data': 'total',
                    'name': 'total'
                },
                {
                    'data': 'grand_total',
                    'name': 'grand_total'
                },
                {
                    'data': 'picture',
                    'name': 'picture'
                },
                {
                    'data': 'warehouse',
                    'name': 'warehouse'
                },
                {
                    'data': 'status',
                    'name': 'status'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at',
                },
                {
                    'data': 'created_by',
                    'name': 'created_by',
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at',
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by',
                },
                {
                    'data': 'approved_at',
                    'name': 'approved_at',
                },
                {
                    'data': 'approved_by',
                    'name': 'approved_by',
                },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        // Filter the 'created_by' column with the name of the authenticated user
        @if (!Auth::user()->isAdmin())
            tableBudgets.column('created_by:name').search('{{ Auth::user()->name }}').draw();
        @endif

        // Function to calculate the total amount
        function calculateTotal() {
            var total = 0;
            tableBudgets.rows({
                search: 'applied'
            }).every(function(rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                var amount = data.grand_total.replace(/[^\d]/g, ''); // Remove non-numeric characters
                total += parseFloat(amount); // Assuming the 'Amount' column is at index 1
            });

            $('#totalAmount').html('Rp. ' + total.toLocaleString('id-ID'));
        }

        function filterByMonth() {
            var selectedMonth = $('#monthFilter').val();

            // Apply the month filter to the 'month' column (assume the column name is 'month')
            tableBudgets.column('month:name').search(selectedMonth).draw();
        }

        function filterByWarehouse() {
            var selectedMerchant = $('#merchantFilter').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableBudgets.column('warehouse:name').search(selectedMerchant).draw();
        }

        function filterByYear() {
            var selectedMerchant = $('#yearFilter').val();

            // Apply the year filter to the 'year' column (assume the column name is 'year')
            tableBudgets.column('year:name').search(selectedMerchant).draw();
        }

        // Initial calculation
        calculateTotal();

        // Bind the filter and calculation function to the month dropdown change event
        $('#monthFilter').on('change', filterByMonth);
        $('#merchantFilter').on('change', filterByWarehouse);
        $('#yearFilter').on('change', filterByYear);

        // Recalculate the total on each table draw
        tableBudgets.on('draw', function() {
            calculateTotal();
        });
        // END BUGDET

        // BUDGET CATEGORIES
        // Load offering list by Ajax
        var tableBudgetsCategories = $('#budget-categories-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.requirement.getRequirements') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'name',
                    'name': 'name'

                },
                {
                    'data': 'value',
                    'name': 'value'
                },
                {
                    'data': 'type',
                    'name': 'type'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at'
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by'
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at'
                },
                {
                    'data': 'action',
                    'name': 'action',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        // Filter the 'created_by' column with the name of the authenticated user
        // @if (!Auth::user()->isAdmin())
        //     tableBudgetsCategories.column('warehouse:name').search('{{ Auth::user()->warehouse_name }}').draw();
        // @endif

        // function filterByWarehouse() {
        //     var selectedMerchant = $('#merchantCategoryFilter').val();

        //     // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
        //     tableBudgetsCategories.column('warehouse:name').search(selectedMerchant).draw();
        // }

        // Bind the filter and calculation function to the month dropdown change event
        // $('#merchantCategoryFilter').on('change', filterByWarehouse);
        // END BUGDET CATEGORIES

        // BUDGET SEGMENT
        // Load offering list by Ajax
        var tableBudgets = $('#segment-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.segment.getSegments') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'name',
                    'name': 'name'

                },
                {
                    'data': 'value',
                    'name': 'value'

                },
                {
                    'data': 'warehouse',
                    'name': 'warehouse'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at'
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by'
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at'
                },
                {
                    'data': 'action',
                    'name': 'action',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        // Filter the 'created_by' column with the name of the authenticated user
        @if (!Auth::user()->isAdmin())
            tableBudgets.column('warehouse:name').search('{{ Auth::user()->warehouse_name }}').draw();
        @endif


        function filterByWarehouse() {
            var selectedMerchant = $('#merchantConfigFilter').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableBudgets.column('warehouse:name').search(selectedMerchant).draw();
        }

        // Bind the filter and calculation function to the month dropdown change event
        $('#merchantConfigFilter').on('change', filterByWarehouse);
        // END BUGDET SEGMENT

        // BUDGET REPORT
        // Load offering list by Ajax
        var tableBudgetsReport = $('#budget-report-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.budget.getBudgetsReport') }}",
            "columns": [{
                    'data': 'month',
                    'name': 'month'

                },
                {
                    'data': 'year',
                    'name': 'year'

                },
                {
                    'data': 'business_unit',
                    'name': 'business_unit'
                },
                {
                    'data': 'buying_product',
                    'name': 'buying_product'
                },
                {
                    'data': 'fee_management',
                    'name': 'fee_management'
                },
                {
                    'data': 'marketing',
                    'name': 'marketing'
                },
                {
                    'data': 'operational',
                    'name': 'operational'
                },
                {
                    'data': 'total_budget',
                    'name': 'total_budget'
                },
                {
                    'data': 'total_selling',
                    'name': 'total_selling'
                },
                {
                    'data': 'achieve',
                    'name': 'achieve'
                },
                {
                    'data': 'status',
                    'name': 'status'
                },
            ]
        }));

        // Filter the 'created_by' column with the name of the authenticated user
        @if (!Auth::user()->isAdmin())
            tableBudgetsReport.column('created_by:name').search('{{ Auth::user()->pic_name }}').draw();
        @endif

        function filterByMonth() {
            var selectedMonth = $('#monthFilter').val();

            // Apply the month filter to the 'month' column (assume the column name is 'month')
            tableBudgetsReport.column('month:name').search(selectedMonth).draw();
        }

        function filterByWarehouse() {
            var selectedMerchant = $('#merchantFilter').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableBudgetsReport.column('warehouse:name').search(selectedMerchant).draw();
        }

        function filterByYear() {
            var selectedMerchant = $('#yearFilter').val();

            // Apply the year filter to the 'year' column (assume the column name is 'year')
            tableBudgetsReport.column('year:name').search(selectedMerchant).draw();
        }

        // Initial calculation
        calculateTotal();

        // Bind the filter and calculation function to the month dropdown change event
        $('#monthFilter').on('change', filterByMonth);
        $('#merchantFilter').on('change', filterByWarehouse);
        $('#yearFilter').on('change', filterByYear);

        // Recalculate the total on each table draw
        tableBudgetsReport.on('draw', function() {
            calculateTotal();
        });
        // END BUGDET REPORT

        // BUDGET REPORT ADMINISTRATOR
        // Initialize the main Dataable
        var tableBudgetsReportAdministrator = $('#budget-tables-report-administrator').DataTable($.extend({},
            dataTableOptions, {
                "ajax": "{{ route('admin.admin.budget.getBudgetsTablesReportAdministrator') }}",
                "columns": [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        'data': 'month',
                        'name': 'month'
                    },
                    {
                        'data': 'year',
                        'name': 'year'
                    },
                    {
                        'data': 'total_budget',
                        'name': 'total_budget'
                    },
                    {
                        'data': 'total_selling',
                        'name': 'total_selling'
                    },
                    {
                        'data': 'rate_cost',
                        'name': 'rate_cost'
                    },
                ]
            }
        ));

        // Filter functions
        function filterByMonthBudget() {
            var selectedMonth = $('#monthFilterBudget').val();
            tableBudgetsReportAdministrator.column('month:name').search(selectedMonth).draw();
        }

        function filterByWarehouseBudget() {
            var selectedMerchant = $('#merchantFilterBudget').val();
            tableBudgetsReportAdministrator.column('warehouse:name').search(selectedMerchant).draw();
        }

        function filterByYearBudget() {
            var selectedMerchant = $('#yearFilterBudget').val();
            tableBudgetsReportAdministrator.column('year:name').search(selectedMerchant).draw();
        }

        // Bind filter functions to the change event of filter dropdowns
        $('#monthFilterBudget').on('change', filterByMonthBudget);
        $('#merchantFilterBudget').on('change', filterByWarehouseBudget);
        $('#yearFilterBudget').on('change', filterByYearBudget);

        // Fetch additional data for both levels via AJAX
        let additionalDataAdministratorBudget = [];
        $.ajax({
            url: "{{ route('admin.admin.budget.getBudgetTablesExpandAdministrator') }}",
            method: 'GET',
            success: function(data) {
                additionalDataAdministratorBudget = data.data;
            },
            error: function(xhr, status, error) {
                console.error('Error fetching additional data budget:', error);
            }
        });

        let additionalDataAdministratorSecondBudget = [];
        $.ajax({
            url: "{{ route('admin.admin.budget.getBudgetsTablesExpandClientAdministrator') }}",
            method: 'GET',
            success: function(data) {
                additionalDataAdministratorSecondBudget = data.data;
            },
            error: function(xhr, status, error) {
                console.error('Error fetching additional data:', error);
            }
        });

        // First-level row formatting function
        function formatFirstLevel(dataItem) {
            let relatedDataAdministrator = additionalDataAdministratorBudget.filter(item =>
                item.month == dataItem.month && item.year == dataItem.year);

            // console.log(additionalDataAdministrator, 'additionalDataAdministrator first')
            // console.log(dataItem, 'dataItem first')

            let formattedDataAdministrator = `
        <div class="table-responsive" >
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Business Unit</th>
                        <th>Total Budget</th>
                        <th>Total Selling</th>
                        <th>Rate Cost</th>
                    </tr>
                </thead>
                <tbody>
    `;

            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            });

            relatedDataAdministrator.forEach(function(row, index) {
                formattedDataAdministrator += `
            <tr class="expanded-second-table" data-parent-id="${dataItem.month}-${dataItem.year}-${row.warehouse_area}" id="expanded-second-table-${index}">
                <td class="dt-control-second"></td>
                <td>${row.month}</td>
                <td>${row.year}</td>
                <td>${row.warehouse_area}</td>
                <td>${formatter.format(row.total_budget)}</td>
                <td>${formatter.format(row.total_selling)}</td>
                <td>${row.rate_cost === null ? "" : row.rate_cost.toFixed(2)}%</td>
            </tr>
        `;
            });

            formattedDataAdministrator += `
                </tbody>
            </table>
        </div>
    `;

            return formattedDataAdministrator;
        }

        let additionalDataAdministratorWarehouseClientBudget = [];
        $.ajax({
            url: "{{ route('admin.admin.budget.getBudgetsTablesExpandClientAdministrator') }}",
            method: 'GET',
            success: function(data) {
                additionalDataAdministratorWarehouseClientBudget = data.data;
            },
            error: function(xhr, status, error) {
                console.error('Error fetching additional data:', error);
            }
        });

        // Second-level row formatting function
        function formatSecondLevel(dataItem) {
            let relatedDataAdministratorSecond = additionalDataAdministratorSecondBudget
                .filter(item =>
                    item.month == dataItem.month && item.year == dataItem.year && item.warehouse_area == dataItem
                    .warehouse_area);

            // console.log(additionalDataAdministratorSecondBudget, 'additionalDataAdministratorSecondBudget kedua')
            // console.log(dataItem, 'dataItem kedua')

            // Create our number formatter.
            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',

                // These options are needed to round to whole numbers if that's what you want.
                minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
                maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
            });



            let formattedDataAdministratorSecond = `
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Business Unit</th>
                        <th>Segment Name</th>
                        <th>Segment Rate</th>
                        <th>Total Budget</th>
                        <th>Rate Cost</th>
                    </tr>
                </thead>
                <tbody>
    `;

            relatedDataAdministratorSecond.forEach(function(row) {
                formattedDataAdministratorSecond += `
            <tr>
                <td>${row.month}</td>
                <td>${row.year}</td>
                <td>${row.warehouse_area}</td>
                <td>${row.segment_name}</td>
                <td>${row.segment_rate}%</td>
                <td>${formatter.format(row.total_budget)}</td>
                <td>${row.rate_cost !== null ? row.rate_cost.toFixed(2) : ""}%</td>
            </tr>
        `;
            });

            formattedDataAdministratorSecond += `
                </tbody>
            </table>
        </div>
    `;

            return formattedDataAdministratorSecond;
        }

        // Handle first-level row expansion
        $('#budget-tables-report-administrator tbody').on('click', 'td.dt-control', function(e) {
            let tr = e.target.closest('tr');
            let row = tableBudgetsReportAdministrator.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                $(tr).removeClass('dt-hasChild');
            } else {
                row.child(formatFirstLevel(row.data())).show();
                $(tr).addClass('dt-hasChild');
            }
        });

        // Handle second-level row expansion
        $('#budget-tables-report-administrator tbody').on('click', 'td.dt-control-second', function(e) {
            e.stopPropagation(); // Prevent the first-level expansion from being triggered

            // Locate the clicked second-level row
            let secondLevelTr = $(this).closest('tr.expanded-second-table');

            // Retrieve the data-parent-id attribute to identify the parent row
            let parentId = secondLevelTr.data('parent-id');

            // Use the parentId to find the first-level parent data
            let [month, year, warehouseName] = parentId.split('-');

            let parentData = additionalDataAdministratorBudget
                .find(item =>
                    item.month == month && item.year == year && item.warehouse_area == warehouseName);

            // Debugging: Log the relevant elements and data
            console.log(secondLevelTr, 'Second-level clicked row');
            console.log(parentId, 'Parent ID');
            console.log(parentData, 'Parent data for second-level expansion');

            if (!parentData) {
                console.error('Parent data is undefined. Ensure correct row selection.');
                return;
            }

            // Proceed with showing or hiding the child row as needed
            let childRow = secondLevelTr.next('tr.child');
            if (childRow.length && childRow.is(':visible')) {
                childRow.hide();
                secondLevelTr.removeClass('shown');
            } else {
                if (!childRow.length) {
                    let childRowContent = formatSecondLevel(parentData);
                    secondLevelTr.after('<tr class="child"><td colspan="8">' + childRowContent + '</td></tr>');
                    childRow = secondLevelTr.next('tr.child');
                }
                childRow.show();
                secondLevelTr.addClass('shown');
            }
        });
        // END BUDGET REPORT ADMINISTRATOR

        // CRM DATA
        // Load offering list by Ajax
        var tableCRMsData = $('#crm-data-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.crm.getCRMsDataTables') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'date',
                    'name': 'date'
                },
                {
                    'data': 'month',
                    'name': 'month'
                },
                {
                    'data': 'year',
                    'name': 'year'
                },
                {
                    'data': 'warehouse',
                    'name': 'warehouse'
                },
                {
                    'data': 'client',
                    'name': 'client'
                },
                {
                    'data': 'picture',
                    'name': 'picture'
                },
                {
                    'data': 'plan',
                    'name': 'plan'
                },
                {
                    'data': 'action',
                    'name': 'action'
                },
                {
                    'data': 'verified_status',
                    'name': 'verified_status'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at'
                },
                {
                    'data': 'created_by',
                    'name': 'created_by'
                },
                {
                    'data': 'verified_at',
                    'name': 'verified_at'
                },
                {
                    'data': 'verified_by',
                    'name': 'verified_by'
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at'
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by'
                },
                {
                    'data': 'options',
                    'name': 'options'
                },
            ]
        }));


        // Filter the 'created_by' column with the name of the authenticated user
        @if (Auth::user()->role_id !== 1)
            tableCRMsData.column('created_by:name').search('{{ Auth::user()->name }}').draw();
        @endif

        function filterByMonthCRM() {
            var selectedMonth = $('#monthFilterCRM').val();

            // Apply the month filter to the 'month' column (assume the column name is 'month')
            tableCRMsData.column('month:name').search(selectedMonth).draw();
        }

        function filterByWarehouseCRM() {
            var selectedMerchant = $('#merchantFilterCRM').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableCRMsData.column('warehouse:name').search(selectedMerchant).draw();
        }

        function filterByYearCRM() {
            var selectedMerchant = $('#yearFilterCRM').val();

            // Apply the year filter to the 'year' column (assume the column name is 'year')
            tableCRMsData.column('year:name').search(selectedMerchant).draw();
        }

        // Bind the filter and calculation function to the month dropdown change event
        $('#monthFilterCRM').on('change', filterByMonthCRM);
        $('#merchantFilterCRM').on('change', filterByWarehouseCRM);
        $('#yearFilterCRM').on('change', filterByYearCRM);
        // END OF CRM DATA

        // CRM
        // Load offering list by Ajax
        var tableCRMs = $('#crm-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.crm.getCRMsTables') }}",
            "columns": [
                // {
                //     className: 'dt-control',
                //     orderable: false,
                //     data: null,
                //     defaultContent: ''
                // },
                // {
                //     'data': 'checkbox',
                //     'name': 'checkbox',
                //     'orderable': false,
                //     'searchable': false,
                //     'exportable': false,
                //     'printable': false
                // },
                {
                    'data': 'month',
                    'name': 'month'
                },
                {
                    'data': 'year',
                    'name': 'year'
                },
                {
                    'data': 'warehouse',
                    'name': 'warehouse',
                },
                {
                    'data': 'total_plan',
                    'name': 'total_plan'
                },
                {
                    'data': 'total_plan_actual',
                    'name': 'total_plan_actual'
                },
                {
                    'data': 'success_rate',
                    'name': 'success_rate'
                },
                {
                    'data': 'status',
                    'name': 'status'
                },
            ],
            columnDefs: [{
                visible: false,
                targets: 2
            }],
            order: [
                [2, 'asc']
            ],
            displayLength: 25,
            drawCallback: function(settings) {
                var api = this.api();
                var rows = api.rows({
                    page: 'current'
                }).nodes();
                var last = null;

                // api.column(2, { page: 'current' })
                //     .data()
                //     .each(function (group, i) {
                //         if (last !== group) {
                //             $(rows)
                //                 .eq(i)
                //                 .before(
                //                     '<tr class="group"><td colspan="5">' +
                //                         group +
                //                         '</td></tr>'
                //                 );

                //             last = group;
                //         }
                //     });
            }
        }));

        // Filter the 'created_by' column with the name of the authenticated user
        @if (!Auth::user()->role_id === 8 || !Auth::user()->role_id === 1)
            tableCRMs.column('created_by:name').search('{{ Auth::user()->name }}').draw();
        @endif

        function filterByMonthCRM() {
            var selectedMonth = $('#monthFilterCRM').val();

            // Apply the month filter to the 'month' column (assume the column name is 'month')
            tableCRMs.column('month:name').search(selectedMonth).draw();
        }

        function filterByWarehouseCRM() {
            var selectedMerchant = $('#merchantFilterCRM').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableCRMs.column('warehouse:name').search(selectedMerchant).draw();
        }

        function filterByYearCRM() {
            var selectedMerchant = $('#yearFilterCRM').val();

            // Apply the year filter to the 'year' column (assume the column name is 'year')
            tableCRMs.column('year:name').search(selectedMerchant).draw();
        }

        // Bind the filter and calculation function to the month dropdown change event
        $('#monthFilterCRM').on('change', filterByMonthCRM);
        $('#merchantFilterCRM').on('change', filterByWarehouseCRM);
        $('#yearFilterCRM').on('change', filterByYearCRM);

        // Formatting function for row details - modify as you need
        //     function format(d) {
        //       console.log(d, 'dataaa')
        //     return `
        //         <div class="table-responsive">
        //             <table class="table table-hover">
        //                 <thead>
        //                     <tr>
        //                         <th></th>
        //                         <th></th>
        //                         <th>Date</th>
        //                         <th>Month</th>
        //                         <th>Year</th>
        //                         <th>Warehouse</th>
        //                         <th>Client</th>
        //                         <th>Picture</th>
        //                         <th>Created At</th>
        //                         <th>Created By</th>
        //                         <th>Updated At</th>
        //                         <th>Updated By</th>
        //                         <th>Option</th>
        //                     </tr>
        //                 </thead>
        //                 <tbody id="massSelectArea">
        //                     <tr>
        //                         <td></td>
        //                         <td></td>
        //                         <td>${d.date}</td>
        //                         <td>${d.month}</td>
        //                         <td>${d.year}</td>
        //                         <td>${d.warehouse}</td>
        //                         <td>${d.client}</td>
        //                         <td>${d.picture}</td>
        //                         <td>${d.created_at}</td>
        //                         <td>${d.created_by}</td>
        //                         <td>${d.updated_at}</td>
        //                         <td>${d.updated_by}</td>
        //                         <td>${d.option}</td>
        //                     </tr>
        //                 </tbody>
        //             </table>
        //         </div>
        //     `;
        // }

        // Add event listener for opening and closing details
        tableCRMs.on('click', 'td.dt-control', function(e) {
            let tr = e.target.closest('tr');
            let row = tableCRMs.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                $(tr).removeClass('dt-hasChild');
            } else {
                // Open this row
                row.child(format(row.data())).show();
                // row.child("<tr><td>test</td></tr>").show();

                $(tr).addClass('dt-hasChild');
            }
        });

        // END CRM

        // Site Visit
        var tableVisits = $('#visit-tables').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.admin.visit.getVisitsTables') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                {
                    'data': 'date',
                    'name': 'date'
                },
                {
                    'data': 'month',
                    'name': 'month'
                },
                {
                    'data': 'year',
                    'name': 'year'
                },
                {
                    'data': 'client',
                    'name': 'client'
                },
                {
                    'data': 'warehouse',
                    'name': 'warehouse'
                },
                {
                    'data': 'assignee',
                    'name': 'assignee'
                },
                {
                    'data': 'picture',
                    'name': 'picture'
                },
                {
                    'data': 'note',
                    'name': 'note'
                },
                {
                    'data': 'next_visit_date',
                    'name': 'next_visit_date'
                },
                {
                    'data': 'status',
                    'name': 'status'
                },
                {
                    'data': 'verified_by',
                    'name': 'verified_by'
                },
                {
                    'data': 'verified_at',
                    'name': 'verified_at'
                },
                {
                    'data': 'created_at',
                    'name': 'created_at',
                },
                {
                    'data': 'created_by',
                    'name': 'created_by',
                },
                {
                    'data': 'updated_at',
                    'name': 'updated_at',
                },
                {
                    'data': 'updated_by',
                    'name': 'updated_by',
                },
                {
                    'data': 'options',
                    'name': 'options',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        // Filter the 'created_by' column with the name of the authenticated user
        @if (!Auth::user()->role_id === 13 || !Auth::user()->role_id === 1)
            tableVisits.column('assignee:name').search('{{ Auth::user()->name }}').draw();
        @endif

        function filterByMonthVisit() {
            var selectedMonth = $('#monthFilterVisit').val();

            // Apply the month filter to the 'month' column (assume the column name is 'month')
            tableVisits.column('month:name').search(selectedMonth).draw();
        }

        function filterByWarehouseVisit() {
            var selectedMerchant = $('#merchantFilterVisit').val();

            // Apply the business area filter to the 'business area' column (assume the column name is 'business area')
            tableVisits.column('warehouse:name').search(selectedMerchant).draw();
        }

        function filterByYearVisit() {
            var selectedMerchant = $('#yearFilterVisit').val();

            // Apply the year filter to the 'year' column (assume the column name is 'year')
            tableVisits.column('year:name').search(selectedMerchant).draw();
        }

        // Bind the filter and calculation function to the month dropdown change event
        $('#monthFilterVisit').on('change', filterByMonthVisit);
        $('#merchantFilterVisit').on('change', filterByWarehouseVisit);
        $('#yearFilterVisit').on('change', filterByYearVisit);

        // Load category list by Ajax
        $('#all-categories-table').DataTable($.extend({}, dataTableOptions, {
            "ajax": "{{ route('admin.catalog.category.getMore') }}",
            "columns": [{
                    'data': 'checkbox',
                    'name': 'checkbox',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                },
                // {
                //   'data': 'cover_image',
                //   'name': 'cover_image',
                //   'orderable': false,
                //   'searchable': false
                // },
                // {
                //   'data': 'feature_image',
                //   'name': 'feature_image',
                //   'orderable': false,
                //   'searchable': false
                // },
                {
                    'data': 'name',
                    'name': 'name'
                },
                {
                    'data': 'parent',
                    'name': 'parent',
                    'orderable': false,
                    'searchable': false
                },
                // {
                //   'data': 'attrs_list_count',
                //   'name': 'attrs_list_count',
                //   'searchable': false
                // },
                {
                    'data': 'products_count',
                    'name': 'products_counts',
                    'orderable': false,
                    'searchable': false
                },
                {
                    'data': 'listings_count',
                    'name': 'listings_count',
                    'searchable': false
                },
                // {
                //   'data': 'order',
                //   'name': 'order',
                //   'searchable': false
                // },
                {
                    'data': 'option',
                    'name': 'option',
                    'orderable': false,
                    'searchable': false,
                    'exportable': false,
                    'printable': false
                }
            ]
        }));

        $(".table-2nd-sort").DataTable({
            "iDisplayLength": {{ getPaginationValue() }},
            "aaSorting": [
                [1, "asc"]
            ],
            "oLanguage": {
                "sInfo": "_START_ to _END_ of _TOTAL_ entries",
                "sLengthMenu": "Show _MENU_",
                "sSearch": "",
                "sEmptyTable": "No data found!",
                "oPaginate": {
                    "sNext": '<i class="fa fa-hand-o-right"></i>',
                    "sPrevious": '<i class="fa fa-hand-o-left"></i>',
                }
            },
            "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [0, -1]
            }],
            "lengthMenu": [
                [10, 25, 50, -1],
                ['10 rows', '25 rows', '50 rows', 'Show all']
            ], // page length options
            dom: 'Bfrtip',
            buttons: [
                'pageLength', 'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        $(".table-option").DataTable({
            "responsive": true,
            "iDisplayLength": {{ getPaginationValue() }},
            "oLanguage": {
                "sInfo": "_START_ to _END_ of _TOTAL_ entries",
                "sLengthMenu": "Show _MENU_",
                "sSearch": "",
                "sEmptyTable": "No data found!",
                "oPaginate": {
                    "sNext": '<i class="fa fa-hand-o-right"></i>',
                    "sPrevious": '<i class="fa fa-hand-o-left"></i>',
                },
            },
            "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [-1]
            }],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        $(".table-no-sort").DataTable({
            // "bSort": false,
            "aaSorting": [],
            "iDisplayLength": {{ getPaginationValue() }},
            "oLanguage": {
                "sInfo": "_START_ to _END_ of _TOTAL_ entries",
                "sLengthMenu": "Show _MENU_",
                "sSearch": "",
                "sEmptyTable": "No data found!",
                "oPaginate": {
                    "sNext": '<i class="fa fa-hand-o-right"></i>',
                    "sPrevious": '<i class="fa fa-hand-o-left"></i>',
                },
            },
            "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [0, -1]
            }],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        $(".table-2nd-no-sort").DataTable({
            "aaSorting": [],
            "iDisplayLength": {{ getPaginationValue() }},
            "oLanguage": {
                "sInfo": "_START_ to _END_ of _TOTAL_ entries",
                "sLengthMenu": "Show _MENU_",
                "sSearch": "",
                "sEmptyTable": "No data found!",
                "oPaginate": {
                    "sNext": '<i class="fa fa-hand-o-right"></i>',
                    "sPrevious": '<i class="fa fa-hand-o-left"></i>',
                },
            },
            "aoColumnDefs": [{
                "bSortable": false,
                "aTargets": [0, 1, -1]
            }],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        $('.table-no-option').DataTable({
            "sLength": "",
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": true,
            "autoWidth": false
        });

        $(".dataTables_length select").addClass(
            'select2-normal'); //Make the data-table length dropdown like select 2
        $(".dt-buttons > .btn").addClass('btn-sm'); //Make the data-table option buttins smaller
    }
    //END DataTables

    //App plugins
    function initAppPlugins() {
        $.ajaxSetup({
            cache: false,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        $('.ajax-form').submit(function(e) {
            e.preventDefault();
            //Return false and abort the action if the form validation failed
            if ($(this).find('input[type=submit]').hasClass('disabled')) {
                notie.alert(3, "{{ trans('responses.form_validation_failed') }}", 5);
                return;
            }

            apply_busy_filter();
        });

        // Icon picker
        $('.iconpicker-input').iconpicker();

        //Initialize Select2 Elements
        $(".select2").not(".dataTables_length .select2").select2();

        $(".select2-normal").select2({
            placeholder: "{{ trans('app.placeholder.select') }}",
            minimumResultsForSearch: -1,
        });

        $(".select2-tag").select2({
            placeholder: "{{ trans('app.placeholder.tags') }}",
            tags: true,
            allowClear: true,
            tokenSeparators: [',', ';'],
        });

        $(".select2-keywords").select2({
            placeholder: "{{ trans('app.keywords') }}",
            tags: true,
            allowClear: true,
            tokenSeparators: [',', ';'],
        });

        $(".select2-set_attribute").select2({
            placeholder: "{{ trans('app.placeholder.attribute_values') }}",
            minimumResultsForSearch: -1,
            tags: true,
            allowClear: true,
            tokenSeparators: [',', ';'],
        });

        $(".select2-attribute_value-attribute").select2({
                placeholder: "{{ trans('app.placeholder.select') }}",
                minimumResultsForSearch: -1,
            })
            .on("change", function(e) {
                var dataString = 'id=' + $(this).val();
                $.ajax({
                    type: "get",
                    url: "{{ route('admin.ajax.getParrentAttributeType') }}",
                    data: dataString,
                    datatype: 'JSON',
                    success: function(attribute_type) {
                        if (attribute_type ==
                            {{ \App\Models\Attribute::TYPE_COLOR }}) {
                            $('#color-option').removeClass('hidden').addClass('show');
                        } else {
                            $('#color-option').removeClass('show').addClass('hidden');
                        }
                    }
                }, "html");
            });

        // $(".select2-roles").select2({
        //  {{-- placeholder: "{{ trans('app.placeholder.roles') }}" --}}
        // });

        //Country
        $("#country_id").change(function() {
            $("#state_id").empty().trigger('change'); //Reset the state dropdown
            var ID = $("#country_id").select2('data')[0].id;
            var url = "{{ route('ajax.getCountryStates') }}"

            $.ajax({
                delay: 250,
                data: "id=" + ID,
                url: url,
                success: function(result) {
                    var data = [];
                    if (result.length !== 0) {
                        data = $.map(result, function(val, id) {
                            return {
                                id: id,
                                text: val
                            };
                        })
                    }

                    $("#state_id").select2({
                        allowClear: true,
                        tags: true,
                        placeholder: "{{ trans('app.placeholder.state') }}",
                        data: data,
                        sortResults: function(results, container, query) {
                            if (query.term) {
                                return results.sort();
                            }

                            return results;
                        }
                    });
                }
            });
        });

        $(".select2-categories").select2({
            placeholder: "{{ trans('app.placeholder.category_sub_groups') }}"
        });

        $(".select2-multi").select2({
            dropdownAutoWidth: true,
            multiple: true,
            width: '100%',
            height: '30px',
            placeholder: "{{ trans('app.placeholder.select') }}",
            allowClear: true
        });

        $(".select2").not(".dataTables_length .select2").css('width', '100%');

        $('.select2-search__field').css('width', '100%');

        //Search for zipCode Test functtion here
        @if (is_incevio_package_loaded('zipcode'))
            $('.searchZipcode').select2({
                ajax: {
                    url: "{{ route(config('zipcode.routes.search')) }}",
                    dataType: 'json',
                    processResults: function(data) {
                        return {
                            results: data,
                            flag: 'selectprogram',
                        };
                    },
                    cache: true
                },
                placeholder: "{{ trans('zipcode::lang.search_zipcode') }}",
                minimumInputLength: 3,
            });
        @endif
        //End search forr zipcde

        //product Seach
        $('#searchProduct').on('keyup', function(e) {
            var showResult = $("#productFounds");
            var q = $(this).val();

            showResult.html('');

            if (q.length < 0) {
                showResult.html(
                    '<span class="lead indent50">{{ trans('validation.min.string', ['attribute' => trans('app.form.search'), 'min' => '0']) }}</span>'
                );
                return;
            }

            showResult.html(
                '<span class="lead indent50">{{ trans('responses.searching') }}</span>');

            $.ajax({
                data: "q=" + q,
                url: "{{ route('search.product') }}",
                // contentType: "application/json; charset=utf-8",
                success: function(results) {
                    showResult.html(results);
                }
            });
        });
        //End product Seach

        $('#searchProduct').on('focus', function(e) {
            var showResult = $("#productFounds");
            var q = $(this).val();
            showResult.html('');
            if (q.length < 0) {
                showResult.html(
                    '<span class="lead indent50">{{ trans('validation.min.string', ['attribute' => trans('app.form.search'), 'min' => '0']) }}</span>'
                );
                return;
            }
            showResult.html('<span class="lead indent50">{{ trans('responses.searching') }}</span>');
            $.ajax({
                data: "q=" + q,
                url: "{{ route('search.product') }}",
                // contentType: "application/json; charset=utf-8",
                success: function(results) {
                    showResult.html(results);
                }
            });
        });

        //Customer Search
        $('.searchCustomer').select2({
            ajax: {
                url: "{{ route('search.customer') }}",
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data,
                        flag: 'selectprogram',
                    };
                },
                cache: true
            },
            placeholder: "{{ trans('app.placeholder.search_customer') }}",
            //minimumInputLength: 3,
        });
        //End Customer Seach

        // Merchant Seach
        $('.searchMerchant').select2({
            ajax: {
                url: "{{ route('search.merchant') }}",
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: "{{ trans('app.placeholder.search_merchant') }}",
            minimumInputLength: 3,
        });
        //End Merchant Search

        //Customer Search
        $('.searchWarehouse').select2({
            ajax: {
                url: "{{ route('search.warehouse') }}",
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data,
                        flag: 'selectprogram',
                    };
                },
                cache: true
            },
            placeholder: "{{ trans('app.placeholder.search_warehouse') }}",
            //minimumInputLength: 3,
        });
        //End Customer Seach

        // Products Search for Select2
        $('.searchProductForSelect').select2({
            ajax: {
                url: "{{ route('search.findProduct') }}",
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: "{!! trans('app.placeholder.search_product') !!}",
            minimumInputLength: 3,
        });
        //End Products Search for Select2

        // Inventories Search for Select2
        $('.searchInventoryForSelect').select2({
            ajax: {
                url: "{{ route('search.findInventory') }}",
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: "{!! trans('app.search_inventory') !!}",
            minimumInputLength: 3,
            allowClear: true
        });
        //End Products Search for Select2

        // Inventories Search for Select2
        $('.searchCategoryForSelect').select2({
            ajax: {
                url: "{{ route('search.findCategory') }}",
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: "{!! trans('app.search_category') !!}",
            minimumInputLength: 3,
            allowClear: true
        });
        //End Products Search for Select2

        /* bootstrap-select */
        $(".selectpicker").selectpicker();

        //Initialize validator And Prevent multiple submit of forms
        $('#form, form[data-toggle="validator"]').validator()
            .on('submit', function(e) {
                if (e.isDefaultPrevented()) {
                    $(this).find('input[type=submit]').removeAttr('disabled');
                } else {
                    $(this).find('input[type=submit]').attr('disabled', 'true');
                }
            });

        //Initialize summernote text editor
        $('.summernote').summernote({
            placeholder: "{{ trans('app.placeholder.start_here') }}",
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['color', ['color']],
                ['insert', ['link', 'picture', 'video']],
                ["view", ["codeview"]],
            ],
        });
        $('.summernote-min').summernote({
            placeholder: "{{ trans('app.placeholder.start_here') }}",
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
            ],
        });
        $('.summernote-without-toolbar').summernote({
            placeholder: "{{ trans('app.placeholder.start_here') }}",
            toolbar: [],
        });
        $('.summernote-long').summernote({
            placeholder: "{{ trans('app.placeholder.start_here') }}",
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
                ['view', ['codeview']],
            ],
            // codemirror: {
            //   theme: 'monokai'
            // },
            // onImageUpload: function(files) {
            //   url = $(this).data('upload'); //path is defined as data attribute for  textarea
            //   console.log(url);
            //   console.log(' not');
            //   // sendFile(files[0], url, $(this));
            // }
        });

        /*
         * Summernote hack
         */
        // Keep the dynamic modal open after close the insert media in summernote field
        $(document).on('hidden.bs.modal', '.modal', function() {
            $('.modal:visible').length && $(document.body).addClass('modal-open');
        });

        $('.modal-dismiss').click(function(event) {
            $('.note-modal').modal('hide');
        });

        //Datemask dd/mm/yyyy
        // $(".datemask").inputmask("yyyy-mm-dd", {"placeholder": "yyyy-mm-dd"});

        //Datemask2 mm/dd/yyyy
        // $(".datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});

        //Money Euro
        // $("[data-mask]").inputmask();

        //Date range picker
        // $('#reservation').daterangepicker();

        //Date range picker with time picker
        // $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
        //TimePicker
        $('.timepicker').datetimepicker({
            format: 'hh:mm A',
            icons: {
                time: 'glyphicon glyphicon-time',
            }
        });

        //Date range as a button
        //Timepicker
        // $(".timepicker").timepicker({
        //   showInputs: false
        // });

        //Datepicker
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd'
        });
        //DateTimepicker
        $(".datetimepicker").datetimepicker({
            format: 'YYYY-MM-DD hh:mm a',
            icons: {
                time: 'glyphicon glyphicon-time',
                date: 'glyphicon glyphicon-calendar',
                previous: 'glyphicon glyphicon-chevron-left',
                next: 'glyphicon glyphicon-chevron-right',
                today: 'glyphicon glyphicon-screenshot',
                up: 'glyphicon glyphicon-chevron-up',
                down: 'glyphicon glyphicon-chevron-down',
                clear: 'glyphicon glyphicon-trash',
                close: 'glyphicon glyphicon-remove'
            }
        });

        //Colorpicker
        $(".my-colorpicker1").colorpicker();
        //color picker with addon
        $(".my-colorpicker2").colorpicker();

        // $('#daterange-btn').daterangepicker(
        //     {
        //       ranges: {
        //         'Today': [moment(), moment()],
        //         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        //         'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        //         'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        //         'This Month': [moment().startOf('month'), moment().endOf('month')],
        //         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        //       },
        //       startDate: moment().subtract(29, 'days'),
        //       endDate: moment()
        //     },
        //     function (start, end) {
        //       $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        //     }
        // );

        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].icheck, input[type="radio"].icheck').iCheck({
            checkboxClass: 'icheckbox_flat-pink',
            radioClass: 'iradio_flat-pink'
        });
        //iCheck line checkbox and radio
        $('.icheckbox_line').each(function() {
            var self = $(this),
                label = self.next(),
                label_text = label.text();

            label.remove();
            self.iCheck({
                checkboxClass: 'icheckbox_line-pink',
                radioClass: 'iradio_line-pink',
                insert: '<div class="icheck_line-icon form-control"></div>' + label_text
            });
        });

        // Coupon form
        $('input#for_limited_customer').on('ifChecked', function() {
            $('#customers_field').removeClass('hidden').addClass('show');
            $('select#customer_list_field').attr('required', 'required');
        });
        $('input#for_limited_customer').on('ifUnchecked', function() {
            $('#customers_field').removeClass('show').addClass('hidden');
            $('select#customer_list_field').removeAttr('required');
        });

        $('input#for_limited_shipping_zones').on('ifChecked', function() {
            $('#zones_field').removeClass('hidden').addClass('show');
            $('select#zone_list_field').attr('required', 'required');
        });
        $('input#for_limited_shipping_zones').on('ifUnchecked', function() {
            $('#zones_field').removeClass('show').addClass('hidden');
            $('select#zone_list_field').removeAttr('required');
        });
        //END Coupon form

        //shipping zone
        $('input#rest_of_the_world').on('ifChecked', function() {
            $('select#country_ids').removeAttr('required').attr('disabled', 'disabled');
            $('select#country_ids').select2('val', '');
        });

        $('input#rest_of_the_world').on('ifUnchecked', function() {
            $('select#country_ids').removeAttr('disabled').attr('required', 'required');
        });

        $('input#free_shipping_checkbox').on('ifChecked', function() {
            $('input#shipping_rate_amount').val(0.0).removeAttr('required').attr('disabled',
                'disabled');
        });

        $('input#free_shipping_checkbox').on('ifUnchecked', function() {
            $('input#shipping_rate_amount').removeAttr('disabled').attr('required', 'required');
        });
        //END shipping zone

        //User Role form
        $("#user-role-status").change(function() {
            var temp = $("#user-role-status").select2('data')[0].text;
            var roleType = temp.toLowerCase();
            var rows = $('table#tbl-permissions tr');
            var platform = rows.filter('.platform-module');
            var merchant = rows.filter('.merchant-module');

            switch (roleType) {
                case 'platform':
                    platform.show();
                    merchant.hide();
                    merchant.find("input[type='checkbox']").iCheck('uncheck');
                    break;
                case 'merchant':
                    platform.hide();
                    merchant.show();
                    platform.find("input[type='checkbox']").iCheck('uncheck');
                    break;
                default:
                    platform.hide();
                    merchant.hide();
                    merchant.find("input[type='checkbox']").iCheck('uncheck');
                    platform.find("input[type='checkbox']").iCheck('uncheck');
            }
        });

        $('input.role-module').on('ifChecked', function() {
            var selfId = $(this).attr('id');
            var childClass = '.' + selfId + '-permission';
            $(childClass).iCheck('enable').iCheck('check');
        });

        $('input.role-module').on('ifUnchecked', function() {
            var selfId = $(this).attr('id');
            var childClass = '.' + selfId + '-permission';
            $(childClass).iCheck('uncheck').iCheck('disable');
        });
        //END User Role form

        //Slug URL Maker
        $('.makeSlug').on('change', function() {
            var slugstr = convertToSlug(this.value);
            $('.slug').val(slugstr);
            // setTimeout(sample,2000)
            verifyUniqueSlug();
        });

        $('.slug').on('change', function() {
            verifyUniqueSlug($(this).val());
        });

        // Check the slug with database to veridy uniqueness
        function verifyUniqueSlug(slug = '') {
            var node = $("#slug");
            var msg = "{{ trans('messages.slug_length') }}";

            // Get the slug from field when not provided
            if (slug == '') {
                slug = node.val();
            }

            // Minimum 3 charecters required
            if (slug.length >= 3) {
                var route = "{{ Route::current()->getName() }}";

                if (route.match(/categorySubGroup/i)) {
                    var tbl = 'category_sub_groups';
                    var url = 'categories/';
                } else if (route.match(/categoryGroup/i)) {
                    var tbl = 'category_groups';
                    var url = 'categorygrp/';
                } else if (route.match(/category/i)) {
                    var tbl = 'categories';
                    var url = 'category/';
                } else if (route.match(/product/i)) {
                    var tbl = 'products';
                    var url = 'product/';
                } else if (route.match(/manufacturer/i)) {
                    var tbl = 'manufacturers';
                    var url = 'brand/';
                } else if (route.match(/inventory/i)) {
                    var tbl = 'inventories';
                    var url = 'product/';
                    slug += '-' + '{{ Auth::user()->shop->slug }}';
                } else if (route.match(/page/i)) {
                    var tbl = 'pages';
                    var url = 'page/';
                } else if (route.match(/blog/i)) {
                    var tbl = 'blogs';
                    var url = 'blog/';
                } else if (route.match(/event/i)) {
                    var tbl = 'events';
                    var url = 'event/';
                } else {
                    var tbl = 'shops';
                    var url = 'shop/';
                }

                // Update the slug field if changed
                if (slug != node.val()) {
                    $('.slug').val(slug);
                }

                var check = getFromPHPHelper('verifyUniqueSlug', [slug, tbl]);

                result = JSON.parse(check);

                if (result.original == 'false') {
                    node.closest(".form-group").addClass('has-error');
                    msg = "{{ trans('messages.this_slug_taken') }}";
                } else if (result.original == 'true') {
                    node.closest(".form-group").removeClass('has-error');
                    msg = "{{ Str::finish(config('app.url'), '/') }}" + url + slug;
                }
            }

            node.next(".help-block").html(msg);
            return;
        }

        function convertToSlug(Text) {
            return Text.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        }
        //END Slug URL Maker

        //Popover
        $('[data-toggle="popover"]').popover({
            html: 'true',
        });

        $('[data-toggle="popover"]').on('click', function() {
            $('[data-toggle="popover"]').not(this).popover('hide');
        });

        $(document).on("click", ".popover-submit-btn", function() {
            $('[data-toggle="popover"]').popover('hide');
        });
        //END Popover

        if ($('#uploadBtn').length) {
            document.getElementById("uploadBtn").onchange = function() {
                document.getElementById("uploadFile").value = this.value;
            };
        }
        if ($('#uploadBtn1').length) {
            document.getElementById("uploadBtn1").onchange = function() {
                document.getElementById("uploadFile1").value = this.value;
            };
        }

        //SEARCH OPTIONS
        var $search_rows = $('#search_table tr');
        $('#search_this').keyup(function() {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
            $search_rows.show().filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });
        //END SEARCH OPTIONS

        //Random code string maker
        /**
         * generate Code
         */
        $('.generate-code').on("click", function(event) {
            var id = $(event.target).attr('id');
            var func = 'generateCouponCode';

            switch (id) {
                case 'gc-pin-number':
                    func = 'generatePinCode';
                    break;

                case 'gc-serial-number':
                    func = 'generateSerialNumber';
                    break;

                default:
                    func = 'generateCouponCode';
            }

            var couponCode = getFromPHPHelper(func);
            $('#' + id).closest(".code-field").find("input.code").val(couponCode);
        });
        //END Random code string maker

        // Toggle button
        $('.btn-toggle').off().on("click", function(e) {
            e.preventDefault();
            var node = $(this);
            var msg = $(this).data("confirm");
            if (!msg) {
                msg = "{{ trans('app.are_you_sure') }}";
            }

            if (node.attr('disabled')) {
                node.toggleClass('active');
                notie.alert(2, "{{ trans('messages.input_error') }}", 2);
                return;
            }

            if (node.hasClass('toggle-confirm')) {
                return new Promise(function(resolve, reject) {
                    $.confirm({
                        title: "{{ trans('app.confirmation') }}",
                        content: msg,
                        type: 'red',
                        buttons: {
                            'confirm': {
                                text: '{{ trans('app.proceed') }}',
                                keys: ['enter'],
                                btnClass: 'btn-red',
                                action: function() {
                                    notie.alert(4,
                                        "{{ trans('messages.confirmed') }}",
                                        2);
                                    proceedToggleActionFor(node);
                                }
                            },
                            'cancel': {
                                text: '{{ trans('app.cancel') }}',
                                action: function() {
                                    node.toggleClass('active');
                                    notie.alert(2,
                                        "{{ trans('messages.canceled') }}",
                                        2);
                                }
                            },
                        }
                    });
                });
            }

            proceedToggleActionFor(node);
        });

        function proceedToggleActionFor(node) {
            var doAfter = node.data('doafter');

            $.ajax({
                // url: node.attr('href'),
                url: node.data('link'),
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "_method": "PUT",
                },
                success: function(data) {
                    if (data == 'success') {
                        notie.alert(1, "{{ trans('responses.success') }}", 2);

                        if (doAfter == 'reload') {
                            window.location.reload();
                        }

                        // For toggle shop status on shop table
                        var tr = node.closest("tr");
                        if (tr.length == 1) {
                            tr.toggleClass('inactive');
                            node.children('i:first').toggleClass('fa-heart-o fa-heart');
                        }
                    } else {
                        notie.alert(3, "{{ trans('responses.failed') }}", 2);
                        node.toggleClass('active');
                    }
                },
                error: function(data) {
                    if (data.status == 403) {
                        notie.alert(2, "{{ trans('responses.denied') }}", 2);
                    } else if (data.status == 444) {
                        notie.alert(2, "{{ trans('messages.demo_restriction') }}", 5);
                    } else {
                        notie.alert(3, "{{ trans('responses.error') }}", 2);
                    }
                    node.toggleClass('active');
                }
            });
        }
        // END Toggle button

        // Toggle Congiguration widgets settings
        $('.toggle-widget').off().on("click", function(e) {
            e.preventDefault();

            var node = $(this);
            var box = node.closest(".box");
            var msg = $(this).data("confirm");
            if (!msg) {
                msg = "{{ trans('app.are_you_sure') }}";
            }

            if (node.hasClass('toggle-confirm')) {
                return new Promise(function(resolve, reject) {
                    $.confirm({
                        title: "{{ trans('app.confirmation') }}",
                        content: msg,
                        type: 'red',
                        buttons: {
                            'confirm': {
                                text: '{{ trans('app.proceed') }}',
                                keys: ['enter'],
                                btnClass: 'btn-red',
                                action: function() {
                                    notie.alert(4,
                                        "{{ trans('messages.confirmed') }}",
                                        2);
                                    proceedToggleActionFor(node);

                                    // Remove the removable box from UI
                                    if (box.length == 1 && box.hasClass(
                                            'removable')) {
                                        box.remove();
                                    }
                                }
                            },
                            'cancel': {
                                text: '{{ trans('app.cancel') }}',
                                action: function() {
                                    notie.alert(2,
                                        "{{ trans('messages.canceled') }}",
                                        2);
                                }
                            },
                        }
                    });
                });
            }

            proceedToggleActionFor(node);

            // Remove the removable box from UI
            if (box.length == 1 && box.hasClass('removable')) {
                box.remove();
            }
        });
        //End

        // Toggle Congiguration widgets settings
        $('.toggle-shop').on("click", function(e) {
            e.preventDefault();
            var node = $(this);
            proceedToggleActionFor(node);
        });
        //End

        //Ajax Form Submit
        $('.ajax-submit-btn').on("click", function(e) {
            return;
        });

        $('.ajax-form').submit(function(e) {
            e.preventDefault();
            //Return false and abort the action if the form validation failed
            if ($(this).find('input[type=submit]').hasClass('disabled')) {
                notie.alert(3, "{{ trans('responses.form_validation_failed') }}", 5);
                return;
            }

            apply_busy_filter();

            var action = this.action;
            var data = $(this).serialize();
            $.ajax({
                url: action,
                type: 'POST',
                data: data,
                success: function(data) {
                    $('#myDynamicModal').modal('hide');
                    remove_busy_filter();

                    if (data == 'success') {
                        notie.alert(1, "{{ trans('responses.success') }}", 3);
                    } else {
                        notie.alert(3, "{{ trans('responses.failed') }}", 3);
                        node.toggleClass('active');
                    }
                },
                error: function(data) {
                    $('#myDynamicModal').modal('hide');
                    remove_busy_filter();
                    if (data.status == 403) {
                        notie.alert(2, "{{ trans('responses.denied') }}", 3);
                    } else if (data.status == 444) {
                        notie.alert(2, "{{ trans('messages.demo_restriction') }}",
                            5);
                    } else {
                        notie.alert(3, "{{ trans('responses.error') }}", 3);
                    }
                    node.toggleClass('active');
                }
            });
        });
        // END Ajax Form Submit

        // Offer Price form
        var errHelp = '<div class="help-block with-errors"></div>';
        $('#offer_price').keyup(
            function() {
                var offerPrice = this.value;
                if (offerPrice !== "") {
                    $('#offer_start').attr('required', 'required');
                    $('#offer_end').attr('required', 'required');
                } else {
                    $('#offer_start').removeAttr('required');
                    $('#offer_end').removeAttr('required');
                }
            }
        );
        //END Offer Price form

        // Collapsible fieldset
        $(function() {
            $('fieldset.collapsible > legend').prepend(
                '<span class="btn-box-tool"><i class="fa fa-toggle-up"></i></span>');
            $('fieldset.collapsible > legend').click(function() {
                $(this).find('span i').toggleClass('fa-toggle-up fa-toggle-down');
                var $divs = $(this).siblings().toggle("slow");
            });
        });
        //END collapsible fieldset
    }
    //END App plugins

    //Mass selection and action section
    function initMassActions() {
        //Enable iCheck plugin for checkboxes
        //iCheck for checkbox and radio inputs
        $('tbody input[type="checkbox"]').each(function() {
            $(this).iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_flat-blue'
            });
        });

        //Enable check and uncheck all functionality
        $(".checkbox-toggle").on('click', function(e) {
            var clicks = $(this).data('clicks');
            var areaId = $(this).closest('table').children('tbody').attr('id');
            var massSelectArea = areaId ? "#" + areaId : "#massSelectArea";

            if (clicks) {
                $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
                unCheckAll(massSelectArea); //Uncheck all checkboxes
            } else {
                $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
                checkAll(massSelectArea); //Check all checkboxes
            }

            $(this).data("clicks", !clicks);
        });

        /**
         * Trigger the mass action functionality.
         * If the response has a 'download' property, call a function.
         */
        $('.massAction').on('click', function(e) {
            e.preventDefault();

            // if (!confirm('Are you sure?')) return false;

            var node = $(this);
            var doAfter = $(this).data('doafter');
            var msg = $(this).data("confirm");
            if (!msg) {
                msg = "{{ trans('app.are_you_sure') }}";
            }

            var allVals = [];
            $(".massCheck:checked").each(function() {
                allVals.push($(this).attr('id'));
            });

            if (allVals.length <= 0) {
                notie.alert(3, "{{ trans('responses.select_some_item') }}", 2);
            } else {
                return new Promise(function(resolve, reject) {
                    $.confirm({
                        title: "{{ trans('app.confirmation') }}",
                        content: msg,
                        type: 'red',
                        buttons: {
                            'confirm': {
                                text: '{{ trans('app.proceed') }}',
                                keys: ['enter'],
                                btnClass: 'btn-red',
                                action: function() {
                                    notie.alert(4,
                                        "{{ trans('messages.confirmed') }}",
                                        2);

                                    $.ajax({
                                        url: node.data('link'),
                                        type: 'POST',
                                        data: {
                                            "_token": "{{ csrf_token() }}",
                                            "ids": allVals,
                                        },
                                        success: function(data) {
                                            if (data[
                                                    'success'
                                                ]) {
                                                notie.alert(1,
                                                    data[
                                                        'success'
                                                    ], 2
                                                );
                                                switch (
                                                    doAfter) {
                                                    case 'reload':
                                                        window
                                                            .location
                                                            .reload();
                                                        break;
                                                    case 'remove':
                                                        $(".massCheck:checked")
                                                            .each(
                                                                function() {
                                                                    $(this)
                                                                        .parents(
                                                                            "tr"
                                                                        )
                                                                        .remove();
                                                                }
                                                            );
                                                        break;
                                                    default:
                                                        unCheckAll
                                                            (
                                                                "#massSelectArea"
                                                            ); //Uncheck all checkboxes
                                                }
                                            } else if (data[
                                                    'error']) {
                                                notie.alert(3,
                                                    data[
                                                        'error'
                                                    ], 2
                                                );
                                            } else if (data[
                                                    'download'
                                                ]) { // For downloading selected items
                                                notie.alert(1,
                                                    data[
                                                        'download'
                                                    ], 2
                                                );

                                                var downloadLink =
                                                    document
                                                    .createElement(
                                                        'a');
                                                downloadLink
                                                    .href = data
                                                    .download_url;
                                                downloadLink
                                                    .download =
                                                    data
                                                    .download_file_name;
                                                downloadLink
                                                    .click();
                                            } else {
                                                notie.alert(3,
                                                    "{{ trans('responses.failed') }}",
                                                    2);
                                            }
                                        },
                                        error: function(data) {
                                            if (data.status ==
                                                403) {
                                                notie.alert(2,
                                                    "{{ trans('responses.denied') }}",
                                                    3);
                                            } else if (data
                                                .status == 444
                                            ) {
                                                notie.alert(2,
                                                    "{{ trans('messages.demo_restriction') }}",
                                                    5);
                                            } else {
                                                notie.alert(3,
                                                    "{{ trans('responses.error') }}",
                                                    2);
                                            }
                                        }
                                    });
                                }
                            },
                            'cancel': {
                                text: '{{ trans('app.cancel') }}',
                                action: function() {
                                    // node.toggleClass('active');
                                    notie.alert(2,
                                        "{{ trans('messages.canceled') }}",
                                        2);
                                }
                            },
                        }
                    });
                });
            }
        });
    }

    function checkAll(selector = "#massSelectArea") {
        $(selector + " input[type='checkbox']").iCheck("check");
    }

    function unCheckAll(selector = "#massSelectArea") {
        $(selector + " input[type='checkbox']").iCheck("uncheck");
    }
    //End Mass selection and action section

    function apply_busy_filter(dom = 'body') {
        //Disable mouse pointer events and set the busy filter
        jQuery(dom).css("pointer-events", "none");
        jQuery('.loader').show();
        jQuery(".wrapper").addClass('blur-filter');
    }

    function remove_busy_filter(dom = 'body') {
        //Enable mouse pointer events and remove the busy filter
        jQuery(dom).css("pointer-events", "auto");
        jQuery(".wrapper").removeClass('blur-filter');
        jQuery('.loader').hide();
    }
    /*************************************
     *** END Initialise application plugins ***
     **************************************/

    function updateScroll(node = 'body') {
        var element = document.getElementById(node);
        element.scrollTop = element.scrollHeight;
    }

    /*
     * Get result from PHP helper functions
     *
     * @param  {str} funcName The PHP function name will be called
     * @param  {mix} args     arguments need to pass into the PHP function
     *
     * @return {mix}
     */
    function getFromPHPHelper(funcName, args = null) {
        var url = "{{ route('helper.getFromPHPHelper') }}";
        var result = 0;
        $.ajax({
            url: url,
            data: "funcName=" + funcName + "&args=" + args,
            async: false,
            success: function(v) {
                result = v;
            }
        });

        return result;
    }

    function copyToClipboard(element) {
        const accountNumber = element.previousElementSibling.value;
        const originalText = element.innerHTML;

        element.innerHTML = "{{ trans('app.copied') }}";

        navigator.clipboard.writeText(accountNumber).then(function() {
            setTimeout(function() {
                element.innerHTML = originalText;
            }, 1000); // Reset back to the original text after 1 second
        }).catch(function(error) {
            console.error('Copy failed: ', error);
            element.innerHTML = originalText; // Reset the text if copying fails
        });
    }

    // Shipping and digital item show hide on form of product with inventory
    if ($('input.requires_shipping').is(':checked') && $('input.downloadable').is(':not(:checked)')) {
        $('#form_shipping_section').show()
    } else {
        $('#form_shipping_section').hide()
    }

    if ($('input.downloadable').is(':checked')) {
        $('#downloadable_section').show();
    } else {
        $('#downloadable_section').hide();
    }

    $('input.downloadable').on('ifChanged', function() {
        var downloadableChecked = $('input.downloadable').is(':checked');

        if (downloadableChecked) {
            $('#downloadable_section').show();
        } else {
            $('#downloadable_section').hide();
        }
    });

    $('input.requires_shipping, input.downloadable').on('ifChanged', function() {
        var requiresShippingChecked = $('input.requires_shipping').is(':checked');
        var downloadableChecked = $('input.downloadable').is(':checked');

        if (requiresShippingChecked && !downloadableChecked) {
            $('#form_shipping_section').show();
        } else {
            $('#form_shipping_section').hide();
        }
    });
</script>
