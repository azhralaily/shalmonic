<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            @if(Auth::user()->role === 'admin')
                <div class="mt-4">
                    <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
                    <select name="role" id="role" class="form-select mt-1 block w-full">
                        <option value="admin" {{ Auth::user()->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="operator" {{ Auth::user()->role == 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="guest" {{ Auth::user()->role == 'guest' ? 'selected' : '' }}>Guest</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
