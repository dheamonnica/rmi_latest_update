@extends('admin.layouts.master')

@section('content')
  {!! Form::open(['route' => 'admin.purchasing.purchasing.store','files' => true, 'id' => 'form-ajax-upload','data-toggle' => 'validator']) !!}

  @include('admin.purchasing._form')

  {!! Form::close() !!}
@endsection


@section('page-script')
<script language="javascript" type="text/javascript">

$('body').on('change', '.itemQtt', function() {
	var itemId = $(this).closest('tr').attr('id');
	calculateTransferTotal();
	// calculateItemTotal(itemId);
});

$('body').on('click', '.deleteThisRow', function() {
	var itemId = $(this).closest('tr').attr('id');
	deleteThisRow(itemId);
});

$('#add-to-request-btn').click(
	function() {

		var ID = $("#product-to-request").select2('data')[0].id;

    var itemDescription = $("#product-to-request").select2('data')[0].text;

    if (ID == '' || itemDescription == '') {
      return false;
    }

    $("#product-to-request").select2("val", ""); 

    if ($("tr#" + ID).length) {
      increaseQttByOne(ID);
      return false;
    }

    var numOfRows = $("tbody#items tr").length;

    var node = '<tr id="' + ID + '">' + ID +
      '<td class="nopadding-right" width="75%">' + itemDescription +
      '<input type="hidden" name="product[' + numOfRows + '][product_id]" value="' + ID + '"></input>' +
      '<input type="hidden" name="product[' + numOfRows + '][item_description]" value="' + itemDescription + '"></input>' +
      '<td class="nopadding-right text-center" width="10%">' +
      '<span>request qty</span>' +
      '<input name="product[' + numOfRows + '][quantity]" value="1" type="number" id="qtt-' + ID + '" class="form-control itemQtt no-border" placeholder="{{ trans('app.quantity') }}" required>' +
      '</td>' +
      '<td class="small" width="10%"><i class="fa fa-trash text-muted deleteThisRow" data-toggle="tooltip" data-placement="left" title="{{ trans('help.remove_this_product_item') }}"></i></td>' +
      '</tr>';

      $('tbody#items').append(node);

      calculateTransferTotal();

      return false; //Return false to prevent unspected form submition
	}
);

  function calculateTransferTotal() {
    var sumQty = 0;
    var sumItem = 0;

    $(".itemQtt").each(
      function() {
        sumQty += ($(this).val()) * 1;
  sumItem ++;
      }
    );

    $("#summary-qty-total").text(sumQty + ' ' + (sumItem > 1 ? 'Pcs' : 'Pc'));
    $("#summary-product-total").text(sumItem + ' ' + (sumItem > 1 ? 'Items' : 'Item'));

    return false;
  };

	function increaseQttByOne(ID) {
        var qtt = $("#qtt-" + ID).val();
        $("#qtt-" + ID).val(++qtt);

		calculateTransferTotal();
        return false;
  };

      	      // Remove table rows
  function deleteThisRow(ID) {
        $("tr#" + ID).remove();
        if ($("tbody#items tr").length <= 1) {
          $("#empty-cart").show(); // Show the empty cart message
        }

    calculateTransferTotal();
        return false;
  };
</script>
@endsection