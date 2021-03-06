@extends('layouts.app')

@section('content')

<div class="jumbotron px-5 mb-5">
  <h1 class="mx-3 mb-4">{{ $type }} Pull Requests</h1>

  <refresh-data
    :refreshing="{{ (int) $settings->value('currently_refreshing') }}"
    last-refreshed="{{ $settings->value('last_refresh') }}"
  />
</div>

<div class="px-4">
  <div class="row mt-5">
    <div class="col-sm-12">
      <h2>{{ $title }}</h2>

      @if($data->isEmpty())
        <p class="mt-4">Nothing to show!</p>
      @else
        <table class="table mt-4">
          @if(is_scalar($data->first()))

            <thead>
              <tr>
                <th class="border-top-0">User</th>
                <th class="border-top-0 text-right">Count</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data as $rankable => $rank)
                <tr>
                  <td>{{ $rankable }}</td>
                  <td class="text-right">{{ $rank }}</td>
                </tr>
              @endforeach
            </tbody>

          @else

            <thead>
              <tr>
                <th class="border-top-0">Pull request</th>
                <th class="border-top-0">Author</th>

                @if($data->first()->mergedBy)
                  <th class="border-top-0">Merger</th>
                @endif
                <th class="border-top-0 text-right">Approvals</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data as $pullRequest)
                <tr>
                  <td>
                    <div class="d-flex justify-content-between">
                      <a href="{{ $pullRequest->url }}?w=1" target="_blank">
                        {{ str_limit($pullRequest->title, 50) }}
                      </a>

                      <span class="mx-3">
                        @php($approvers = $pullRequest->approvals->map(function ($approval) {
                          return $approval->teamMember->name;
                        }))

                        @if($pullRequest->comment_count)
                          <i class="fa fa-comment ml-1"></i>
                        @endif
                        @if($pullRequest->task_count)
                          <i class="fa fa-check-square ml-1"></i>
                        @endif
                        @if($approvers->contains(auth()->user()->name))
                          <i class="fa fa-thumbs-up ml-1" title="You've approved this one already!"></i>
                        @endif
                      </span>
                    </div>
                  </td>
                  <td>{{ $pullRequest->author->name }}</td>
                  @if($data->first()->mergedBy)
                    <td>{{ $pullRequest->mergedBy->name }}</td>
                  @endif
                  <td class="text-right">{{ count($pullRequest['approvals']) }}</td>
                </tr>
              @endforeach
            </tbody>

          @endif
        </table>
      @endif
    </div>
  </div>
</div>

@endsection
