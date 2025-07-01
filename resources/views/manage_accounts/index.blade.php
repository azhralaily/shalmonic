@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Settings</h2>
    <p class="mb-4 text-gray-600">Manage user accounts and permissions</p>
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="email" placeholder="Enter email address" value="{{ request('email') }}" class="border rounded p-2 flex-1">
        <button class="bg-green-600 text-white px-4 py-2 rounded">Search</button>
    </form>
    <button onclick="document.getElementById('addModal').showModal()" class="bg-green-500 text-white px-4 py-2 rounded mb-4 float-right">Add Account</button>
    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td class="border px-4 py-2">{{ $user->id }}</td>
                <td class="border px-4 py-2">{{ $user->name }}</td>
                <td class="border px-4 py-2">{{ $user->email }}</td>
                <td class="border px-4 py-2">{{ $user->role }}</td>
                <td class="border px-4 py-2 flex gap-2">
                    <button onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')" class="text-green-600"><i class="fas fa-edit"></i></button>
                    @if($user->id != auth()->id())
                    <form action="{{ route('manage.accounts.delete') }}" method="POST" onsubmit="return confirm('Delete this user?')">
                        @csrf
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <button class="text-red-600"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Add Modal -->
    <dialog id="addModal" class="rounded p-4">
        <form method="POST" action="{{ route('manage.accounts.create') }}" class="space-y-2">
            @csrf
            <h3 class="font-bold text-lg mb-2">Add Account</h3>
            <input type="text" name="name" placeholder="Name" class="border rounded p-2 w-full" required>
            <input type="email" name="email" placeholder="Email" class="border rounded p-2 w-full" required>
            <input type="password" name="password" placeholder="Password" class="border rounded p-2 w-full" required>
            <select name="role" class="border rounded p-2 w-full">
                <option value="guest">Guest</option>
                <option value="operator">Operator</option>
                <option value="admin">Admin</option>
            </select>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="addModal.close()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button class="px-4 py-2 bg-green-600 text-white rounded">Add</button>
            </div>
        </form>
    </dialog>
    <!-- Edit Modal -->
    <dialog id="editModal" class="rounded p-4">
        <form method="POST" action="{{ route('manage.accounts.update') }}" class="space-y-2">
            @csrf
            <input type="hidden" name="id" id="edit_id">
            <h3 class="font-bold text-lg mb-2">Edit Account</h3>
            <input type="text" name="name" id="edit_name" placeholder="Name" class="border rounded p-2 w-full" required>
            <input type="email" name="email" id="edit_email" placeholder="Email" class="border rounded p-2 w-full" required>
            <select name="role" id="edit_role" class="border rounded p-2 w-full">
                <option value="guest">Guest</option>
                <option value="operator">Operator</option>
                <option value="admin">Admin</option>
            </select>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="editModal.close()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button class="px-4 py-2 bg-green-600 text-white rounded">Save</button>
            </div>
        </form>
    </dialog>
</div>
<script>
function editUser(id, name, email, role) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;
    document.getElementById('editModal').showModal();
}
</script>
@endsection 