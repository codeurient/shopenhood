@extends('admin.layouts.app')


@section('content')
    <div class="container-fluid">
        dashboard

        <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:none;border:none;color:blue;cursor:pointer;text-decoration:underline;">
                Logout Admin
            </button>
        </form>
    </div>



    <style></style>
@endsection