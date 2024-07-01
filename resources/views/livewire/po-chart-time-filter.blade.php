<div>
    {{-- In work, do what you enjoy. --}}
    <div class="col-md-8">
        <div class="box">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                        <i class="icon ion-md-pulse hidden-sm"></i>
                        {{ trans('app.visitors_graph') }}
                    </div>
                </ul>
            <!-- /.nav .nav-tabs -->

            <div class="tab-content">
                <div class="tab-pane active" id="visitors_tab">
                    <div>{!! $chart->container() !!}</div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- /.nav-tabs-custom -->
    </div><!-- /.box -->
</div>
