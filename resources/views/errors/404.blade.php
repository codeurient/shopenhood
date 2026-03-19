<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found · {{ config('app.name', 'Shopenhood') }}</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        luxury: {
                            black:    '#000000',
                            surface:  '#1A1A1A',
                            charcoal: '#37474F',
                            gold:     '#D4AF37',
                            light:    '#E0E0E0',
                        }
                    },
                    fontFamily: { sans: ['Inter', 'Segoe UI', 'sans-serif'] },
                    animation: {
                        'fade-up':    'fadeUp 0.7s ease forwards',
                        'fade-in':    'fadeIn 0.9s ease forwards',
                        'pulse-gold': 'pulseGold 3s ease-in-out infinite',
                        'float':      'float 5s ease-in-out infinite',
                        'scan':       'scan 4s linear infinite',
                        'shimmer':    'shimmer 2.5s linear infinite',
                    },
                    keyframes: {
                        fadeUp:    { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        fadeIn:    { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        pulseGold: { '0%, 100%': { opacity: '0.12' }, '50%': { opacity: '0.35' } },
                        float:     { '0%, 100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-8px)' } },
                        scan:      { '0%': { top: '0%' }, '100%': { top: '100%' } },
                        shimmer:   { '0%': { backgroundPosition: '-200% center' }, '100%': { backgroundPosition: '200% center' } },
                    },
                }
            }
        }
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; overflow: hidden; }

        .text-gold-shimmer {
            background: linear-gradient(90deg, #D4AF37 0%, #F5E070 40%, #D4AF37 60%, #A07C10 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 2.5s linear infinite;
        }

        .glitch { position: relative; }
        .glitch::before, .glitch::after {
            content: attr(data-text);
            position: absolute; inset: 0;
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glitch::before {
            background-image: linear-gradient(90deg, #D4AF37, #F5E070, #D4AF37);
            background-size: 200% auto;
            animation: shimmer 2.5s linear infinite, glitchTop 3.5s infinite;
            clip-path: polygon(0 0, 100% 0, 100% 38%, 0 38%);
            transform: translate(-3px, 0); opacity: 0.7;
        }
        .glitch::after {
            background-image: linear-gradient(90deg, #A07C10, #D4AF37, #A07C10);
            background-size: 200% auto;
            animation: shimmer 2.5s linear infinite reverse, glitchBot 3.5s infinite;
            clip-path: polygon(0 62%, 100% 62%, 100% 100%, 0 100%);
            transform: translate(3px, 0); opacity: 0.7;
        }
        @keyframes glitchTop {
            0%, 90%, 100% { transform: translate(-3px, 0); }
            92% { transform: translate(3px, -2px); }
            94% { transform: translate(-3px, 2px); }
            96% { transform: translate(3px, 0); }
        }
        @keyframes glitchBot {
            0%, 90%, 100% { transform: translate(3px, 0); }
            92% { transform: translate(-3px, 2px); }
            94% { transform: translate(3px, -2px); }
            96% { transform: translate(-3px, 0); }
        }

        .dot-grid {
            background-image: radial-gradient(circle, #37474F 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Corner brackets */
        .bracket { position: relative; }
        .bracket::before {
            content: '';
            position: absolute; top: 0; left: 0;
            width: 14px; height: 14px;
            border-top: 1.5px solid #D4AF37;
            border-left: 1.5px solid #D4AF37;
        }
        .bracket::after {
            content: '';
            position: absolute; bottom: 0; right: 0;
            width: 14px; height: 14px;
            border-bottom: 1.5px solid #D4AF37;
            border-right: 1.5px solid #D4AF37;
        }
        .bracket-extra::before { border-right: 1.5px solid #D4AF37; right: 0; left: auto; }

        .scan-container { position: relative; overflow: hidden; }
        .scan-line {
            position: absolute; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
            animation: scan 4s linear infinite;
            opacity: 0.4;
        }

        .d1 { animation-delay: 0.05s; animation-fill-mode: both; }
        .d2 { animation-delay: 0.15s; animation-fill-mode: both; }
        .d3 { animation-delay: 0.25s; animation-fill-mode: both; }
        .d4 { animation-delay: 0.35s; animation-fill-mode: both; }
        .d5 { animation-delay: 0.45s; animation-fill-mode: both; }
        .d6 { animation-delay: 0.55s; animation-fill-mode: both; }
        .init0 { opacity: 0; }
    </style>
</head>
<body class="bg-luxury-black font-sans h-screen flex flex-col dot-grid overflow-hidden">

    {{-- Radial glow --}}
    <div class="fixed inset-0 pointer-events-none" aria-hidden="true">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full animate-pulse-gold"
             style="background: radial-gradient(circle, rgba(212,175,55,0.16) 0%, transparent 70%);"></div>
    </div>

    {{-- Header --}}
    <header class="relative z-10 flex items-center justify-between px-5 py-3 border-b border-luxury-charcoal/40 flex-shrink-0">
        <a href="{{ url('/') }}" class="flex items-center gap-2 group">
            <span class="w-7 h-7 rounded flex items-center justify-center border border-luxury-gold/40 group-hover:border-luxury-gold transition-colors duration-300">
                <i class="fa-solid fa-store text-luxury-gold text-xs"></i>
            </span>
            <span class="text-luxury-light text-[12px] font-semibold tracking-widest uppercase group-hover:text-luxury-gold transition-colors duration-300">
                {{ config('app.name', 'Shopenhood') }}
            </span>
        </a>
        <div class="flex items-center gap-1.5 text-luxury-charcoal text-[10px] tracking-widest uppercase">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse inline-block"></span>
            <span>Error 404</span>
        </div>
    </header>

    {{-- Main --}}
    <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-6 text-center min-h-0">

        {{-- Two-column layout on wider screens, stacked on mobile --}}
        <div class="flex flex-col lg:flex-row items-center justify-center gap-8 lg:gap-16 w-full max-w-4xl">

            {{-- Left: icon --}}
            <div class="init0 animate-float animate-fade-in d1 flex-shrink-0">
                <div class="bracket scan-container p-5" style="display:inline-block;">
                    <div class="scan-line"></div>
                    <div class="w-20 h-20 rounded-full border border-luxury-charcoal flex items-center justify-center relative"
                         style="background: radial-gradient(circle at 40% 35%, #1A1A1A, #000);">
                        <i class="fa-solid fa-bag-shopping text-4xl text-luxury-charcoal"
                           style="filter: drop-shadow(0 0 10px rgba(212,175,55,0.25));"></i>
                        <span class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <span class="block w-16 h-[2px] rotate-[135deg] rounded-full"
                                  style="background: linear-gradient(90deg, transparent, #D4AF37, transparent); opacity:0.75;"></span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Right: text content --}}
            <div class="flex flex-col items-center lg:items-start text-center lg:text-left min-w-0">

                {{-- Label --}}
                <p class="init0 animate-fade-in d2 text-luxury-charcoal text-[10px] tracking-[0.3em] uppercase font-medium mb-1">
                    — &nbsp;Page Not Found&nbsp; —
                </p>

                {{-- 404 --}}
                <div class="init0 animate-fade-up d2">
                    <h1 class="glitch text-gold-shimmer font-bold leading-none select-none"
                        data-text="404"
                        style="font-size: clamp(4rem, 12vw, 7rem); letter-spacing: -0.04em;">
                        404
                    </h1>
                </div>

                {{-- Divider --}}
                <div class="init0 animate-fade-in d3 flex items-center gap-3 my-3 w-full max-w-[260px]">
                    <div class="flex-1 h-px" style="background: linear-gradient(90deg, transparent, #37474F);"></div>
                    <i class="fa-solid fa-diamond text-luxury-gold" style="font-size: 6px;"></i>
                    <div class="flex-1 h-px" style="background: linear-gradient(90deg, #37474F, transparent);"></div>
                </div>

                {{-- Message --}}
                <div class="init0 animate-fade-up d4 mb-4 max-w-sm">
                    <h2 class="text-luxury-light font-semibold mb-1.5" style="font-size: clamp(0.9rem, 2vw, 1.1rem);">
                        This page has left the building
                    </h2>
                    <p class="text-luxury-charcoal text-[13px] leading-relaxed">
                        The product or page you're looking for may have been moved, removed, or never existed.
                    </p>
                </div>

                {{-- Buttons --}}
                <div class="init0 animate-fade-up d5 flex flex-wrap items-center gap-2 mb-4">
                    <a href="{{ url('/') }}"
                       class="inline-flex items-center justify-center gap-2 h-[34px] px-5 rounded text-[13px] font-semibold transition-all duration-300 hover:brightness-110 hover:-translate-y-0.5"
                       style="background: #D4AF37; color: #000000;">
                        <i class="fa-solid fa-house text-xs"></i>
                        Back to Home
                    </a>
                    <a href="{{ url('/listings') }}"
                       class="inline-flex items-center justify-center gap-2 h-[34px] px-5 rounded text-[13px] font-semibold border border-luxury-gold/50 text-luxury-gold transition-all duration-300 hover:bg-luxury-gold/10 hover:-translate-y-0.5">
                        <i class="fa-solid fa-grid-2 text-xs"></i>
                        Browse
                    </a>
                    <a href="javascript:history.back()"
                       class="inline-flex items-center justify-center gap-2 h-[34px] px-4 rounded text-[13px] font-medium text-luxury-charcoal transition-all duration-300 hover:text-luxury-light hover:-translate-y-0.5">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Go Back
                    </a>
                </div>

                {{-- Search --}}
                <div class="init0 animate-fade-up d6 w-full max-w-sm">
                    <form action="{{ url('/listings') }}" method="GET" class="relative">
                        <input type="text"
                               name="search"
                               placeholder="Search products, brands..."
                               class="w-full h-[34px] bg-luxury-surface border border-luxury-charcoal rounded pl-3 pr-10 text-[13px] text-luxury-light placeholder-luxury-charcoal outline-none transition-colors duration-200"
                               onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor=''">
                        <button type="submit"
                                class="absolute right-0 top-0 h-[34px] w-[34px] flex items-center justify-center text-luxury-charcoal hover:text-luxury-gold transition-colors duration-200">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="relative z-10 border-t border-luxury-charcoal/30 px-5 py-2.5 flex items-center justify-between flex-shrink-0">
        <span class="text-luxury-charcoal text-[10px]">
            &copy; {{ date('Y') }} {{ config('app.name', 'Shopenhood') }}. All rights reserved.
        </span>
        <div class="flex items-center gap-3 text-luxury-charcoal text-[10px]">
            <a href="{{ url('/') }}" class="hover:text-luxury-gold transition-colors duration-200">Home</a>
            <span class="opacity-30">·</span>
            <a href="{{ url('/listings') }}" class="hover:text-luxury-gold transition-colors duration-200">Listings</a>
            <span class="opacity-30">·</span>
            <a href="mailto:{{ config('mail.from.address', 'support@shopenhood.com') }}" class="hover:text-luxury-gold transition-colors duration-200">Support</a>
        </div>
    </footer>

</body>
</html>
