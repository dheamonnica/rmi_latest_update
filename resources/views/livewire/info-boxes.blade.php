<div>
    {{-- Do your work, then step back. --}}
    <!-- Info boxes -->
  <div class="row">
    <div class="col-md-8 col-sm-7 col-xs-12">
      <div class="row">
        <div class="col-sm-6 col-xs-12 nopadding-right">
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="icon ion-md-filing"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ trans('app.pending_verifications') }}</span>
              <span class="info-box-number">
                {{ $pending_verifications }}
                <a href="{{ route('admin.vendor.shop.verifications') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('app.take_action') }}">
                  <i class="icon ion-md-paper-plane"></i>
                </a>
              </span>

              <div class="progress">
                <div class="progress-bar bg-warning" style="width: 0;"></div>
              </div>

              <span class="progress-description">
                <i class="icon ion-md-hourglass"></i>
                {{ trans_choice('messages.pending_verifications', $pending_verifications, ['count' => $pending_verifications]) }}
              </span>
            </div><!-- /.info-box-content -->
          </div>
        </div>

        <div class="col-sm-6 col-xs-12 px-2">
          <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="icon ion-md-pulse"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ trans('app.pending_approvals') }}</span>
              <span class="info-box-number">
                {{ $pending_approvals }}
                <a href="{{ route('admin.vendor.shop.index') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('app.take_action') }}">
                  <i class="icon ion-md-paper-plane"></i>
                </a>
              </span>

              <div class="progress">
                <div class="progress-bar bg-info" style="width: 0;"></div>
              </div>

              <span class="progress-description">
                <i class="icon ion-md-hourglass"></i>
                {{ trans_choice('messages.pending_approvals', $pending_approvals, ['count' => $pending_approvals]) }}
              </span>
            </div><!-- /.info-box-content -->
          </div>
        </div>
      </div>
    </div><!-- /.col-*-* -->

    <div class="col-md-4 col-sm-5 col-xs-12 nopadding-left">
      <div class="info-box bg-red">
        <span class="info-box-icon"><i class="icon ion-md-megaphone"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">{{ trans('app.appealed_disputes') }}</span>
          <span class="info-box-number">
            {{ $dispute_count }}
            <a href="{{ route('admin.support.dispute.index') }}" class="pull-right" data-toggle="tooltip" data-placement="left" title="{{ trans('app.take_action') }}">
              <i class="icon ion-md-paper-plane"></i>
            </a>
          </span>

          @php
            $last_months = $last_60days_dispute_count - $last_30days_dispute_count;
            $difference = $last_30days_dispute_count - $last_months;
            $last_30_days_percents = $last_months == 0 ? 100 : round(($difference / $last_months) * 100);
          @endphp
          <div class="progress">
            <div class="progress-bar bg-danger" style="width: 0;"></div>
          </div>

          <span class="progress-description">
            <i class="icon ion-md-arrow-{{ $difference > 0 ? 'up' : 'down' }}"></i>
            {{ trans('messages.last_30_days_percents', ['percent' => $last_30_days_percents, 'state' => $difference > 0 ? trans('app.increase') : trans('app.decrease')]) }}
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
    </div>
    <!-- /.col-*-* -->
  </div>
</div>
