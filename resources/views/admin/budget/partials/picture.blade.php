<td>
    <!-- Trigger link for the popup -->
    <a href="#popup-{{$budget->id}}">
        <img src="{{ get_storage_file_url(optional($budget->image)->path, 'cover_thumb') }}" class="thumbnail">
    </a>

    <!-- Popup container -->
    <div id="popup-{{$budget->id}}" class="popup">
        <!-- Close button -->
        <a href="#" class="close">&times;</a>
        <!-- Image to display -->
        <img src="{{ get_storage_file_url(optional($budget->image)->path, 'medium') }}">
    </div>
</td>

<style>
    /* Basic styling for the popup */
    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        padding: 20px;
        border-radius: 8px;
        z-index: 9999;
        text-align: center;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        /* max-width: 90%; */
        /* max-height: 90%; */
        overflow: auto;
    }

    /* Show the popup when the anchor tag is targeted */
    .popup:target {
        display: block;
    }

    /* Centering the image within the popup */
    .popup img {
        display: block;
        margin: 0 auto;
        max-width: 100%;
        max-height: 100%;
    }

    /* Styling for the close button */
    .popup .close {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #fff;
        font-size: 20px;
        text-decoration: none;
    }

    .popup .close:hover {
        color: #ccc;
    }
</style>
