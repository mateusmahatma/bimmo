@extends('layouts.app')

@section('body')
<div class="container-fluid py-3">
    <div class="row">
        <aside class="col-md-2 bg-light border-end">
            @include('layouts.sidebar')
        </aside>

        <main class="col-md-10">
            @yield('container')
        </main>
    </div>
</div>
@endsection