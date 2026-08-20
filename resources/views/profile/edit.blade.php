@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')
<section id="sigap-profile" class="sigap-page">
    <div class="sigap-page__header">
        <div class="flex items-center gap-4">
            <span class="sigap-avatar !w-14 !h-14 !text-xl">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            <div>
                <h1 class="sigap-page__title">{{ $user->name }}</h1>
                <p class="sigap-page__subtitle">{{ $user->email }}</p>
            </div>
        </div>
        <span class="sigap-badge sigap-badge--role-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
    </div>

    <div class="flex flex-col gap-6 max-w-3xl">
        @include('profile.partials.update-profile-information-form')

        @include('profile.partials.update-password-form')

        @include('profile.partials.delete-user-form')
    </div>
</section>
@endsection