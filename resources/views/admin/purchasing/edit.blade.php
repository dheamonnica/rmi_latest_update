@extends('admin.layouts.master')

@section('content')
  {!! Form::open(['route' => 'admin.purchasing.purchasing.store', 'data-toggle' => 'validator']) !!}

  @include('admin.purchasing._form')

  {!! Form::close() !!}
@endsection