<div class="col-sm-3 col-xs-12 pt-5">
    <div class="info-box bg-{{ $options['bg-color'] ?? 'yellow'}}">
      <span class="info-box-icon"><i class="icon ion-md-{{ $options['icon'] ?? 'filling'}}"></i></span>

      <div class="info-box-content">
        <span class="info-box-text">{{ $options['name'] ?? '-' }}</span>
        <span class="info-box-number">
          <h3>{{ $count }}</h3>
          <a href="{{ route($options['route'] ?? 'admin.vendor.shop.verifications') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ $options['action'] ?? trans('app.take_action') }}">
            {{-- <i class="icon ion-md-paper-plane"></i> --}}
          </a>
        </span>
      </div><!-- /.info-box-content -->
    </div>
  </div>