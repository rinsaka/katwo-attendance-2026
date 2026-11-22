@extends('layouts.admin-2027')

{{-- @inject('myController', 'App\Http\Controllers\Controller') --}}

@section('content')
<main class="container-md">
    @include('layouts.flash')

    <div class="card my-3 border-0 shadow-lg">
        <div class="card-header bg-primary text-white h5 mb-0">
            活動予定一覧（管理者モード）
        </div>

    </div>
</main>

@endsection
