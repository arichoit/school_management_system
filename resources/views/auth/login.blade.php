@extends('layouts.public')

@section('content')
<div class="min-h-[80vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="inline-flex w-14 h-14 rounded-2xl bg-blue-900 items-center justify-center text-white shadow-lg shadow-blue-900/20 mb-4">
            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-blue-950 tracking-tight">
            {{ __('messages.portal_title') }}
        </h2>
        <p class="mt-2 text-sm text-slate-500">
            Sign in to access your role-specific dashboard and academic records
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl rounded-2xl border border-slate-200">
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="font-medium">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form class="space-y-5" action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        {{ __('messages.email_address') }}
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email', 'admin@school.test') }}"
                               class="appearance-none block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl shadow-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               value="password"
                               class="appearance-none block w-full px-3.5 py-2.5 border border-slate-300 rounded-xl shadow-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900 text-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-blue-900 focus:ring-blue-900 border-slate-300 rounded">
                        <label for="remember" class="ml-2 block text-slate-700 font-medium">
                            Remember my session
                        </label>
                    </div>
                    <span class="text-slate-400">Institutional SSO</span>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-blue-900 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900 transition">
                        {{ __('messages.login') }}
                    </button>
                </div>
            </form>

            <!-- Quick Login Demo Switchers -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center mb-3">
                    Fast Demo Role Quick-Fill
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <button type="button" onclick="fillLogin('admin@school.test')"
                            class="p-2 text-center rounded-lg border border-purple-200 bg-purple-50 text-purple-900 hover:bg-purple-100 transition text-[11px] font-bold">
                        👑 Principal
                    </button>
                    <button type="button" onclick="fillLogin('teacher@school.test')"
                            class="p-2 text-center rounded-lg border border-blue-200 bg-blue-50 text-blue-900 hover:bg-blue-100 transition text-[11px] font-bold">
                        👨‍🏫 Teacher
                    </button>
                    <button type="button" onclick="fillLogin('parent@school.test')"
                            class="p-2 text-center rounded-lg border border-amber-200 bg-amber-50 text-amber-900 hover:bg-amber-100 transition text-[11px] font-bold">
                        👨‍👧 Parent
                    </button>
                    <button type="button" onclick="fillLogin('student@school.test')"
                            class="p-2 text-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-900 hover:bg-emerald-100 transition text-[11px] font-bold">
                        🎓 Student
                    </button>
                    <button type="button" onclick="fillLogin('accountant@school.test')"
                            class="p-2 text-center rounded-lg border border-cyan-200 bg-cyan-50 text-cyan-900 hover:bg-cyan-100 transition text-[11px] font-bold">
                        💼 Accountant
                    </button>
                    <a href="{{ route('home') }}"
                       class="p-2 text-center rounded-lg border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 transition text-[11px] font-bold flex items-center justify-center">
                        ← Back Home
                    </a>
                </div>
                <p class="mt-3 text-[11px] text-center text-slate-400">
                    Default password for all demo accounts: <strong class="text-slate-600">password</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function fillLogin(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
        document.getElementById('loginForm').submit();
    }
</script>
@endsection
