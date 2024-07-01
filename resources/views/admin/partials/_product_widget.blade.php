<div class="admin-user-widget">
  <span class="admin-user-widget-img">
    <img src="{{ get_catalog_featured_img_src($product, 'small') }}" class="thumbnail" alt="{{ trans('app.image') }}">
  </span>

  <div class="admin-user-widget-content">
    <span class="admin-user-widget-title">
      {{ $product->name }}
    </span>

    <span class="admin-user-widget-text text-muted">
      {{ trans('app.form.manufacture_skuid') . ': ' . $product->manufacture_skuid }}
    </span>

    <span class="admin-user-widget-text text-muted">
      {{-- {{ $product->gtin_type . ': ' . $product->gtin }} --}}
      {{ trans('app.form.selling_skuid') . ': ' . $product->selling_skuid }}
    </span>

    <span class="admin-user-widget-text text-muted">
      {{ trans('app.form.client_skuid') . ': ' . $product->client_skuid }}
    </span>

    <span class="admin-user-widget-text text-muted">
      {{ trans('app.manufacturer') . ': ' . optional($product->manufacturer)->name }}
      <i class="fa fa-check-square-o pull-right" style="position: absolute; right: 30px; top: 90px; font-size: 40px; color: rgba(0, 0, 0, 0.2);"></i>
    </span>
  </div> <!-- /.admin-user-widget-content -->
</div>
