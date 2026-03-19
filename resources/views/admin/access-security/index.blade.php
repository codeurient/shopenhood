@extends('admin.layouts.app')

@section('title', 'Access Security')
@section('page-title', 'Access Security')

@section('content')
<div>

    {{-- ── Page Header ──────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#1A1A1A]">Access Security</h2>
        <p class="text-[#37474F] text-sm mt-1">Manage the secret URL path used to access this admin panel</p>
    </div>

    {{-- ── Flash ─────────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded flex items-start gap-2 text-[13px]">
            <i class="fa-solid fa-circle-check flex-shrink-0 mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded text-[13px]">
            <div class="flex items-center gap-2 mb-1">
                <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                <strong>Please fix the following errors:</strong>
            </div>
            <ul class="ml-6 list-disc space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- ── Override Warning ─────────────────────────────────────────── --}}
    @if($isOverriddenByEnv)
        <div class="mb-5 flex items-start gap-3 p-4 bg-amber-50 border border-amber-300 rounded text-[13px] text-amber-800">
            <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5 text-amber-500"></i>
            <div>
                <strong>ADMIN_PATH_OVERRIDE is active.</strong>
                The path is currently forced by the <code class="bg-amber-100 px-1 rounded">ADMIN_PATH_OVERRIDE</code>
                environment variable and cannot be changed from here.
                Remove <code class="bg-amber-100 px-1 rounded">ADMIN_PATH_OVERRIDE</code> from your <code class="bg-amber-100 px-1 rounded">.env</code> to enable dynamic management.
            </div>
        </div>
    @endif

    {{-- ── Current Path Status ──────────────────────────────────────── --}}
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
        <div class="px-4 py-3 border-b border-[#E0E0E0]">
            <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-[#D4AF37]"></i>
                Current Admin Path
            </h3>
        </div>
        <div class="p-5" x-data="{ revealed: false }">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-1 flex items-center gap-2 h-[34px] px-3 bg-[#F5F5F5] border border-[#E0E0E0] rounded font-mono text-[13px] text-[#1A1A1A]">
                    <span x-show="!revealed" class="tracking-widest text-[#37474F]">
                        {{ str_repeat('●', min(strlen($currentPath), 6)) }}{{ substr($currentPath, -4) }}
                    </span>
                    <span x-show="revealed" x-cloak>{{ $currentPath }}</span>
                </div>
                <button type="button" @click="revealed = !revealed"
                        class="inline-flex items-center gap-1.5 h-[34px] px-3 border border-[#E0E0E0] text-[#37474F] text-[12px] rounded hover:border-[#D4AF37] hover:text-[#D4AF37] transition">
                    <i class="fa-solid" :class="revealed ? 'fa-eye-slash' : 'fa-eye'"></i>
                    <span x-text="revealed ? 'Hide' : 'Reveal'"></span>
                </button>
                <button type="button" x-show="revealed" x-cloak
                        @click="navigator.clipboard.writeText('{{ $currentPath }}').then(() => { $el.innerHTML = '<i class=\'fa-solid fa-check\'></i> Copied'; setTimeout(() => $el.innerHTML = '<i class=\'fa-solid fa-copy\'></i> Copy', 2000) })"
                        class="inline-flex items-center gap-1.5 h-[34px] px-3 border border-[#E0E0E0] text-[#37474F] text-[12px] rounded hover:border-[#D4AF37] hover:text-[#D4AF37] transition">
                    <i class="fa-solid fa-copy"></i> Copy
                </button>
            </div>

            <div class="flex items-center gap-2 text-[12px] text-[#37474F]">
                <i class="fa-solid fa-link text-[10px]"></i>
                <span>Login URL:</span>
                <a href="{{ url($currentPath.'/login') }}" target="_blank"
                   class="text-[#D4AF37] hover:underline font-mono">
                    {{ url($currentPath.'/login') }}
                </a>
            </div>

            <div class="mt-3 flex items-center gap-2">
                @if($isManagedByDb)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#D4AF37]/10 text-[#B8960C] border border-[#D4AF37]/30">
                        <i class="fa-solid fa-database text-[9px]"></i> Database-managed
                    </span>
                @elseif($isOverriddenByEnv)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700 border border-amber-300">
                        <i class="fa-solid fa-lock text-[9px]"></i> Env override active
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#F5F5F5] text-[#37474F] border border-[#E0E0E0]">
                        <i class="fa-solid fa-file-lines text-[9px]"></i> .env default
                    </span>
                @endif
            </div>
        </div>
    </div>

    @unless($isOverriddenByEnv)

    {{-- ── Generate Random Path ─────────────────────────────────────── --}}
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
        <div class="px-4 py-3 border-b border-[#E0E0E0]">
            <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                <i class="fa-solid fa-shuffle text-[#D4AF37]"></i>
                Generate Random Path
                <span class="ml-1 text-[11px] font-normal text-[#37474F]">— Recommended</span>
            </h3>
        </div>
        <div class="p-5">
            <p class="text-[13px] text-[#37474F] mb-4">
                Instantly generate a cryptographically random 16-character path and activate it.
                You will be redirected to the new URL automatically.
            </p>
            <div class="flex items-start gap-3 p-3 bg-amber-50 border border-amber-200 rounded text-[12px] text-amber-800 mb-4">
                <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-0.5 text-amber-500"></i>
                <span>
                    <strong>Important:</strong> After generating, your current URL stops working immediately.
                    You will be redirected automatically, but <strong>bookmark the new URL</strong> before closing your browser.
                </span>
            </div>
            <form action="{{ route('admin.access-security.generate') }}" method="POST"
                  onsubmit="return confirm('Generate a new random path? You will be redirected to the new URL. The current path will stop working immediately.')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-shuffle text-xs"></i>
                    Generate &amp; Activate New Path
                </button>
            </form>
        </div>
    </div>

    {{-- ── Set Custom Path ──────────────────────────────────────────── --}}
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
        <div class="px-4 py-3 border-b border-[#E0E0E0]">
            <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-[#D4AF37]"></i>
                Set Custom Path
            </h3>
        </div>
        <div class="p-5">
            <p class="text-[13px] text-[#37474F] mb-4">
                Enter a custom path. Must be at least 8 characters. Allowed: letters, numbers,
                hyphens (<code class="bg-[#F5F5F5] px-1 rounded">-</code>),
                underscores (<code class="bg-[#F5F5F5] px-1 rounded">_</code>).
            </p>
            <form action="{{ route('admin.access-security.update') }}" method="POST"
                  onsubmit="return confirm('Change the admin path? You will be redirected to the new URL immediately.')">
                @csrf
                @method('PUT')
                <div class="flex items-start gap-3">
                    <div class="flex-1">
                        <input type="text" name="admin_path"
                               value="{{ old('admin_path') }}"
                               placeholder="e.g. my-secret-panel-2024"
                               minlength="8" maxlength="64"
                               class="h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 font-mono">
                        @error('admin_path')
                            <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#D4AF37] text-[#D4AF37] text-[13px] font-semibold rounded hover:bg-[#D4AF37] hover:text-[#000000] transition flex-shrink-0">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        Save Path
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Reset to .env ────────────────────────────────────────────── --}}
    @if($isManagedByDb)
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden mb-5">
        <div class="px-4 py-3 border-b border-[#E0E0E0]">
            <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                <i class="fa-solid fa-rotate-left text-[#37474F]"></i>
                Reset to .env Value
            </h3>
        </div>
        <div class="p-5">
            <p class="text-[13px] text-[#37474F] mb-4">
                Remove the database-managed path and fall back to the
                <code class="bg-[#F5F5F5] px-1 rounded">ADMIN_PATH</code> value in your
                <code class="bg-[#F5F5F5] px-1 rounded">.env</code> file
                (currently: <strong class="text-[#1A1A1A] font-mono">{{ env('ADMIN_PATH', 'admin') }}</strong>).
            </p>
            <form action="{{ route('admin.access-security.reset') }}" method="POST"
                  onsubmit="return confirm('Reset to .env path value? The database-managed path will be removed.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:border-red-400 hover:text-red-600 transition">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    Reset to .env
                </button>
            </form>
        </div>
    </div>
    @endif

    @endunless

    {{-- ── Emergency Recovery ───────────────────────────────────────── --}}
    <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <div class="px-4 py-3 border-b border-[#E0E0E0]">
            <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-[#D4AF37]"></i>
                Emergency Recovery
                <span class="ml-1 text-[11px] font-normal text-[#37474F]">— If you lose access</span>
            </h3>
        </div>
        <div class="p-5 space-y-4 text-[13px] text-[#37474F]">

            <div>
                <p class="font-semibold text-[#1A1A1A] mb-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#D4AF37] text-[#000000] text-[10px] font-bold mr-1">1</span>
                    Hard override via .env (fastest)
                </p>
                <p class="mb-2">Add this to your <code class="bg-[#F5F5F5] border border-[#E0E0E0] px-1 rounded">.env</code> file, then clear config cache:</p>
                <div class="bg-[#1A1A1A] text-[#E0E0E0] rounded p-3 font-mono text-[12px] space-y-1">
                    <div>ADMIN_PATH_OVERRIDE=<span class="text-[#D4AF37]">your-recovery-path</span></div>
                </div>
                <div class="bg-[#1A1A1A] text-[#E0E0E0] rounded p-3 font-mono text-[12px] mt-1">
                    php artisan config:clear
                </div>
                <p class="mt-1 text-[12px]">Access the panel, change the path, then remove <code class="bg-[#F5F5F5] border border-[#E0E0E0] px-1 rounded">ADMIN_PATH_OVERRIDE</code> from .env.</p>
            </div>

            <div class="border-t border-[#E0E0E0] pt-4">
                <p class="font-semibold text-[#1A1A1A] mb-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#D4AF37] text-[#000000] text-[10px] font-bold mr-1">2</span>
                    Artisan commands (from server)
                </p>
                <div class="bg-[#1A1A1A] text-[#E0E0E0] rounded p-3 font-mono text-[12px] space-y-1">
                    <div><span class="text-[#37474F]"># Show current path</span></div>
                    <div>php artisan admin:path</div>
                    <div class="mt-2"><span class="text-[#37474F]"># Generate a new random path</span></div>
                    <div>php artisan admin:path --generate</div>
                    <div class="mt-2"><span class="text-[#37474F]"># Set a specific path</span></div>
                    <div>php artisan admin:path --set=<span class="text-[#D4AF37]">my-secret-path</span></div>
                    <div class="mt-2"><span class="text-[#37474F]"># Reset to .env value</span></div>
                    <div>php artisan admin:path --reset</div>
                </div>
            </div>

            <div class="border-t border-[#E0E0E0] pt-4">
                <p class="font-semibold text-[#1A1A1A] mb-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#D4AF37] text-[#000000] text-[10px] font-bold mr-1">3</span>
                    Direct database query (last resort)
                </p>
                <div class="bg-[#1A1A1A] text-[#E0E0E0] rounded p-3 font-mono text-[12px] space-y-1">
                    <div><span class="text-[#37474F]"># Remove the DB-managed path (falls back to ADMIN_PATH in .env)</span></div>
                    <div>DELETE FROM settings WHERE <span class="text-[#D4AF37]">`key`</span> = 'admin.access_path';</div>
                </div>
                <p class="mt-1 text-[12px]">Then run <code class="bg-[#F5F5F5] border border-[#E0E0E0] px-1 rounded">php artisan cache:clear</code> to flush the cached path.</p>
            </div>

        </div>
    </div>

</div>
@endsection
