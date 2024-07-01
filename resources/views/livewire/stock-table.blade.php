<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    <div class="col-md-6">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">
              {{ trans('app.open_tickets') }}
          </div>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th width="65%">{{ trans('app.subject') }}</th>
                <th>{{ trans('app.priority') }}</th>
                <th><i class="icon ion-md-chatbubbles"></i></th>
                <th>{{ trans('app.updated_at') }}</th>
              </tr>
            </thead>
            <tbody class="box-body">
              @forelse($open_tickets->take(5) as $ticket)
                <tr>
                  <td>
                    <span class="label label-outline"> {{ $ticket->category->name }} </span>
                    <p class="indent5">
                      <a href="{{ route('admin.support.ticket.show', $ticket->id) }}">{{ $ticket->subject }}</a>
                    </p>
                  </td>
                  <td>{!! $ticket->priorityLevel() !!}</td>
                  <td><span class="label label-default">{{ $ticket->replies_count }}</span></td>
                  <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3">{{ trans('app.no_data_found') }}</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
</div>
