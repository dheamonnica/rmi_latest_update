@extends('theme::layouts.main')

@section('content')
  <!-- CATEGORY COVER IMAGE -->
  @include('theme::banners.category_cover', ['category' => $categorySubGroup])

  <!-- HEADER SECTION -->
  @include('theme::headers.category_sub_group_page', ['category' => $categorySubGroup])

  <!-- CONTENT SECTION -->
  products: {{$products}} - {{Auth::guard('customer')->user()->shop_id}}
  @include('theme::contents.category_page', ['category' => $categorySubGroup])

  <!-- BROWSING ITEMS -->
  @include('theme::sections.recent_views')

  <!-- bottom Banner -->
  @include('theme::banners.bottom')
@endsection
