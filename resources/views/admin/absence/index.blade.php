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

            <input type="hidden" id="photoInput" name="img_clock_in">
            <input type="hidden" id="photoInputOut" name="img_clock_out">

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
                        <th>{{ trans('app.form.clock_in_img') }}</th>
                        <th>{{ trans('app.form.clock_out') }}</th>
                        <th>{{ trans('app.form.clock_out_img') }}</th>
                        <th>{{ trans('app.form.office') }}</th>
                        <th>{{ trans('app.form.address') }}</th>
                        <th>{{ trans('app.form.total_hours') }}</th>
                    </tr>
                </thead>
                <tbody id="massSelectArea">
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="clockInModal" tabindex="-1" aria-labelledby="clockInModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clockInModalLabel">Clock In Confirmation</h5>
                    </div>
                    <div class="modal-body">
                        <div style="position: relative; width: 100%; display: inline-block;">
                            <video id="video" style="width: 100%;" autoplay></video>
                            <i class="fa fa-camera"
                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 48px; color: white; opacity: 0.7; pointer-events: none;"></i>
                        </div>

                        <div class="p-3">
                            <div class="pull-right">
                                <button id="capture" class="btn btn-warning btn-flat">
                                    <i class="fa fa-camera"></i>
                                    Capture Photo</button>
                            </div>
                        </div>

                        <div id="resultPhoto">
                            Result:
                        </div>
                        <canvas id="canvas"></canvas>
                        <img id="photo" alt="Your Photo" style="display: none; width: 100%" name="img_clock_in" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelButtonClockIn">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmClockIn">Yes, Clock In</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="clockOutModal" tabindex="-1" aria-labelledby="clockInModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clockInModalLabel">Clock Out Confirmation</h5>
                    </div>
                    <div class="modal-body">
                        <div style="position: relative; width: 100%; display: inline-block;">
                            <video id="videoOut" style="width: 100%;" autoplay></video>
                            <i class="fa fa-camera"
                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 48px; color: white; opacity: 0.7; pointer-events: none;"></i>
                        </div>

                        <div class="p-3">
                            <div class="pull-right">
                                <button id="captureOut" class="btn btn-warning btn-flat">
                                    <i class="fa fa-camera"></i>
                                    Capture Photo</button>
                            </div>
                        </div>

                        <div id="resultPhotoOut">
                            Result:
                        </div>
                        <canvas id="canvasOut"></canvas>
                        <img id="photoOut" alt="Your Photo" style="display: none; width: 100%" name="img_clock_out" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelButtonClockOut">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmClockOut">Yes, Clock Out</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        $('#clockInBtn').hide();
        $('#clockOutBtn').hide();

        $('#cancelButtonClockIn').on('click', function() {
            // $('#clockInModal').css('display', 'none').attr('aria-hidden', 'true').removeClass('in');
            $('#clockInModal').hide();
            $('.modal-backdrop').remove(); // Remove any remaining backdrops
        });

        $('#cancelButtonClockOut').on('click', function() {
            $('#clockOutModal').hide();
            $('.modal-backdrop').remove(); // Remove any remaining backdrops
        });

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

                    if (!res) {
                        console.log('blm login dan blm logout')
                        $('#clockInBtn').prop('disabled', false);
                        $('#clockInBtn').show();

                        $('#clockOutBtn').prop('disabled', true);
                        $('#clockOutBtn').show();
                    } else {
                        if (data.success.clock_in != null && data.success.clock_out == null) {
                            console.log('udh login blm logout')
                            $('#clockInBtn').prop('disabled', true);
                            $('#clockInBtn').show();

                            $('#clockOutBtn').prop('disabled', false);
                            $('#clockOutBtn').show();
                        } else if (data.success.clock_in != null && data.success.clock_out !=
                            null) {
                            console.log('udh login udh logout')
                            $('#clockInBtn').prop('disabled', true);
                            $('#clockInBtn').show();

                            $('#clockOutBtn').prop('disabled', true);
                            $('#clockOutBtn').show();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        $('#clockOutBtn').on('click', function() {
            console.log('clockOutBtn');

            $('#clockOutModal').modal('show');
            $('#photoOut').hide();
            $('#canvasOut').hide();
            $('#resultPhotoOut').hide();

            const video = document.getElementById('videoOut');
            const canvas = document.getElementById('canvasOut');
            const photo = document.getElementById('photoOut');
            const photoInput = document.getElementById('photoInputOut');

            // Access user's camera
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => console.error("Camera access denied: ", err));

            // Capture photo
            document.getElementById('captureOut').addEventListener('click', () => {
                $('#videoOut').hide();
                $('#captureOut').hide();
                $('#resultPhotoOut').show();

                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convert to Base64 and show the photo
                const dataUrl = canvas.toDataURL('image/png');
                photo.src = dataUrl;
                photo.style.display = 'block';
                photoInput.value = dataUrl; // Send Base64 string to the server
                console.log(dataUrl, 'dataUrl');

                $('#confirmClockOut').on('click', function() {
                    $('#photoOut').show();
                    const clock_out =
                        "{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}"; // Generate formatted datetime in PHP
                    const absenceId =
                    "{{ Auth::user()->id }}"; // Generate formatted datetime in PHP
                    $.ajax({
                        url: "{{ route('admin.admin.absence.clockOut') }}", // Ensure this route is correct
                        type: 'PUT', // Use the appropriate HTTP method (GET, POST, etc.)
                        data: {
                            user_id: {{ Auth::user()->id }},
                            clock_out: clock_out,
                            clock_out_img: dataUrl // Send Base64 image string
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
                });
            });
        });

        function getPhoto(branch_loc, longitude, latitude, address, clock_in, warehouse_id) {
            $('#clockInModal').modal('show');
            $('#photo').hide();
            $('#canvas').hide();
            $('#resultPhoto').hide();

            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const photo = document.getElementById('photo');
            const photoInput = document.getElementById('photoInput');

            // Access user's camera
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => console.error("Camera access denied: ", err));

            // Capture photo
            document.getElementById('capture').addEventListener('click', () => {
                $('#video').hide();
                $('#capture').hide();
                $('#resultPhoto').show();

                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convert to Base64 and show the photo
                const dataUrl = canvas.toDataURL('image/png');
                photo.src = dataUrl;
                photo.style.display = 'block';
                photoInput.value = dataUrl; // Send Base64 string to the server
                console.log(dataUrl, 'dataUrl');

                $('#confirmClockIn').on('click', function() {
                    $('#photo').show();
                    $.ajax({
                        url: "{{ route('admin.absence.store') }}", // Ensure this route is correct
                        type: 'POST', // Use the appropriate HTTP method (GET, POST, etc.)
                        data: {
                            user_id: {{ Auth::user()->id }},
                            branch_loc: branch_loc,
                            warehouse_id: warehouse_id,
                            longitude: longitude,
                            latitude: latitude,
                            address: address,
                            clock_in: clock_in,
                            clock_in_img: dataUrl // Send Base64 image string
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
                });
            });
        }
        
        // if (Auth::user()->id !== 1) {
            // Pass branch location from PHP to JavaScript
            const branchLongitude = @json($branch_loc->longitude ?? null);
            const branchLatitude = @json($branch_loc->latitude ?? null);
            const warehouse_id= @json($warehouse->id ?? null);
            console.log(warehouse_id, 'warehouse_id')

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
                            // const radius = 0.1; // Radius in kilometers
                            const radius = 100; // Radius in kilometers
                            const warehouse_name = {!! json_encode($branch_loc->warehouse_name ?? null) !!};

                            // Update the result in the DOM
                            if (distance <= radius) {
                                checkIfUserHasClockIn();
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

                                                    const clock_in =
                                                        "{{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}"; // Generate formatted datetime in PHP

                                                    getPhoto(branch_loc,
                                                        longitude,
                                                        latitude, address,
                                                        clock_in, warehouse_id);
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
                                $('#clockInBtn').show();
                                $('#clockInBtn').prop('disabled', true);
                                $('#clockOutBtn').show();
                                $('#clockOutBtn').prop('disabled', true);
                            }
                            $('#calculation-spinner').hide();

                        }
                    );

                }).fail(function() {
                    $('#branch_loc').text('Error retrieving address.');
                });
            } else {
                $('#branch_loc').text('Coordinates not available or office area is not set.');
                $('#distance-result').hide();
                $('#calculation-spinner').hide();
            }
        // }
    });
</script>
