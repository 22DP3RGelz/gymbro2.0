@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Admin Dashboard</div>

                <div class="card-body">
                    <div class="user-list">
                        <div class="list-header">
                            <h2>Users</h2>
                            <div class="sort-controls">
                                <div class="user-stats">
                                    <span class="stat-item">👑 Admins: {{ $adminCount }}</span>
                                    <span class="stat-item">👤 Users: {{ $userCount }}</span>
                                </div>
                                <a href="{{ route('adminspage', ['sort' => $currentSort == 'asc' ? 'desc' : 'asc']) }}" 
                                   class="sort-button">
                                    {{ $currentSort == 'asc' ? '↓ A-Z' : '↑ Z-A' }}
                                </a>
                            </div>
                        </div>

                        <div class="user-table">
                            <div class="table-header">
                                <span>Name</span>
                                <span>Email</span>
                                <span>Joined</span>
                                <span>Streak</span>
                                <span>Actions</span>
                            </div>
                            
                            @foreach($users as $user)
                            <div class="user-row" id="user-{{ $user->id }}">
                                <span class="user-name" id="name-{{ $user->id }}">{{ $user->name }}</span>
                                <span class="user-email">{{ $user->email }}</span>
                                <span class="user-joined">{{ $user->joined }}</span>
                                <span class="user-streak">🔥 {{ $user->streak ?? 0 }}</span>
                                <div class="user-actions">
                                    <button onclick="editUserName({{ $user->id }})" class="edit-btn">Edit</button>
                                    <button onclick="deleteUser({{ $user->id }})" class="delete-btn">Delete</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.info {
    color: var(--text-light);
    font-size: 0.9rem;
}

.user-list {
    background: var(--surface);
    border-radius: 1rem;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.user-table {
    background: var(--primary-gray);
    border-radius: 8px;
    overflow: hidden;
}

.table-header, .user-row {
    display: grid;
    grid-template-columns: 2fr 3fr 1fr 1fr 2fr;
    padding: 1rem 1.5rem;
    align-items: center;
    gap: 1rem;
}

.table-header {
    background: var(--surface-light);
    color: var(--text);
    font-weight: 600;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.user-row {
    transition: background-color 0.2s;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.user-row:hover {
    background: var(--surface-light);
}

.user-row:last-child {
    border-bottom: none;
}

.user-details {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.user-name {
    font-weight: 600;
    color: var(--text);
}

.user-email {
    color: var(--text-light);
}

.user-streak {
    color: var(--primary);
    font-weight: 500;
}

.sort-controls {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.user-stats {
    display: flex;
    gap: 1rem;
    color: var(--text-light);
    font-size: 0.9rem;
}

.stat-item {
    padding: 0.5rem 1rem;
    background: var(--surface-light);
    border-radius: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.sort-button {
    padding: 0.5rem 1rem;
    background: var(--primary);
    color: white;
    border-radius: 0.5rem;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.sort-button:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
}

.user-actions {
    display: flex;
    gap: 0.5rem;
}

.edit-btn, .delete-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.edit-btn {
    background: var(--primary);
    color: white;
}

.edit-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.delete-btn {
    background: var(--error);
    color: white;
}

.delete-btn:hover {
    background: #dc2626;
    transform: translateY(-1px);
}
</style>

<script>
function editUserName(userId) {
    const nameElement = document.getElementById(`name-${userId}`);
    const currentName = nameElement.textContent;
    const newName = prompt('Enter new name:', currentName);
    
    if (newName && newName !== currentName) {
        fetch(`/admin/users/${userId}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: newName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                nameElement.textContent = newName;
                alert('Name updated successfully');
            }
        })
        .catch(error => alert('Error updating name'));
    }
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`user-${userId}`).remove();
                alert('User deleted successfully');
            }
        })
        .catch(error => alert('Error deleting user'));
    }
}
</script>
@endsection
