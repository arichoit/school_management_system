<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('messages.portal_title')) - {{ config('app.name', 'SMS') }}</title>

    <!-- Inter & Bangla Fonts -->
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
<body class="h-full antialiased font-sans text-slate-800"
      x-data="{
          sidebarOpen: false,
          sidebarCollapsed: false,
          profileMenuOpen: false,
          childSwitcherOpen: false,
          toast: {
              show: false,
              message: '{{ session('success') ?? session('error') ?? session('info') ?? '' }}',
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
         class="fixed top-5 right-5 z-50 max-w-sm w-full shadow-xl rounded-xl pointer-events-auto border overflow-hidden p-4 flex items-center space-x-3 no-print"
         :class="{
             'bg-emerald-50 border-emerald-300 text-emerald-900': toast.type === 'success',
             'bg-rose-50 border-rose-300 text-rose-900': toast.type === 'error',
             'bg-blue-50 border-blue-300 text-blue-900': toast.type === 'info'
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

    @php
        $user = auth()->user();
        $role = $user->role;
        $roleLabel = match($role) {
            'admin' => __('messages.admin'),
            'teacher' => __('messages.teacher'),
            'parent' => __('messages.parent'),
            'student' => __('messages.student'),
            'accountant' => __('messages.accountant'),
            default => 'User'
        };
        $roleBadgeColor = match($role) {
            'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
            'teacher' => 'bg-blue-100 text-blue-800 border-blue-200',
            'parent' => 'bg-amber-100 text-amber-800 border-amber-200',
            'student' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'accountant' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200'
        };
    @endphp

    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-900/60 z-40 lg:hidden no-print" 
             x-cloak></div>

        <!-- Sidebar Navigation -->
        <aside :class="{
                   'translate-x-0': sidebarOpen,
                   '-translate-x-full': !sidebarOpen,
                   'lg:w-64': !sidebarCollapsed,
                   'lg:w-20': sidebarCollapsed
               }" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-blue-950 text-white flex flex-col transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:h-screen lg:sticky lg:top-0 shadow-xl border-r border-blue-900 flex-shrink-0 no-print">
            
            <!-- Brand / Header -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-blue-900/60 bg-blue-950">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl bg-blue-800 flex items-center justify-center text-white font-bold flex-shrink-0 shadow-inner">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <div x-show="!sidebarCollapsed" class="whitespace-nowrap transition-all duration-200">
                        <span class="block text-base font-extrabold tracking-tight text-white leading-tight">Apex Academy</span>
                        <span class="block text-[11px] font-semibold text-emerald-400 uppercase tracking-wider">SMS Portal</span>
                    </div>
                </a>
                <!-- Collapse button on tablet/desktop -->
                <button @click="sidebarCollapsed = !sidebarCollapsed" 
                        class="hidden lg:flex p-1.5 rounded-lg text-blue-300 hover:text-white hover:bg-blue-900/50 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        <path x-show="sidebarCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" x-cloak />
                    </svg>
                </button>
                <!-- Mobile close button -->
                <button @click="sidebarOpen = false" class="lg:hidden text-blue-300 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Role Badge Banner -->
            <div x-show="!sidebarCollapsed" class="px-5 py-3 bg-blue-900/30 border-b border-blue-900/40">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-blue-300 font-medium">{{ __('messages.role') }}:</span>
                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[11px] uppercase tracking-wide bg-blue-800 text-emerald-300 border border-blue-700">
                        {{ $role }}
                    </span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                {{-- SUPER ADMIN ROUTES --}}
                @if($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.dashboard') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.dashboard') }}</span>
                    </a>

                    <a href="{{ route('admin.students.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.students.*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.student_directory') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.student_directory') }}</span>
                    </a>

                    <a href="{{ route('admin.academics.classes') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.academics.classes*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.class_management') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.class_management') }}</span>
                    </a>

                    <a href="{{ route('admin.academics.timetable') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.academics.timetable*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.timetable_matrix') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.timetable_matrix') }}</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.staff_management') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.staff_management') }}</span>
                    </a>
                @endif

                {{-- TEACHER ROUTES --}}
                @if($role === 'teacher')
                    <a href="{{ route('teacher.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('teacher.dashboard') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.teacher_dashboard') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.teacher_dashboard') }}</span>
                    </a>

                    <a href="{{ route('teacher.attendance.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('teacher.attendance.*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.daily_attendance') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.daily_attendance') }}</span>
                    </a>

                    <a href="{{ route('teacher.grades.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('teacher.grades.*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.exam_grades') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.exam_grades') }}</span>
                    </a>
                @endif

                {{-- PARENT ROUTES --}}
                @if($role === 'parent')
                    <a href="{{ route('parent.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('parent.dashboard') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.parent_dashboard') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.parent_dashboard') }}</span>
                    </a>

                    <a href="{{ route('parent.fees.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('parent.fees.*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.fee_invoices') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.fee_invoices') }}</span>
                    </a>

                    <a href="{{ route('parent.homework.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('parent.homework.*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.homework_tasks') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.homework_tasks') }}</span>
                    </a>
                @endif

                {{-- STUDENT ROUTES --}}
                @if($role === 'student')
                    <a href="{{ route('student.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('student.dashboard') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.student_dashboard') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.student_dashboard') }}</span>
                    </a>

                    <a href="{{ route('student.grades.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('student.grades.*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.report_card') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.report_card') }}</span>
                    </a>
                @endif

                {{-- ACCOUNTANT ROUTES --}}
                @if($role === 'accountant')
                    <a href="{{ route('accountant.dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('accountant.dashboard') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.accountant_dashboard') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.accountant_dashboard') }}</span>
                    </a>

                    <a href="{{ route('accountant.fees.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('accountant.fees.*') ? 'bg-blue-800 text-white shadow-sm' : 'text-blue-200 hover:bg-blue-900/50 hover:text-white' }}"
                       :title="sidebarCollapsed ? '{{ __('messages.fee_structure_setup') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.fee_structure_setup') }}</span>
                    </a>
                @endif

                <div class="pt-4 mt-4 border-t border-blue-900/50">
                    <a href="{{ route('home') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-blue-300 hover:bg-blue-900/50 hover:text-white transition"
                       :title="sidebarCollapsed ? '{{ __('messages.school_name') }}' : ''">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        <span x-show="!sidebarCollapsed">{{ __('messages.home') }} (Website)</span>
                    </a>
                </div>
            </nav>

            <!-- Bottom User Card in Sidebar -->
            <div class="p-4 border-t border-blue-900/60 bg-blue-950/80">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ $user->name }}</p>
                        <p class="text-xs text-blue-300 truncate">{{ $user->email }}</p>
                    </div>
                    <form x-show="!sidebarCollapsed" action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="{{ __('messages.logout') }}" class="text-blue-300 hover:text-rose-400 p-1.5 rounded-lg hover:bg-blue-900/40 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Portal Container -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navigation Bar -->
            <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm no-print">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <!-- Left: Hamburger (mobile) + Breadcrumbs -->
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = true" 
                                class="lg:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>

                        <div class="hidden sm:flex items-center space-x-2 text-xs font-medium text-slate-500">
                            <a href="{{ route('home') }}" class="hover:text-blue-900 transition">{{ __('messages.portal_title') }}</a>
                            <span>/</span>
                            <span class="capitalize text-blue-900 font-semibold">{{ $role }}</span>
                            @hasSection('breadcrumb')
                                <span>/</span>
                                <span class="text-slate-800 font-bold">@yield('breadcrumb')</span>
                            @endif
                        </div>
                    </div>

                    <!-- Right Controls: Child Switcher (if parent), Language, Academic Year, Profile -->
                    <div class="flex items-center space-x-3">
                        <!-- Multi-Child Switcher Dropdown (Parent Role) -->
                        @if($role === 'parent' && isset($children) && $children->count() > 1)
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        type="button" 
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 border border-amber-200 text-amber-900 hover:bg-amber-100 transition shadow-xs">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <span>{{ $activeChild->user->name ?? __('messages.select_child') }}</span>
                                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>

                                <div x-show="open" 
                                     @click.away="open = false" 
                                     class="origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-slate-100 z-50 p-1"
                                     x-cloak>
                                    <div class="px-3 py-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        {{ __('messages.select_child') }}
                                    </div>
                                    @foreach($children as $child)
                                        <form action="{{ route('parent.switch-child', $child->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full text-left px-3 py-2 text-xs rounded-lg flex items-center justify-between transition {{ (isset($activeChild) && $activeChild->id === $child->id) ? 'bg-amber-50 text-amber-900 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                                <div>
                                                    <p class="font-medium">{{ $child->user->name }}</p>
                                                    <p class="text-[10px] text-slate-400">{{ $child->currentEnrollment?->schoolClass->name }} - {{ $child->currentEnrollment?->section->name }}</p>
                                                </div>
                                                @if(isset($activeChild) && $activeChild->id === $child->id)
                                                    <span class="text-amber-600 font-bold">✓</span>
                                                @endif
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Academic Year Badge -->
                        <div class="hidden md:inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            2026-2027
                        </div>

                        <!-- Bilingual Switcher -->
                        <a href="{{ route('locale.switch', app()->getLocale() === 'en' ? 'bn' : 'en') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                            <svg class="w-3.5 h-3.5 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                            <span>{{ app()->getLocale() === 'en' ? 'বাংলা' : 'English' }}</span>
                        </a>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    type="button" 
                                    class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-slate-100 transition focus:outline-none"
                                    aria-expanded="false" 
                                    :aria-expanded="open.toString()">
                                <div class="w-8 h-8 rounded-full bg-blue-900 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="hidden sm:inline text-xs font-semibold text-slate-700 max-w-[120px] truncate">
                                    {{ $user->name }}
                                </span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-slate-100 z-50 p-1"
                                 x-cloak>
                                <div class="px-4 py-3">
                                    <p class="text-xs font-medium text-slate-500">{{ __('messages.welcome_back') }}</p>
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ $user->name }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold rounded {{ $roleBadgeColor }} border">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                                <div class="py-1">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-lg transition flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            <span>{{ __('messages.logout') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Main Content Body -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">
                <div class="max-w-7xl mx-auto space-y-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
