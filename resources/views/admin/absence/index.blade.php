@extends('admin.layouts.master')

@section('page-style')
    @include('plugins.ionic')
@endsection

@section('content')
    <div class="box border-small p-2">
        <div class="box-header with-border">
            <h3 class="box-title">ABSENCE DATA</h3>
            <div class="box-tools pull-right p-2">
            </div>

            <hr>

            <h5 style="font-weight: bold">Distance Check</h5>
            <span id="distance-result">Calculating Distance Radius ...</span>
            <p>Office Area: <span id="branch_loc">Loading...</span></p>

            <span class="spinner" id="calculation-spinner" style="width: 30px; height: 30px;"></span>

            <div id="output"></div>
            <div class="pull-right">
                <button class="btn btn-success btn-flat" id="clockInBtn">
                    <i class="fa fa-sign-in"></i>
                    CLOCK IN
                </button>
                <button class="btn btn-danger btn-flat" id="clockOutBtn">
                    <i class="fa fa-sign-out"></i>
                    CLOCK OUT
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="absence-tables">
                <thead>
                    <tr>
                        <th class="massActionWrapper">
                            <button type="button" class="btn btn-xs btn-default checkbox-toggle">
                                <i class="fa fa-square-o" data-toggle="tooltip" data-placement="top"
                                    title="{{ trans('app.select_all') }}"></i>
                            </button>
                        </th>
                        <th>{{ trans('app.form.name') }}</th>
                        <th>{{ trans('app.form.clock_in') }}</th>
                        <th>{{ trans('app.form.clock_out') }}</th>
                        <th>{{ trans('app.form.office') }}</th>
                        <th>{{ trans('app.form.address') }}</th>
                        <th>{{ trans('app.form.total_hours') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>
    </div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $('#clockInBtn').hide();
        $('#clockOutBtn').hide();

        // Function to calculate the distance between two points (Haversine formula)
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the Earth in kilometers
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Distance in kilometers
        }

        function checkIfUserHasClockIn() {
            console.log('checkIfUserHasClockIn');
            $.ajax({
                url: "{{ route('admin.admin.absence.checkIfUserHasClockIn') }}",
                method: "GET",
                success: function(data) {
                    console.log(data.success, 'checkIfUserHasClockIn data');
                    const res = data.success !== null ? true : false;
                    console.log(res, 'checkIfUserHasClockIn cek');
                    if (res) {
                        console.log('udh login')
                        $('#clockInBtn').prop('disabled', true);
                        $('#clockInBtn').show();
                        $('#clockOutBtn').show();
                    } else {
                        console.log('blm login')
                        $('#clockInBtn').prop('disabled', false);
                        $('#clockInBtn').show();
                        $('#clockOutBtn').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function checkIfUserHasClockOut() {
            console.log('checkIfUserHasClockOut');
            $.ajax({
                url: "{{ route('admin.admin.absence.checkIfUserHasClockOut') }}",
                method: "GET",
                success: function(data) {
                    console.log(data.success, 'checkIfUserHasClockOut data');
                    const res = data.success !== null ? true : false;
                    console.log(res, 'checkIfUserHasClockOut cek');
                    if (res) {
                        console.log('udh logout')
                        $('#clockOutBtn').prop('disabled', true);
                        $('#clockOutBtn').show();
                    } else {
                        console.log('blm logout')
                        $('#clockOutBtn').prop('disabled', false);
                        $('#clockOutBtn').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        $('#clockOutBtn').on('click', function() {
            console.log('clockOutBtn');

            $.ajax({
                url: "{{ route('admin.admin.absence.clockOut') }}", // Ensure this route is correct
                type: 'PUT', // Use the appropriate HTTP method (GET, POST, etc.)
                // data: {
                //     user_id: {{ Auth::user()->id }},
                //     branch_loc: branch_loc,
                //     longitude: longitude,
                //     latitude: latitude,
                //     address: address,
                // },
                success: function(
                    response
                ) {
                    // Handle success response
                    response,
                    location
                    .reload();
                },
                error: function(
                    xhr) {
                    console
                        .error(
                            'AJAX Error:',
                            xhr
                            .responseText
                        );
                }
            });
        });

        // Pass branch location from PHP to JavaScript
        const branchLongitude = @json($branch_loc->longitude);
        const branchLatitude = @json($branch_loc->latitude);

        // Check if coordinates are available
        if (branchLongitude && branchLatitude) {
            // Nominatim API for reverse geocoding
            const apiUrl =
                `https://nominatim.openstreetmap.org/reverse?lat=${branchLatitude}&lon=${branchLongitude}&format=json`;

            // Fetch the address
            $.getJSON(apiUrl, function(data) {

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLat = position.coords.latitude;
                        const userLon = position.coords.longitude;

                        // Calculate the distance
                        const distance = calculateDistance(branchLatitude, branchLongitude,
                            userLat,
                            userLon);
                        checkIfUserHasClockIn();
                        checkIfUserHasClockOut();
                        // const radius = 0.1; // Radius in kilometers
                        const radius = 10; // Radius in kilometers
                        const warehouse_name = {!! json_encode($branch_loc->warehouse_name) !!};

                        // Update the result in the DOM
                        if (distance <= radius) {
                            $('#distance-result').text('You are within the radius (' + distance
                                .toFixed(2) +
                                ' km)');

                            if (data && data.display_name) {
                                $('#branch_loc').text(warehouse_name + ", " + data
                                    .display_name);
                                $('#clockInBtn').on('click', function() {
                                    navigator.geolocation.getCurrentPosition(
                                        function(position) {
                                            const latitude = position.coords
                                                .latitude;
                                            const longitude = position.coords
                                                .longitude;
                                            const branch_loc =
                                                @json(Auth::user()->shop_id) !==
                                                null ?
                                                @json(Auth::user()->shop_id) :
                                                @json(Auth::user()->office_location_id);

                                            // Display coordinates
                                            // $('#output').text(
                                            //     `Fetching address for Latitude: ${latitude}, Longitude: ${longitude}`
                                            // );

                                            // Nominatim API URL
                                            const url =
                                                `https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`;

                                            $.getJSON(url, function(data) {
                                                const address = data
                                                    .display_name;
                                                // $('#output').html(
                                                //     `<p>Address: ${address}</p>`
                                                // );

                                                const clock_in = "{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}"; // Generate formatted datetime in PHP

                                                $.ajax({
                                                    url: "{{ route('admin.absence.store') }}", // Ensure this route is correct
                                                    type: 'POST', // Use the appropriate HTTP method (GET, POST, etc.)
                                                    data: {
                                                        user_id: {{ Auth::user()->id }},
                                                        branch_loc: branch_loc,
                                                        longitude: longitude,
                                                        latitude: latitude,
                                                        address: address,
                                                        clock_in: clock_in
                                                    },
                                                    success: function(
                                                        response
                                                    ) {
                                                        // Handle success response
                                                        response,
                                                        location
                                                        .reload();
                                                    },
                                                    error: function(
                                                        xhr) {
                                                        console
                                                            .error(
                                                                'AJAX Error:',
                                                                xhr
                                                                .responseText
                                                            );
                                                    }
                                                });
                                            }).fail(function(xhr, status,
                                                error) {
                                                console.error(
                                                    'Error fetching address:',
                                                    error);
                                                $('#output').text(
                                                    'Failed to retrieve address.'
                                                );
                                            });
                                        },
                                        function(error) {
                                            console.error("Error getting location:",
                                                error);
                                            $('#output').text(
                                                'Error getting location.');
                                        }
                                    );
                                });
                            } else {
                                $('#branch_loc').text('Address not found.');
                            }
                        } else {
                            $('#distance-result').text(
                                'You are beyond the allowed radius. Kindly move closer to your office area. (' +
                                distance
                                .toFixed(2) +
                                ' km)');
                            $('#branch_loc').text(warehouse_name + ", " + data.display_name);


                        }
                        $('#calculation-spinner').hide();

                    }
                );

            }).fail(function() {
                $('#branch_loc').text('Error retrieving address.');
            });
        } else {
            $('#branch_loc').text('Coordinates not available.');
        }
    });
</script>
