@extends('admin.layouts.app')

@section('title', 'Create Policy')
@section('page-title', 'Legal & Policies')

@section('content')
<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.legal.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Legal & Policies
            </a>
            <h2 class="text-2xl font-bold text-[#1A1A1A]">Create Policy</h2>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded flex items-start gap-2">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <ul class="text-[13px] space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.legal.store') }}" method="POST" class="space-y-5">
        @csrf

        @include('admin.legal._form', ['policy' => null])

        <div class="flex justify-between">
            <a href="{{ route('admin.legal.index') }}"
               class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-plus text-xs"></i>
                Create Policy
            </button>
        </div>
    </form>

</div>
@endsection
