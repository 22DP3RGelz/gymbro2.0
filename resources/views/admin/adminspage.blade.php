@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <h2>Admin Dashboard</h2>
        
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <form action="{{ route('admin.updateName', $user->id) }}" method="POST" class="edit-name-form">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="name" value="{{ $user->name }}" class="edit-name-input">
                                <button type="submit" class="btn-small">Update</button>
                            </form>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')" class="btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.edit-name-form {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.edit-name-input {
    background: var(--primary-gray);
    border: 1px solid var(--accent-color);
    color: var(--text-light);
    padding: 0.3rem;
    border-radius: 4px;
}

.btn-small {
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
}

.btn-danger {
    background: #e74c3c;
}
</style>
@endsection
