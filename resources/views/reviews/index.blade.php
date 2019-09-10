@extends('layouts.app')

@section('content')

<div class="jumbotron px-5 mb-5">
  <h1 class="mx-3 mb-4">Open Pull Requests</h1>

  <refresh-data
    :refreshing="{{ (int) $settings->value('currently_refreshing') }}"
    last-refreshed="{{ $settings->value('last_refresh') }}"
  />
</div>

<div class="px-4">
  <div class="row mt-5">
    <div class="col-sm-12 col-md-8">
      <h2 class="d-flex justify-content-between">
        Require more reviews

        <a href="{{ route('reviews.all', 'not-ready-for-merge') }}">
          <small><i class="fa fa-arrow-right"></i></small>
        </a>
      </h2>

      @if($notReadyForMerge->isNotEmpty())
        <table class="table mt-4">
          <thead>
            <tr>
              <th class="border-top-0">Pull request</th>
              <th class="border-top-0">Author</th>
            </tr>
          </thead>
          <tbody>
            @foreach($notReadyForMerge as $pullRequest)
              <tr>
                <td>
                  <div class="d-flex justify-content-between">
                    <a href="{{ $pullRequest->url }}?w=1" target="_blank">
                      {{ str_limit($pullRequest->title, 50) }}
                    </a>

                    <span class="mx-3">
                      @if($pullRequest->comment_count)
                        <i class="fa fa-comment ml-1"></i>
                      @endif
                      @if($pullRequest->task_count)
                        <i class="fa fa-check-square ml-1"></i>
                      @endif
                    </span>
                  </div>
                </td>
                <td>{{ $pullRequest->author->name }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
    <div class="col-sm-12 col-lg-4">
      <h2 class="d-flex justify-content-between">
        Most reviews

        <a href="{{ route('reviews.all', 'reviewers') }}">
          <small><i class="fa fa-arrow-right"></i></small>
        </a>
      </h2>
      <table class="table mt-4">
        <thead>
          <tr>
            <th class="border-top-0">Reviewer</th>
            <th class="border-top-0 text-right">Count</th>
          </tr>
        </thead>
        <tbody>
          @foreach($mostReviewed as $reviewer => $prs)
            <tr>
              <td>{{ $reviewer }}</td>
              <td class="text-right">{{ $prs }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="row mt-5">
    <div class="col-sm-12">
      <h2 class="d-flex justify-content-between">
        Ready for merge

        <a href="{{ route('reviews.all', 'ready-for-merge') }}">
          <small><i class="fa fa-arrow-right"></i></small>
        </a>
      </h2>

      @if($readyForMerge->isNotEmpty())
        <table class="table mt-4">
          <thead>
            <tr>
              <th class="border-top-0">Pull request</th>
              <th class="border-top-0">Author</th>
            </tr>
          </thead>
          <tbody>
            @foreach($readyForMerge as $pullRequest)
              <tr>
                <td>
                  <div class="d-flex justify-content-between">
                    <a href="{{ $pullRequest->url }}?w=1" target="_blank">
                      {{ str_limit($pullRequest->title, 30) }}
                    </a>

                    <span class="ml-3">
                      @if($pullRequest->comment_count)
                        <i class="fa fa-comment ml-1"></i>
                      @endif
                      @if($pullRequest->task_count)
                        <i class="fa fa-check-square ml-1"></i>
                      @endif
                    </span>
                  </div>
                </td>
                <td>{{ $pullRequest->author->name }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p>Nothing to show!</p>
      @endif
    </div>
  </div>
</div>

@endsection
