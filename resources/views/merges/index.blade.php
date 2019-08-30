@extends('layouts.app')

@section('content')

<div class="jumbotron px-5 mb-5">
  <h1 class="mx-3 mb-4">Merged Pull Requests</h1>

  <refresh-data
    :refreshing="{{ (int) $settings->value('currently_refreshing') }}"
    last-refreshed="{{ $settings->value('last_refresh') }}"
  />
</div>

<div class="px-4">
  <div class="row mt-5">
    <div class="col-sm-12">
      <h2 class="d-flex justify-content-between">
        Flagged merges

        <a href="{{ route('merges.all', 'not-ready-for-merge') }}">
          <small><i class="fa fa-arrow-right"></i></small>
        </a>
      </h2>

      @if($flaggedMerges->isNotEmpty())
        <table class="table mt-4">
          <thead>
            <tr>
              <th class="border-top-0">Pull request</th>
              <th class="border-top-0">Author</th>
              <th class="border-top-0">Merger</th>
              <th class="border-top-0">Approvals</th>
            </tr>
          </thead>
          <tbody>
            @foreach($flaggedMerges as $pullRequest)
              <tr>
                <td>
                  <a href="{{ $pullRequest['links']['html']['href'] }}" target="_blank">
                    {{ str_limit($pullRequest['title'], 50) }}
                  </a>
                </td>
                <td>{{ $pullRequest['author']['display_name'] }}</td>
                <td>{{ $pullRequest['closed_by']['display_name'] }}</td>
                <td>{{ count($pullRequest['approvals']) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p>Nothing to show!</p>
      @endif
    </div>
  </div>

  <div class="row pt-5">
    <div class="col-sm-12 col-lg-8">
      <h2 class="d-flex justify-content-between">
        Recent merges

        <a href="{{ route('merges.all', 'recent') }}">
          <small><i class="fa fa-arrow-right"></i></small>
        </a>
      </h2>

      <table class="table mt-4">
        <thead>
          <tr>
            <th class="border-top-0">Pull request</th>
            <th class="border-top-0">Author</th>
            <th class="border-top-0">Merger</th>
          </tr>
        </thead>
        <tbody>
          @foreach($recentMerges as $pullRequest)
            <tr>
              <td>
                <a href="{{ $pullRequest['links']['html']['href'] }}" target="_blank">
                  {{ str_limit($pullRequest['title'], 50) }}
                </a>
              </td>
              <td>{{ $pullRequest['author']['display_name'] }}</td>
              <td>{{ $pullRequest['closed_by']['display_name'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="col-sm-12 col-lg-4">
      <h2 class="d-flex justify-content-between">
        Most merges

        <a href="{{ route('merges.all', 'mergers') }}">
          <small><i class="fa fa-arrow-right"></i></small>
        </a>
      </h2>

      <table class="table mt-4">
        <thead>
          <tr>
            <th class="border-top-0">Merger</th>
            <th class="border-top-0 text-right">Merged</th>
          </tr>
        </thead>
        <tbody>
          @foreach($mostMerges as $merger => $count)
            <tr>
              <td>{{ $merger }}</td>
              <td class="text-right">{{ $count }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
