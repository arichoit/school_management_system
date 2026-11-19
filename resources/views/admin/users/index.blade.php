@extends('layouts.portal')

@section('title', __('messages.staff_management'))
@section('breadcrumb', __('messages.staff_management'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.staff_management') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Manage institutional user accounts, role allocations, and security statuses.</p>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('messages.search') }} Name or Email Address"
                       class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
            </div>
            <div class="sm:col-span-4">
                <select name="role" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    <option value="">All Institutional Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Super Admin / Principal</option>
                    <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher (Faculty)</option>
                    <option value="accountant" {{ request('role') === 'accountant' ? 'selected' : '' }}>Accountant / Finance</option>
                    <option value="parent" {{ request('role') === 'parent' ? 'selected' : '' }}>Parent / Guardian</option>
                    <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full py-2 px-3 text-xs font-bold rounded-xl bg-slate-800 text-white hover:bg-slate-700 transition">
                    {{ __('messages.filter') }}
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="py-2 px-3 text-xs font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Desktop View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">User</th>
                        <th class="px-6 py-3.5">Assigned Role</th>
                        <th class="px-6 py-3.5">Phone</th>
                        <th class="px-6 py-3.5">Locale</th>
                        <th class="px-6 py-3.5">Account Status</th>
                        <th class="px-6 py-3.5 text-right">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @foreach($users as $u)
                        @php
                            $roleBadge = match($u->role) {
                                'admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'teacher' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'parent' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'student' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'accountant' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $u->name }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $roleBadge }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium whitespace-nowrap">
                                {{ $u->phone ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 uppercase font-bold text-slate-500">
                                {{ $u->locale ?? 'en' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        Deactivated
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-status', $u->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-2.5 py-1 text-[11px] font-bold rounded-lg border transition {{ $u->is_active ? 'border-rose-200 text-rose-700 hover:bg-rose-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}">
                                            {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400 italic">Self (Principal)</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Cards (<768px) -->
        <div class="md:hidden divide-y divide-slate-200">
            @foreach($users as $u)
                <div class="p-4 space-y-2 hover:bg-slate-50 transition">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded border {{ match($u->role) { 'admin' => 'bg-purple-100 text-purple-800 border-purple-200', 'teacher' => 'bg-blue-100 text-blue-800 border-blue-200', 'parent' => 'bg-amber-100 text-amber-800 border-amber-200', 'student' => 'bg-emerald-100 text-emerald-800 border-emerald-200', default => 'bg-slate-100 text-slate-800 border-slate-200' } }}">
                            {{ $u->role }}
                        </span>
                        @if($u->is_active)
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Active</span>
                        @else
                            <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded">Deactivated</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $u->name }}</p>
                        <p class="text-xs text-slate-400">{{ $u->email }}</p>
                    </div>
                    @if($u->id !== auth()->id())
                        <div class="pt-2 flex justify-end">
                            <form action="{{ route('admin.users.toggle-status', $u->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-bold underline {{ $u->is_active ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $u->is_active ? 'Deactivate Account' : 'Activate Account' }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
