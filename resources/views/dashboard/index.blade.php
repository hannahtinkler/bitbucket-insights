@extends('layouts.app')

@section('content')

<div class="jumbotron px-5 mb-5 mt-4">
  <h1 class="mx-3">Bitbucket Insights</h1>

  <refresh-data
    :refreshing="{{ (int) $settings->value('currently_refreshing') }}"
    last-refreshed="{{ $settings->value('last_refresh') }}"
  />
</div>

<div class="px-4">
  <div class="row mt-5">
    <div class="col-sm-12">
      <h2>Flagged Merges</h2>

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
      <h2>Recent merges</h2>
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
      <h2>Most merges</h2>
      <table class="table mt-4">
        <thead>
          <tr>
            <th class="border-top-0">Merger</th>
            <th class="border-top-0">Merged</th>
          </tr>
        </thead>
        <tbody>
          @foreach($mostMerges as $merger => $count)
            <tr>
              <td>{{ $merger }}</td>
              <td>{{ $count }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
