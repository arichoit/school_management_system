@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-blue-950 via-blue-900 to-blue-950 text-white overflow-hidden py-16 sm:py-24">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-800/80 border border-blue-700 text-xs font-semibold text-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ __('messages.admissions_open') }}
                </div>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                    {{ __('messages.hero_title') }}
                </h1>
                <p class="text-base sm:text-lg text-blue-200 max-w-2xl leading-relaxed mx-auto lg:mx-0">
                    {{ __('messages.hero_subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-2">
                    <button @click="admissionModalOpen = true" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-bold text-sm bg-emerald-500 text-slate-950 hover:bg-emerald-400 shadow-lg shadow-emerald-500/20 transition transform active:scale-95">
                        <svg class="w-5 h-5 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>{{ __('messages.apply_now') }}</span>
                    </button>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl font-bold text-sm bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur transition transform active:scale-95">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>{{ __('messages.explore_portal') }}</span>
                    </a>
                </div>
            </div>

            <!-- Hero Highlights Card -->
            <div class="lg:col-span-5">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">School At A Glance</span>
                        <span class="text-xs text-blue-200">Established 2012</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-900/40 p-4 rounded-2xl border border-blue-800/50">
                            <span class="text-2xl sm:text-3xl font-extrabold text-white block">{{ $stats['students'] }}</span>
                            <span class="text-xs text-blue-200 font-medium">{{ __('messages.total_students') }}</span>
                        </div>
                        <div class="bg-blue-900/40 p-4 rounded-2xl border border-blue-800/50">
                            <span class="text-2xl sm:text-3xl font-extrabold text-emerald-400 block">{{ $stats['teachers'] }}</span>
                            <span class="text-xs text-blue-200 font-medium">{{ __('messages.total_teachers') }}</span>
                        </div>
                        <div class="bg-blue-900/40 p-4 rounded-2xl border border-blue-800/50">
                            <span class="text-2xl sm:text-3xl font-extrabold text-amber-300 block">100%</span>
                            <span class="text-xs text-blue-200 font-medium">Board Pass Rate</span>
                        </div>
                        <div class="bg-blue-900/40 p-4 rounded-2xl border border-blue-800/50">
                            <span class="text-2xl sm:text-3xl font-extrabold text-cyan-300 block">5 Roles</span>
                            <span class="text-xs text-blue-200 font-medium">Integrated Portal</span>
                        </div>
                    </div>

                    <div class="pt-2 text-xs text-blue-200 space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Fully responsive across smartphone, tablet & desktop</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Bilingual English primary with Bengali localization</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Live parent-child tracking with online fee payments</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KPI Metrics Section -->
<section class="py-10 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="p-4 rounded-xl">
                <p class="text-3xl sm:text-4xl font-extrabold text-blue-900">1,200+</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-1">{{ __('messages.kpi_students_count') }}</p>
            </div>
            <div class="p-4 rounded-xl">
                <p class="text-3xl sm:text-4xl font-extrabold text-blue-900">85+</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-1">{{ __('messages.kpi_teachers_count') }}</p>
            </div>
            <div class="p-4 rounded-xl">
                <p class="text-3xl sm:text-4xl font-extrabold text-emerald-600">100%</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-1">{{ __('messages.kpi_pass_rate') }}</p>
            </div>
            <div class="p-4 rounded-xl">
                <p class="text-3xl sm:text-4xl font-extrabold text-emerald-600">98%</p>
                <p class="text-xs sm:text-sm font-semibold text-slate-600 mt-1">{{ __('messages.kpi_satisfaction') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Notice Board Preview & Academics -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            <!-- Left: Notice Board (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-extrabold text-blue-950">{{ __('messages.recent_notices') }}</h2>
                        <p class="text-xs text-slate-500">Official circulars, exam dates, and institution announcements</p>
                    </div>
                    <a href="{{ route('public.notices') }}" class="text-xs font-bold text-blue-900 hover:text-blue-700 flex items-center gap-1 transition">
                        <span>{{ __('messages.view_all_notices') }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($notices as $notice)
                        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $catBadge = match($notice->category) {
                                                'academic' => 'bg-blue-100 text-blue-800',
                                                'exam' => 'bg-amber-100 text-amber-800',
                                                'holiday' => 'bg-emerald-100 text-emerald-800',
                                                default => 'bg-slate-100 text-slate-700'
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $catBadge }}">
                                            {{ $notice->category }}
                                        </span>
                                        <span class="text-xs text-slate-400">
                                            {{ $notice->published_at ? $notice->published_at->format('M d, Y') : 'Recent' }}
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900">{{ $notice->title }}</h3>
                                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $notice->content }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl p-8 border border-slate-200 text-center text-slate-400 text-sm">
                            No notices published at the moment.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Quick Admission Form (5 cols) -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md">
                    <div class="mb-5">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider mb-2">Fast Inquiry</span>
                        <h3 class="text-xl font-extrabold text-blue-950">{{ __('messages.admission_inquiry') }}</h3>
                        <p class="text-xs text-slate-500 mt-1">Receive complete syllabus and admission schedule directly on your phone & email.</p>
                    </div>

                    <form action="{{ route('admission.inquiry.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.student_name') }} *</label>
                            <input type="text" name="student_name" required placeholder="Full Student Name"
                                   class="w-full rounded-lg border-slate-300 shadow-xs focus:border-blue-900 focus:ring-blue-900 sm:text-xs py-2 px-3 border">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.guardian_name') }} *</label>
                            <input type="text" name="guardian_name" required placeholder="Parent / Guardian Name"
                                   class="w-full rounded-lg border-slate-300 shadow-xs focus:border-blue-900 focus:ring-blue-900 sm:text-xs py-2 px-3 border">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.email_address') }} *</label>
                                <input type="email" name="email" required placeholder="email@address.com"
                                       class="w-full rounded-lg border-slate-300 shadow-xs focus:border-blue-900 focus:ring-blue-900 sm:text-xs py-2 px-3 border">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.phone_number') }} *</label>
                                <input type="tel" name="phone" required placeholder="+8801700000000"
                                       class="w-full rounded-lg border-slate-300 shadow-xs focus:border-blue-900 focus:ring-blue-900 sm:text-xs py-2 px-3 border">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.select_class') }} *</label>
                            <select name="class_applied_for" required
                                    class="w-full rounded-lg border-slate-300 shadow-xs focus:border-blue-900 focus:ring-blue-900 sm:text-xs py-2 px-3 border">
                                @foreach($classes as $c)
                                    <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" 
                                class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 transition shadow-sm">
                            {{ __('messages.send_inquiry') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About & Academics Highlights -->
<section id="about" class="py-16 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">Holistic Education</span>
            <h2 class="text-3xl font-extrabold text-blue-950">{{ __('messages.about_title') }}</h2>
            <p class="text-sm text-slate-600 leading-relaxed">{{ __('messages.about_desc') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-900/30 transition">
                <div class="w-12 h-12 rounded-xl bg-blue-900 text-white flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Academic Rigor & Curricula</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    National curriculum delivered with international pedagogic standards, emphasizing analytical comprehension, language mastery, and creative problem solving.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-900/30 transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Modern STEM & ICT Laboratories</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    High-speed fiber-connected computer labs, robotics workshops, and fully stocked Physics, Chemistry, and Biology demonstration suites.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-900/30 transition">
                <div class="w-12 h-12 rounded-xl bg-purple-600 text-white flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-2">Character, Arts & Athletics</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Competitive football, cricket, debate, chess, and fine arts clubs develop ethical, collaborative, and confident citizens.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Classes Offered Section -->
<section id="academics" class="py-16 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl font-extrabold text-blue-950">Academic Programs & Classes</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-2">Comprehensive secondary education structured into modern sections with active mentor teachers.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            @foreach($classes as $c)
                <div class="bg-white rounded-2xl p-6 border border-slate-200 text-center hover:border-blue-900 transition shadow-xs group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-900 font-extrabold text-base flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-900 group-hover:text-white transition">
                        {{ $c->numeric_level }}
                    </div>
                    <h3 class="font-extrabold text-slate-900 text-base">{{ $c->name }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Code: {{ $c->code }}</p>
                    <div class="mt-4 pt-4 border-t border-slate-100 space-y-1">
                        @foreach($c->sections as $sec)
                            <span class="inline-block px-2 py-0.5 text-[11px] bg-slate-100 rounded text-slate-600 font-medium">
                                {{ $sec->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
