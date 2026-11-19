@extends('layouts.portal')

@section('title', __('messages.admit_student'))
@section('breadcrumb', __('messages.admit_student'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-blue-950 tracking-tight">{{ __('messages.admit_student') }}</h1>
            <p class="text-xs text-slate-500 mt-1">Register institutional student credentials and assign initial class enrollment.</p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition flex items-center gap-1">
            ← {{ __('messages.student_directory') }}
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p class="font-medium">• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.students.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Student Personal Information -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-blue-950 uppercase tracking-wider border-b border-slate-100 pb-2">
                1. Student Profile & Credentials
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.student_name') }} *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Shakib Al Hasan"
                           class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.email_address') }} *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="student.login@school.test"
                           class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.gender') }} *</label>
                    <select name="gender" required class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male (ছাত্র)</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female (ছাত্রী)</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Date of Birth *</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', '2013-01-15') }}" required
                           class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.blood_group') }}</label>
                    <input type="text" name="blood_group" value="{{ old('blood_group') }}" placeholder="e.g. A+, B+, O+"
                           class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.phone_number') }}</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+8801700000000"
                           class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.emergency_phone') }}</label>
                    <input type="tel" name="emergency_contact" value="{{ old('emergency_contact') }}" placeholder="Emergency Guardian Contact"
                           class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Residential Address</label>
                <textarea name="address" rows="2" placeholder="Street Address, City, Postal Code"
                          class="w-full text-xs rounded-xl border border-slate-300 py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">{{ old('address') }}</textarea>
            </div>
        </div>

        <!-- 2. Academic Enrollment Configuration -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4"
             x-data="{ 
                 selectedClass: '{{ old('class_id', $classes->first()?->id) }}',
                 sectionsMap: {{ json_encode($classes->mapWithKeys(fn($c) => [$c->id => $c->sections])) }}
             }">
            <h3 class="text-sm font-bold text-blue-950 uppercase tracking-wider border-b border-slate-100 pb-2">
                2. Academic Enrollment Assignment
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Academic Year *</label>
                    <select name="academic_year_id" required class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}">{{ $ay->name }} (Active)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Class *</label>
                    <select name="class_id" x-model="selectedClass" required class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Class Section *</label>
                    <select name="section_id" required class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                        <template x-for="sec in (sectionsMap[selectedClass] || [])" :key="sec.id">
                            <option :value="sec.id" x-text="sec.name + ' (' + (sec.room_number || 'Main') + ')'"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">{{ __('messages.roll_number') }} *</label>
                    <input type="number" name="roll_number" value="{{ old('roll_number', 10) }}" min="1" max="200" required
                           class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
            </div>
        </div>

        <!-- 3. Guardian / Parent Association -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-blue-950 uppercase tracking-wider border-b border-slate-100 pb-2">
                3. Guardian Association (Optional Linkage)
            </h3>
            <p class="text-xs text-slate-500">Associate with an existing verified parent account to enable instant portal tracking.</p>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Existing Guardian Account</label>
                <select name="parent_id" class="w-full text-xs rounded-xl border border-slate-300 py-2.5 px-3 focus:outline-none focus:ring-2 focus:ring-blue-900">
                    <option value="">-- No guardian linkage at this time --</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->user->name }} ({{ $p->user->email }} - {{ $p->relationship }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('admin.students.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-blue-900 text-white hover:bg-blue-800 shadow-md transition">
                Complete Admission & Enrollment
            </button>
        </div>
    </form>
</div>
@endsection
