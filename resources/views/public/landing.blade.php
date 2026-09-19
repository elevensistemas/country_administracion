<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club de Campo La Ranita | Tranquilidad, Naturaleza y Vida de Campo en Manzanares</title>
    
    <!-- Meta SEO -->
    <meta name="description" content="Club de Campo La Ranita: 120 hectáreas de serenidad, naturaleza virgen, seguridad 24 hs y deportes en Manzanares, Pilar / Open Door. Lotes exclusivos de 2.000 a 5.000 m².">
    <meta name="keywords" content="Club de Campo La Ranita, La Ranita Manzanares, Barrio Privado Pilar, Lotes en Manzanares, Country Club Buenos Aires, Vida de campo Pilar">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/access/laranitacountryclub') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/access/laranitacountryclub') }}">
    <meta property="og:title" content="Club de Campo La Ranita | Exclusividad & Naturaleza en Manzanares">
    <meta property="og:description" content="Descubrí 120 hectáreas de campo natural, arboledas centenarias, infraestructura deportiva de primer nivel y la tranquilidad que tu familia busca.">
    <meta property="og:image" content="{{ asset('img/landing/hero_main.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/access/laranitacountryclub') }}">
    <meta property="twitter:title" content="Club de Campo La Ranita | Manzanares">
    <meta property="twitter:description" content="120 hectáreas de campo natural, deportes, Club House y seguridad permanente a solo 50 minutos de Buenos Aires.">
    <meta property="twitter:image" content="{{ asset('img/landing/hero_main.jpg') }}">

    <!-- Google Fonts & Tailwind CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Playfair+Display:ital,wght@0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f4f7f4',
                            100: '#e5ede5',
                            200: '#cdddc9',
                            300: '#a6c39e',
                            400: '#7ba471',
                            500: '#58874d',
                            600: '#436c39',
                            700: '#35552e',
                            800: '#2c4427',
                            900: '#253922',
                            950: '#111f10',
                        },
                        amberGold: {
                            500: '#d97706',
                            600: '#b45309',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
        }
        .text-balance {
            text-wrap: balance;
        }
        .hero-gradient {
            background: linear-gradient(180deg, rgba(17, 31, 16, 0.45) 0%, rgba(17, 31, 16, 0.85) 100%);
        }
    </style>
</head>
<body class="font-sans text-slate-800 bg-slate-50 antialiased selection:bg-brand-500 selection:text-white">

    <!-- TOP ANNOUNCEMENT BAR -->
    <div class="bg-brand-900 text-brand-100 text-xs py-2 px-4 text-center font-medium tracking-wide flex justify-center items-center gap-2">
        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
        <span class="truncate sm:whitespace-normal">Guardia & Accesos 24 hs: <strong>+54 9 11 0000-0000</strong> &bull; Manzanares, Pilar / Open Door</span>
    </div>

    <!-- NAVIGATION BAR -->
    <header class="sticky top-0 z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="#inicio" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-brand-800 flex items-center justify-center text-white shadow-md group-hover:bg-brand-700 transition">
                        <svg class="w-6 h-6 text-brand-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-serif font-bold text-xl text-slate-900 tracking-tight group-hover:text-brand-800 transition">Club de Campo</span>
                        <span class="block text-xs uppercase tracking-widest text-brand-700 font-bold -mt-1">La Ranita</span>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-slate-600">
                    <a href="#club" class="hover:text-brand-700 transition">El Club</a>
                    <a href="#infraestructura" class="hover:text-brand-700 transition">Infraestructura</a>
                    <a href="#naturaleza" class="hover:text-brand-700 transition">Naturaleza</a>
                    <a href="#galeria" class="hover:text-brand-700 transition">Galería</a>
                    <a href="#ubicacion" class="hover:text-brand-700 transition">Ubicación</a>
                    <a href="#contacto" class="hover:text-brand-700 transition">Contacto</a>
                </nav>

                <!-- Action Button & Login Portal -->
                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ url('/login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-slate-700 hover:text-brand-800 bg-slate-100 hover:bg-slate-200/80 rounded-full transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Portal Residentes
                    </a>
                    <a href="#contacto" class="inline-flex items-center px-5 py-2.5 text-xs font-bold text-white bg-brand-800 hover:bg-brand-700 rounded-full shadow-sm hover:shadow transition transform active:scale-95">
                        Agendar Visita
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobileMenuBtn" type="button" class="p-2 text-slate-600 hover:text-slate-900 focus:outline-none" aria-label="Abrir menú">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu dropdown -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-b border-slate-200 px-6 py-4 space-y-3 text-sm font-semibold text-slate-700 shadow-xl">
            <a href="#club" class="block py-1 hover:text-brand-700">El Club</a>
            <a href="#infraestructura" class="block py-1 hover:text-brand-700">Infraestructura & Deportes</a>
            <a href="#naturaleza" class="block py-1 hover:text-brand-700">Naturaleza & Río</a>
            <a href="#galeria" class="block py-1 hover:text-brand-700">Galería Fotográfica</a>
            <a href="#ubicacion" class="block py-1 hover:text-brand-700">Ubicación</a>
            <a href="#contacto" class="block py-1 text-brand-700 font-bold">Contacto & Visitas</a>
            <hr class="border-slate-100 my-2">
            <a href="{{ url('/login') }}" class="block py-2 text-center text-xs font-bold text-slate-700 bg-slate-100 rounded-lg">Portal Propietarios / Garita</a>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section id="inicio" class="relative min-h-[90vh] flex items-center justify-center bg-brand-950 overflow-hidden">
        <!-- Background Media Container (Video + Image Cross-fade) -->
        <div class="absolute inset-0 w-full h-full overflow-hidden">
            <!-- Background Image -->
            <img id="heroImage" src="{{ asset('img/landing/hero_main.jpg') }}" alt="Club de Campo La Ranita Manzanares" class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 transition-opacity duration-1000 ease-in-out opacity-0">

            <!-- Background Video (Primary on load) -->
            <video id="heroVideo" autoplay muted loop playsinline preload="auto" class="absolute inset-0 w-full h-full object-cover object-center transform scale-105 transition-opacity duration-1000 ease-in-out opacity-100">
                <source src="{{ asset('img/landing/hero_video.mp4') }}" type="video/mp4">
            </video>
        </div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 hero-gradient z-0 pointer-events-none"></div>

        <!-- Hero Content -->
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center text-white z-10">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur-md text-brand-100 text-xs sm:text-sm font-semibold tracking-wider uppercase mb-6 border border-white/20">
                <svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Manzanares &bull; Pilar &bull; Open Door
            </span>

            <h1 class="font-serif text-3xl sm:text-5xl md:text-7xl font-bold tracking-tight leading-tight sm:leading-[1.1] mb-6 text-balance drop-shadow-sm">
                El refugio natural donde la vida encuentra su ritmo.
            </h1>

            <p class="max-w-2xl mx-auto text-lg sm:text-xl text-slate-200 font-light leading-relaxed mb-10 text-balance">
                120 hectáreas de campo virgen, arboledas centenarias, amplios lotes y la serenidad del Río Luján. Diseñado para quienes eligen vivir en armonía con la naturaleza.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#contacto" class="w-full sm:w-auto px-8 py-4 rounded-full bg-brand-500 hover:bg-brand-400 text-white font-bold text-sm sm:text-base shadow-lg hover:shadow-brand-500/30 transition transform hover:-translate-y-0.5">
                    Coordinar Visita Guiada
                </a>
                <a href="#club" class="w-full sm:w-auto px-8 py-4 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-semibold text-sm sm:text-base border border-white/30 transition">
                    Conocer el Club
                </a>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mt-16 max-w-4xl mx-auto pt-8 border-t border-white/15">
                <div class="p-3">
                    <div class="text-3xl sm:text-4xl font-extrabold text-white font-serif">120</div>
                    <div class="text-xs sm:text-sm text-brand-200 font-medium mt-1">Hectáreas de Campo</div>
                </div>
                <div class="p-3">
                    <div class="text-3xl sm:text-4xl font-extrabold text-white font-serif">131</div>
                    <div class="text-xs sm:text-sm text-brand-200 font-medium mt-1">Lotes Exclusivos</div>
                </div>
                <div class="p-3">
                    <div class="text-3xl sm:text-4xl font-extrabold text-white font-serif">24/7</div>
                    <div class="text-xs sm:text-sm text-brand-200 font-medium mt-1">Seguridad & Vigilancia</div>
                </div>
                <div class="p-3">
                    <div class="text-3xl sm:text-4xl font-extrabold text-white font-serif">100%</div>
                    <div class="text-xs sm:text-sm text-brand-200 font-medium mt-1">Naturaleza & Calma</div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: EL CLUB & FILOSOFÍA -->
    <section id="club" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Identidad & Tradición</span>
                    <h2 class="font-serif text-3xl sm:text-5xl font-bold text-slate-900 mt-4 mb-6 leading-tight">
                        Un espacio pensado para reencontrarse con lo esencial.
                    </h2>
                    <p class="text-slate-600 text-lg leading-relaxed mb-6 font-normal">
                        Ubicado en el límite estratégico entre <strong>Manzanares (Pilar)</strong> y <strong>Open Door (Luján)</strong>, <em>Club de Campo La Ranita</em> nació como un proyecto campestre que preserva el encanto rural pampeano, integrando servicios de vanguardia y un estándar urbanístico de excelencia.
                    </p>
                    <p class="text-slate-600 text-base leading-relaxed mb-8">
                        Con solo 131 parcelas distribuidas en 120 hectáreas arboladas, la baja densidad poblacional asegura privacidad, vistas infinitas hacia el horizonte y un contacto genuino con la fauna y flora autóctona.
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-800 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Grandes Dimensiones de Lotes</h4>
                                <p class="text-sm text-slate-500">Superficies que van desde los 2.000 m² hasta 5.000 m², garantizando amplitud entre residencias.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-800 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Ribera Natural sobre el Río Luján</h4>
                                <p class="text-sm text-slate-500">Senderos ecológicos parquizados para paseos a caballo, caminatas y atardeceres memorables.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-800 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Seguridad Integral Permanente</h4>
                                <p class="text-sm text-slate-500">Puesto de control perimetral, monitoreo de última generación y control de accesos vehicular 24 horas.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="relative mx-auto rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                        <img src="{{ asset('img/landing/clubhouse.jpg') }}" alt="Club House La Ranita" class="w-full h-[520px] object-cover hover:scale-105 transition duration-700">
                    </div>
                    <!-- Overlaid mini badge -->
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl border border-slate-100 max-w-xs hidden sm:block">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-serif text-xl font-bold">
                                CH
                            </div>
                            <div>
                                <h5 class="font-bold text-slate-900 text-sm">Club House Tradicional</h5>
                                <p class="text-xs text-slate-500">Galerías criollas, gastronomía & punto de encuentro.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: INFRAESTRUCTURA & AMENITIES -->
    <section id="infraestructura" class="py-24 bg-slate-50 border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-600 bg-brand-100/60 px-3 py-1 rounded-full">Amenities & Confort</span>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold text-slate-900 mt-4 mb-4">
                    Infraestructura pensada para el deporte y el bienestar
                </h2>
                <p class="text-slate-600 text-lg font-light">
                    Instalaciones de primer nivel integradas armónicamente en el paisaje rural.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1: Club House -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-xl transition group duration-300">
                    <div class="h-60 overflow-hidden relative">
                        <img src="{{ asset('img/landing/clubhouse.jpg') }}" alt="Club House" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-4 right-4 bg-black/60 backdrop-blur-md text-white text-xs px-3 py-1 rounded-full font-medium">Social</span>
                    </div>
                    <div class="p-8">
                        <h3 class="font-serif text-xl font-bold text-slate-900 mb-2">Club House & Restaurante</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Emblemático casco de campo con amplias galerías, salón de eventos familiares, chimenea central y servicio gastronómico.
                        </p>
                        <ul class="text-xs text-slate-500 space-y-1.5">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Salón de usos múltiples y reuniones</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Vistas panorámicas a las arboledas</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 2: Piscinas & Solarium -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-xl transition group duration-300">
                    <div class="h-60 overflow-hidden relative">
                        <img src="{{ asset('img/landing/pool.jpg') }}" alt="Piscinas & Solarium" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-4 right-4 bg-black/60 backdrop-blur-md text-white text-xs px-3 py-1 rounded-full font-medium">Relax</span>
                    </div>
                    <div class="p-8">
                        <h3 class="font-serif text-xl font-bold text-slate-900 mb-2">Piscinas & Solarium</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Gran piscina principal con borde atérmico de travertino, solarium parquizado y sector seguro para los más chicos.
                        </p>
                        <ul class="text-xs text-slate-500 space-y-1.5">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Piscina para adultos y niños</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Vestuarios completos y solarium verde</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 3: Tenis & Pádel -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-xl transition group duration-300">
                    <div class="h-60 overflow-hidden relative">
                        <img src="{{ asset('img/landing/tennis.jpg') }}" alt="Canchas de Tenis" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-4 right-4 bg-black/60 backdrop-blur-md text-white text-xs px-3 py-1 rounded-full font-medium">Deportes</span>
                    </div>
                    <div class="p-8">
                        <h3 class="font-serif text-xl font-bold text-slate-900 mb-2">Tenis & Pádel</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Canchas de polvo de ladrillo y superficie rápida con iluminación profesional para disputar partidos al caer la tarde.
                        </p>
                        <ul class="text-xs text-slate-500 space-y-1.5">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Canchas de tenis reglamentarias</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Torneos internos y clínicas deportivas</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 4: Fútbol & Polideportivo -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-xl transition group duration-300">
                    <div class="h-60 overflow-hidden relative bg-brand-900 flex items-center justify-center">
                        <img src="{{ asset('img/landing/nature_park.jpg') }}" alt="Espacios Verdes y Fútbol" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-4 right-4 bg-black/60 backdrop-blur-md text-white text-xs px-3 py-1 rounded-full font-medium">Actividad</span>
                    </div>
                    <div class="p-8">
                        <h3 class="font-serif text-xl font-bold text-slate-900 mb-2">Fútbol & Multideporte</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Canchas de césped natural para fútbol 11 y fútbol 7, además de playón polideportivo para vóley y básquet.
                        </p>
                        <ul class="text-xs text-slate-500 space-y-1.5">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Canchas con césped de alta densidad</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Juegos recreativos y actividades al aire libre</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 5: Hípica & Cabalgatas -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-xl transition group duration-300">
                    <div class="h-60 overflow-hidden relative">
                        <img src="{{ asset('img/landing/equestrian.jpg') }}" alt="Sector Hípico" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-4 right-4 bg-black/60 backdrop-blur-md text-white text-xs px-3 py-1 rounded-full font-medium">Hípica</span>
                    </div>
                    <div class="p-8">
                        <h3 class="font-serif text-xl font-bold text-slate-900 mb-2">Espacio Ecuestre & Piqueadero</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Instalaciones hípicas, caballerizas y senderos reservados para paseos a caballo y práctica de equitación.
                        </p>
                        <ul class="text-xs text-slate-500 space-y-1.5">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Corrales y piquetes cuidados</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Circuitos internos de cabalgata</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 6: Servicios Subterráneos -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-xl transition group duration-300">
                    <div class="h-60 overflow-hidden relative bg-slate-900">
                        <img src="{{ asset('img/landing/sunset.jpg') }}" alt="Tranquilidad y Servicios" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span class="absolute top-4 right-4 bg-black/60 backdrop-blur-md text-white text-xs px-3 py-1 rounded-full font-medium">Tecnología</span>
                    </div>
                    <div class="p-8">
                        <h3 class="font-serif text-xl font-bold text-slate-900 mb-2">Servicios & Conectividad</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Tendido de fibra óptica subterráneo, red eléctrica subterránea, recolección interna de residuos y administración activa.
                        </p>
                        <ul class="text-xs text-slate-500 space-y-1.5">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Internet de alta velocidad para Home Office</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>Cuidado paisajístico y forestación continua</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: NATURALEZA & ENTORNO -->
    <section id="naturaleza" class="py-24 bg-brand-950 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <img src="{{ asset('img/landing/nature_park.jpg') }}" alt="Parque Natural" class="w-full h-full object-cover">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400 bg-white/10 px-3 py-1 rounded-full border border-white/10">Biodiversidad Protegida</span>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold mt-4 mb-6 leading-tight">
                    El aire puro de la pampa, a la vera del Río Luján.
                </h2>
                <p class="text-slate-300 text-lg leading-relaxed mb-6 font-light">
                    En La Ranita, la naturaleza no es un decorado: es el corazón del lugar. Sauces criollos, eucaliptos añejos, casuarinas y una variada avifauna componen una postal viva los 365 días del año.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-8">
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <div class="text-emerald-400 text-2xl font-serif font-bold mb-2">+2.500</div>
                        <h4 class="font-bold text-white mb-1">Especies Arbóreas</h4>
                        <p class="text-xs text-slate-400">Arboleda madura que brinda sombra, frescura y privacidad natural entre propiedades.</p>
                    </div>
                    <div class="p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <div class="text-emerald-400 text-2xl font-serif font-bold mb-2">1.500 m</div>
                        <h4 class="font-bold text-white mb-1">Costanera y Senderos</h4>
                        <p class="text-xs text-slate-400">Paseos peatonales ribereños para desconectarse de la rutina y reconectar con la paz.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: GALERÍA FOTOGRÁFICA INTERACTIVA -->
    <section id="galeria" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Experiencia Visual</span>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold text-slate-900 mt-4 mb-4">
                    Imágenes de una vida en plenitud
                </h2>
                <p class="text-slate-600 text-lg font-light">
                    Hacé clic en cualquier fotografía para verla en pantalla completa.
                </p>
            </div>

            <!-- Gallery Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Item 1 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer h-72" onclick="openLightbox('{{ asset('img/landing/hero_main.jpg') }}', 'Avenidas Principales y Arboledas Centenarias')">
                    <img src="{{ asset('img/landing/hero_main.jpg') }}" alt="Avenida Principal" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-6">
                        <span class="text-white text-sm font-semibold">Avenidas Principales y Arboledas</span>
                    </div>
                </div>

                <!-- Item 2 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer h-72" onclick="openLightbox('{{ asset('img/landing/clubhouse.jpg') }}', 'Club House Tradicional y Galerías')">
                    <img src="{{ asset('img/landing/clubhouse.jpg') }}" alt="Club House" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-6">
                        <span class="text-white text-sm font-semibold">Club House Tradicional</span>
                    </div>
                </div>

                <!-- Item 3 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer h-72" onclick="openLightbox('{{ asset('img/landing/pool.jpg') }}', 'Sector de Piscinas y Solarium de Travertino')">
                    <img src="{{ asset('img/landing/pool.jpg') }}" alt="Piscinas" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-6">
                        <span class="text-white text-sm font-semibold">Piscina & Solarium</span>
                    </div>
                </div>

                <!-- Item 4 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer h-72" onclick="openLightbox('{{ asset('img/landing/tennis.jpg') }}', 'Canchas de Tenis de Polvo de Ladrillo')">
                    <img src="{{ asset('img/landing/tennis.jpg') }}" alt="Tenis" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-6">
                        <span class="text-white text-sm font-semibold">Canchas de Tenis</span>
                    </div>
                </div>

                <!-- Item 5 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer h-72" onclick="openLightbox('{{ asset('img/landing/equestrian.jpg') }}', 'Sector Hípico y Caballos en Pastoreo')">
                    <img src="{{ asset('img/landing/equestrian.jpg') }}" alt="Sector Hípico" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-6">
                        <span class="text-white text-sm font-semibold">Sector Ecuestre</span>
                    </div>
                </div>

                <!-- Item 6 -->
                <div class="group relative rounded-2xl overflow-hidden shadow-md cursor-pointer h-72" onclick="openLightbox('{{ asset('img/landing/sunset.jpg') }}', 'Atardeceres Únicos sobre el Campo')">
                    <img src="{{ asset('img/landing/sunset.jpg') }}" alt="Atardecer" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-6">
                        <span class="text-white text-sm font-semibold">Atardeceres en el Campo</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LIGHTBOX MODAL -->
    <div id="lightboxModal" class="fixed inset-0 z-50 bg-black/95 backdrop-blur-xl hidden flex items-center justify-center p-4" onclick="closeLightbox(event)">
        <button class="absolute top-6 right-6 text-white/80 hover:text-white text-4xl font-light focus:outline-none" onclick="closeLightboxDirect()">&times;</button>
        <div class="max-w-5xl max-h-[90vh] flex flex-col items-center">
            <img id="lightboxImg" src="" alt="" class="max-w-full max-h-[80vh] rounded-xl shadow-2xl object-contain">
            <p id="lightboxCaption" class="text-slate-300 text-center text-sm sm:text-base mt-4 font-medium"></p>
        </div>
    </div>

    <!-- SECTION: UBICACIÓN & ACCESOS -->
    <section id="ubicacion" class="py-24 bg-slate-50 border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-600 bg-brand-100/60 px-3 py-1 rounded-full">Accesibilidad Óptima</span>
                    <h2 class="font-serif text-3xl sm:text-5xl font-bold text-slate-900 mt-4 mb-6 leading-tight">
                        Cerca de todo, con la tranquilidad que buscás.
                    </h2>
                    <p class="text-slate-600 text-lg leading-relaxed mb-6 font-light">
                        Estratégicamente localizado en la localidad de <strong>Manzanares</strong> (Partido del Pilar), próximo a <strong>Open Door</strong> (Partido de Luján), combinando un acceso rápido y seguro con el más absoluto silencio de campo.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Desde Panamericana Ramal Pilar (Km 59.5)</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Acceso pavimentado directo por calle de ingreso a Manzanares. A solo 15 minutos del centro comercial y médico de Pilar.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Coordenadas de Navegación GPS</h4>
                                <p class="text-sm text-slate-500 mt-0.5 font-mono text-xs text-brand-800 font-semibold">-34.4772223, -59.0379338 &bull; Club de Campo La Ranita</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="https://maps.google.com/?q=-34.4772223,-59.0379338" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            Abrir en Google Maps
                        </a>
                        <a href="https://waze.com/ul?ll=-34.4772223,-59.0379338&navigate=yes" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 text-xs font-bold transition">
                            Abrir en Waze
                        </a>
                    </div>
                </div>

                <!-- Map Frame -->
                <div class="rounded-3xl overflow-hidden shadow-xl border-4 border-white bg-slate-200 h-[480px]">
                    <iframe 
                        title="Mapa Club de Campo La Ranita"
                        src="https://maps.google.com/maps?q=-34.4772223,-59.0379338&t=&z=14&ie=UTF8&iwloc=&output=embed" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: CONTACTO & FORMULARIO -->
    <section id="contacto" class="py-24 bg-white relative">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-600 bg-brand-50 px-3 py-1 rounded-full">Atención Personalizada</span>
                <h2 class="font-serif text-3xl sm:text-5xl font-bold text-slate-900 mt-4 mb-4">
                    Comenzá a proyectar tu vida en La Ranita
                </h2>
                <p class="text-slate-600 text-base sm:text-lg font-light">
                    Completá el siguiente formulario para recibir información sobre lotes disponibles o coordinar una visita personalizada.
                </p>
            </div>

            <!-- Card Form -->
            <div class="bg-slate-50 rounded-3xl p-8 sm:p-12 border border-slate-200/80 shadow-lg">
                <!-- Session Alerts -->
                @if(session('success'))
                    <div class="mb-8 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                        <svg class="w-6 h-6 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <div>
                            <h5 class="font-bold text-sm">{{ session('success') }}</h5>
                            <p class="text-xs text-emerald-700">Nuestro equipo de administración se pondrá en contacto a la brevedad.</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-8 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800">
                        <h5 class="font-bold text-sm mb-1">Por favor corregí los siguientes campos:</h5>
                        <ul class="list-disc list-inside text-xs space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('/access/laranitacountryclub/contacto') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Honeypot Field (hidden from real users) -->
                    <div style="display:none !important;" aria-hidden="true">
                        <input type="text" name="website_hp" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="nombre" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Nombre y Apellido *</label>
                            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Juan Pérez" class="w-full px-4 py-3.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white text-sm outline-none transition">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Correo Electrónico *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="juan@ejemplo.com" class="w-full px-4 py-3.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white text-sm outline-none transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="telefono" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Teléfono / WhatsApp *</label>
                            <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}" required placeholder="+54 9 11 ..." class="w-full px-4 py-3.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white text-sm outline-none transition">
                        </div>

                        <div>
                            <label for="interes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Motivo de Consulta *</label>
                            <select id="interes" name="interes" required class="w-full px-4 py-3.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white text-sm outline-none transition">
                                <option value="Información General" {{ old('interes') == 'Información General' ? 'selected' : '' }}>Información General</option>
                                <option value="Coordinar Visita al Club" {{ old('interes') == 'Coordinar Visita al Club' ? 'selected' : '' }}>Coordinar Visita al Club</option>
                                <option value="Consulta sobre Lotes y Loteo" {{ old('interes') == 'Consulta sobre Lotes y Loteo' ? 'selected' : '' }}>Consulta sobre Lotes y Loteo</option>
                                <option value="Contacto Administración" {{ old('interes') == 'Contacto Administración' ? 'selected' : '' }}>Contacto Administración</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="mensaje" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Mensaje o Consulta *</label>
                        <textarea id="mensaje" name="mensaje" rows="4" required placeholder="Escribí aquí tus dudas o los días y horarios de tu preferencia para visitarnos..." class="w-full px-4 py-3.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white text-sm outline-none transition">{{ old('mensaje') }}</textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                        <p class="text-xs text-slate-500">Tus datos están protegidos y serán utilizados únicamente con fines informativos.</p>
                        <button type="submit" class="w-full sm:w-auto px-10 py-4 rounded-full bg-brand-800 hover:bg-brand-700 text-white font-bold text-sm shadow-md hover:shadow-lg transition transform active:scale-95">
                            Enviar Consulta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: Brand Info -->
                <div class="md:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-brand-700 flex items-center justify-center text-white font-serif font-bold text-sm">
                            LR
                        </div>
                        <span class="font-serif font-bold text-lg text-white">Club de Campo La Ranita</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">
                        Un estilo de vida natural, seguro y exclusivo en Manzanares, Provincia de Buenos Aires.
                    </p>
                    <p class="text-xs text-slate-500">
                        GPS: -34.4772223, -59.0379338
                    </p>
                </div>

                <!-- Col 2: Navigation Links -->
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Navegación</h5>
                    <ul class="text-xs space-y-2.5">
                        <li><a href="#club" class="hover:text-white transition">El Club</a></li>
                        <li><a href="#infraestructura" class="hover:text-white transition">Infraestructura</a></li>
                        <li><a href="#naturaleza" class="hover:text-white transition">Naturaleza & Río</a></li>
                        <li><a href="#galeria" class="hover:text-white transition">Galería Fotográfica</a></li>
                        <li><a href="#ubicacion" class="hover:text-white transition">Ubicación & Accesos</a></li>
                    </ul>
                </div>

                <!-- Col 3: Institutional / Portals -->
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Accesos al Sistema</h5>
                    <ul class="text-xs space-y-2.5">
                        <li><a href="{{ url('/login') }}" class="hover:text-white transition flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Portal de Propietarios</a></li>
                        <li><a href="{{ url('/login') }}" class="hover:text-white transition flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>Control de Acceso (Garita)</a></li>
                        <li><a href="{{ url('/login') }}" class="hover:text-white transition flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Administración Central</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact Direct -->
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Contacto Directo</h5>
                    <p class="text-xs text-slate-400 mb-2"><strong>Ubicación:</strong> Manzanares, Pilar / Open Door, Luján.</p>
                    <p class="text-xs text-slate-400 mb-2"><strong>Email:</strong> contacto@laranita.com</p>
                    <p class="text-xs text-slate-400"><strong>Guardia 24 hs:</strong> Atención y control permanente</p>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} Club de Campo La Ranita. Todos los derechos reservados.</p>
                <div class="flex items-center gap-6">
                    <a href="#inicio" class="hover:text-slate-400 transition">Volver arriba &uarr;</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT: Interactivity, Mobile Menu & Lightbox -->
    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
            mobileMenu.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                });
            });
        }

        // Lightbox functions
        function openLightbox(src, caption) {
            const modal = document.getElementById('lightboxModal');
            const img = document.getElementById('lightboxImg');
            const cap = document.getElementById('lightboxCaption');
            if (modal && img && cap) {
                img.src = src;
                cap.textContent = caption || '';
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeLightbox(event) {
            if (event.target.id === 'lightboxModal') {
                closeLightboxDirect();
            }
        }

        function closeLightboxDirect() {
            const modal = document.getElementById('lightboxModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Escape key to close lightbox
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightboxDirect();
            }
        });

        // Hero Background Media Switcher (Video 20s <-> Imagen 20s)
        (function() {
            const heroVideo = document.getElementById('heroVideo');
            const heroImage = document.getElementById('heroImage');
            if (!heroVideo || !heroImage) return;

            let showingVideo = true;

            function switchToImage() {
                heroVideo.classList.remove('opacity-100');
                heroVideo.classList.add('opacity-0');
                heroImage.classList.remove('opacity-0');
                heroImage.classList.add('opacity-100');
                showingVideo = false;
            }

            function switchToVideo() {
                heroImage.classList.remove('opacity-100');
                heroImage.classList.add('opacity-0');
                heroVideo.classList.remove('opacity-0');
                heroVideo.classList.add('opacity-100');
                try {
                    heroVideo.currentTime = 0;
                    heroVideo.play().catch(function() {});
                } catch(e) {}
                showingVideo = true;
            }

            // Inicia mostrando el video; a los 20 segundos conmuta a la imagen fija y continúa alternando cada 20s
            setInterval(function() {
                if (showingVideo) {
                    switchToImage();
                } else {
                    switchToVideo();
                }
            }, 20000);

            // Fallback en caso de que el navegador bloquee el autoplay de video
            heroVideo.play().catch(function() {
                switchToImage();
            });
        })();
    </script>
</body>
</html>
