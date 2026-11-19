<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SMS') }} - {{ __('messages.school_name') }}</title>
    
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', 'Hind Siliguri', sans-serif;
        }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased flex flex-col font-sans"
      x-data="{ 
          mobileMenuOpen: false, 
          admissionModalOpen: false,
          toast: {
              show: false,
              message: '{{ session('success') ?? session('error') ?? '' }}',
              type: '{{ session('success') ? 'success' : (session('error') ? 'error' : 'info') }}'
          }
      }"
      x-init="
          if (toast.message) {
              toast.show = true;
              setTimeout(() => toast.show = false, 5000);
          }
          window.addEventListener('notify', e => {
              toast.message = e.detail.message;
              toast.type = e.detail.type || 'success';
              toast.show = true;
              setTimeout(() => toast.show = false, 5000);
          });
      ">

    <!-- Toast Notification -->
    <div x-show="toast.show" 
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-5 right-5 z-50 max-w-sm w-full shadow-lg rounded-xl pointer-events-auto border overflow-hidden p-4 flex items-center space-x-3"
         :class="{
             'bg-emerald-50 border-emerald-200 text-emerald-900': toast.type === 'success',
             'bg-rose-50 border-rose-200 text-rose-900': toast.type === 'error',
             'bg-blue-50 border-blue-200 text-blue-900': toast.type === 'info'
         }"
         x-cloak>
        <template x-if="toast.type === 'success'">
            <svg class="w-6 h-6 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </template>
        <template x-if="toast.type === 'error'">
            <svg class="w-6 h-6 text-rose-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </template>
        <p class="text-sm font-medium flex-1" x-text="toast.message"></p>
        <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600 focus:outline-none">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <!-- Top Announcement Bar -->
    <div class="bg-blue-950 text-white text-xs py-2 px-4 border-b border-blue-900">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    {{ __('messages.admissions_open') }}
                </span>
                <span class="hidden md:inline text-slate-300">| {{ __('messages.admissions_open_desc') }}</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="tel:+8801711000000" class="hover:text-blue-300 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>+880 1711 000000</span>
                </a>
                <span class="text-blue-700">|</span>
                <!-- Bilingual Toggle -->
                <a href="{{ route('locale.switch', app()->getLocale() === 'en' ? 'bn' : 'en') }}" 
                   class="inline-flex items-center gap-1 font-semibold text-emerald-300 hover:text-white transition px-2 py-0.5 rounded bg-blue-900/60 border border-blue-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    <span>{{ app()->getLocale() === 'en' ? 'বাংলা' : 'English' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Brand / Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-900 flex items-center justify-center text-white font-bold text-xl shadow-md shadow-blue-900/20 group-hover:scale-105 transition transform">
                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
                    </div>
                    <div>
                        <span class="block text-xl font-extrabold text-blue-950 tracking-tight leading-tight">
                            {{ __('messages.school_name') }}
                        </span>
                        <span class="block text-xs font-medium text-slate-500">
                            {{ __('messages.school_tagline') }}
                        </span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-900 transition">
                        {{ __('messages.home') }}
                    </a>
                    <a href="{{ route('home') }}#about" class="text-sm font-semibold text-slate-700 hover:text-blue-900 transition">
                        {{ __('messages.about') }}
                    </a>
                    <a href="{{ route('home') }}#academics" class="text-sm font-semibold text-slate-700 hover:text-blue-900 transition">
                        {{ __('messages.academics') }}
                    </a>
                    <a href="{{ route('public.notices') }}" class="text-sm font-semibold text-slate-700 hover:text-blue-900 transition">
                        {{ __('messages.notices') }}
                    </a>
                    <button @click="admissionModalOpen = true" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800 transition">
                        {{ __('messages.admission_inquiry') }}
                    </button>
                </nav>

                <!-- Actions: Portal Button -->
                <div class="hidden md:flex items-center space-x-3">
                    @auth
                        @php
                            $roleRoute = match(auth()->user()->role) {
                                'admin' => route('admin.dashboard'),
                                'teacher' => route('teacher.dashboard'),
                                'parent' => route('parent.dashboard'),
                                'student' => route('student.dashboard'),
                                'accountant' => route('accountant.dashboard'),
                                default => route('home'),
                            };
                        @endphp
                        <a href="{{ $roleRoute }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span>{{ __('messages.dashboard') }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold bg-blue-900 text-white hover:bg-blue-800 shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>{{ __('messages.login') }}</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center space-x-2 md:hidden">
                    <a href="{{ route('locale.switch', app()->getLocale() === 'en' ? 'bn' : 'en') }}" 
                       class="text-xs font-bold px-2 py-1 bg-slate-100 rounded text-blue-900">
                        {{ app()->getLocale() === 'en' ? 'বাংলা' : 'EN' }}
                    </a>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            type="button" 
                            class="p-2 rounded-lg text-slate-700 hover:text-blue-900 hover:bg-slate-100 focus:outline-none"
                            aria-expanded="false" 
                            :aria-expanded="mobileMenuOpen.toString()">
                        <span class="sr-only">Open main menu</span>
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-6 space-y-3"
             x-cloak>
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-800 hover:bg-slate-100">
                {{ __('messages.home') }}
            </a>
            <a href="{{ route('home') }}#about" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-800 hover:bg-slate-100">
                {{ __('messages.about') }}
            </a>
            <a href="{{ route('home') }}#academics" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-800 hover:bg-slate-100">
                {{ __('messages.academics') }}
            </a>
            <a href="{{ route('public.notices') }}" class="block px-3 py-2 rounded-lg text-base font-semibold text-slate-800 hover:bg-slate-100">
                {{ __('messages.notices') }}
            </a>
            <button @click="mobileMenuOpen = false; admissionModalOpen = true" class="w-full text-left px-3 py-2 rounded-lg text-base font-semibold text-emerald-700 hover:bg-emerald-50">
                {{ __('messages.admission_inquiry') }}
            </button>
            <div class="pt-2 border-t border-slate-100">
                @auth
                    <a href="{{ route('login') }}" class="block w-full text-center py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 text-white">
                        {{ __('messages.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center py-2.5 rounded-lg text-sm font-semibold bg-blue-900 text-white">
                        {{ __('messages.login') }}
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Page Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Admission Inquiry Modal -->
    <div x-show="admissionModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" 
         aria-modal="true" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="admissionModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="admissionModalOpen = false" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="admissionModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100">
                
                <div class="bg-blue-900 px-6 py-4 flex items-center justify-between text-white">
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <h3 class="text-lg font-bold">{{ __('messages.admission_inquiry') }}</h3>
                    </div>
                    <button @click="admissionModalOpen = false" class="text-blue-200 hover:text-white focus:outline-none">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('admission.inquiry.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.student_name') }} *</label>
                        <input type="text" name="student_name" required placeholder="e.g. Samir Hasan"
                               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-900 focus:ring-blue-900 sm:text-sm py-2 px-3 border">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.guardian_name') }} *</label>
                        <input type="text" name="guardian_name" required placeholder="e.g. Tariq Hasan"
                               class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-900 focus:ring-blue-900 sm:text-sm py-2 px-3 border">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.email_address') }} *</label>
                            <input type="email" name="email" required placeholder="parent@example.com"
                                   class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-900 focus:ring-blue-900 sm:text-sm py-2 px-3 border">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.phone_number') }} *</label>
                            <input type="tel" name="phone" required placeholder="+8801700000000"
                                   class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-900 focus:ring-blue-900 sm:text-sm py-2 px-3 border">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.select_class') }} *</label>
                            <select name="class_applied_for" required
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-900 focus:ring-blue-900 sm:text-sm py-2 px-3 border">
                                <option value="Class 6">Class 6 (ষষ্ঠ শ্রেণি)</option>
                                <option value="Class 7">Class 7 (সপ্তম শ্রেণি)</option>
                                <option value="Class 8">Class 8 (অষ্টম শ্রেণি)</option>
                                <option value="Class 9 (Science)">Class 9 (বিজ্ঞান)</option>
                                <option value="Class 9 (Commerce)">Class 9 (ব্যবসায় শিক্ষা)</option>
                                <option value="Class 10 (Science)">Class 10 (বিজ্ঞান)</option>
                                <option value="Class 10 (Commerce)">Class 10 (ব্যবসায় শিক্ষা)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.previous_school') }}</label>
                            <input type="text" name="previous_school" placeholder="Previous Institution"
                                   class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-900 focus:ring-blue-900 sm:text-sm py-2 px-3 border">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.inquiry_message') }}</label>
                        <textarea name="message" rows="3" placeholder="Tell us any questions or special accommodations..."
                                  class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-900 focus:ring-blue-900 sm:text-sm py-2 px-3 border"></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-100">
                        <button type="button" @click="admissionModalOpen = false"
                                class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-lg text-sm font-semibold bg-blue-900 text-white hover:bg-blue-800 shadow-md transition">
                            {{ __('messages.send_inquiry') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Public Website Footer -->
    <footer class="bg-slate-900 text-slate-300 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: Identity -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-800 flex items-center justify-center text-white font-bold">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                        </div>
                        <span class="text-lg font-bold text-white tracking-tight leading-snug">{{ __('messages.school_name') }}</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        {{ __('messages.hero_subtitle') }}
                    </p>
                    <div class="flex items-center space-x-3 text-slate-400 pt-2">
                        <span class="text-xs bg-slate-800 px-2 py-1 rounded border border-slate-700">EIIN: 134521</span>
                        <span class="text-xs bg-slate-800 px-2 py-1 rounded border border-slate-700">Board: Dhaka</span>
                    </div>
                </div>

                <!-- Col 2: Academics -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">{{ __('messages.academics') }}</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="{{ route('home') }}#academics" class="hover:text-emerald-400 transition">Junior High (Class 6 - 8)</a></li>
                        <li><a href="{{ route('home') }}#academics" class="hover:text-emerald-400 transition">Secondary SSC (Science & Business)</a></li>
                        <li><a href="{{ route('home') }}#academics" class="hover:text-emerald-400 transition">STEM & Robotics Lab</a></li>
                        <li><a href="{{ route('home') }}#academics" class="hover:text-emerald-400 transition">Bilingual Curriculum Standards</a></li>
                    </ul>
                </div>

                <!-- Col 3: Portal Links -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">{{ __('messages.portal_title') }}</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition">{{ __('messages.admin') }}</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition">{{ __('messages.teacher') }}</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition">{{ __('messages.parent') }}</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition">{{ __('messages.student') }}</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition">{{ __('messages.accountant') }}</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact & Location -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">{{ __('messages.contact') }}</h4>
                    <p class="text-xs text-slate-400 leading-relaxed mb-3">
                        House 12, Road 7/A, Dhanmondi R/A,<br>
                        Dhaka-1209, Bangladesh.
                    </p>
                    <p class="text-xs text-slate-400 mb-1">
                        <strong class="text-white">Email:</strong> admissions@apexacademy.edu.bd
                    </p>
                    <p class="text-xs text-slate-400 mb-4">
                        <strong class="text-white">Helpline:</strong> +880 1711 000000, +880 2 9876543
                    </p>
                    <button @click="admissionModalOpen = true" class="w-full text-center py-2 px-3 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition">
                        {{ __('messages.apply_now') }}
                    </button>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-slate-800 text-center text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p>&copy; {{ date('Y') }} {{ __('messages.school_name') }}. All rights reserved. Built with Laravel Blade + Tailwind CSS + Alpine.js.</p>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('locale.switch', 'en') }}" class="hover:text-white {{ app()->getLocale() === 'en' ? 'text-emerald-400 font-bold' : '' }}">English</a>
                    <span>•</span>
                    <a href="{{ route('locale.switch', 'bn') }}" class="hover:text-white {{ app()->getLocale() === 'bn' ? 'text-emerald-400 font-bold' : '' }}">বাংলা</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
