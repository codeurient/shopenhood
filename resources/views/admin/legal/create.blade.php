@extends('admin.layouts.app')

@section('title', 'Create Policy')
@section('page-title', 'Legal & Policies')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.legal.index') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-2xl font-bold text-gray-900">Create Policy</h2>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.legal.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('admin.legal._form', ['policy' => null])

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 font-medium transition">
                Create Policy
            </button>
            <a href="{{ route('admin.legal.index') }}" class="px-6 py-3 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>

</div>
@endsection
