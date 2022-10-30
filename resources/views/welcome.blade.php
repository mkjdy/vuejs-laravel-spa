@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
@endsection

@section('content')
    <router-view />
@endsection

@section('scripts')
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    <script src="{{ mix('js/app.js') }}" type="text/javascript"></script>
@endsection