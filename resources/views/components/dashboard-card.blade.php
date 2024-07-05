<div class="col-md-3 stretch-card grid-margin">
    <div class="card bg-gradient-{{ $options['color'] ?? 'danger'}} card-img-holder text-white">
        <div class="card-body">
            <img src="/images/circle.svg" class="card-img-absolute" alt="circle-image"></img>
            <h4 class="font-weight-normal mb-3">{{ $options['name'] ?? '-'}} <i class="icon ion-md-{{ $options['icon'] ?? 'circle'}} float-right"></i>
            </h4>

            <h2 class="mb-5"> {{ $options['is_currency'] ? (get_formated_currency(intval($count) ?? 0, 0, config('system_settings.currency.id'))) : ($count ?? 0)}} </h2>
            <h6 class="card-text">
                &nbsp;
                @if(isset($options['is_count_plus']))
                    @if ($options['is_count_plus'])    
                        <i class="icon ion-md-add"></i> {{ $count2 ?? 0 }} {{ $options['count_plus_name'] ?? '-' }}
                    @endif
                @endif
            </h6>
        </div>
    </div>
</div>