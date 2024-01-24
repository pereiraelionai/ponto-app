@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <login-component csrf_token="{{ @csrf_token() }}"></login-component>
    </div>
</div>
@endsection
