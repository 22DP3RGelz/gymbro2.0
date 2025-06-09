@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="search-container">
            <input type="text" 
                   id="userSearch" 
                   class="search-input" 
                   placeholder="Search users by name..."
                   autocomplete="off">
            <div id="searchResults" class="search-results"></div>
        </div>
    </div>
</div>

<script>
document.getElementById('userSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.trim();
    const resultsDiv = document.getElementById('searchResults');
    
    if (searchTerm.length < 2) {
        resultsDiv.style.display = 'none';
        return;
    }

    fetch(`/search-users?term=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(response => {
            if (response.success && response.data.length > 0) {
                const html = response.data.map(user => `
                    <div class="search-result-item">
                        <span class="user-name">${user.name}</span>
                        <button 
                            onclick="addFriend(${user.id}, this)" 
                            class="add-friend-btn"
                            ${user.buttonState === 'added' ? 'disabled' : ''}>
                            ${user.buttonState === 'added' ? 'Added' : 'Add Friend'}
                        </button>
                    </div>
                `).join('');
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<div class="search-result-item">No users found</div>';
            }
            resultsDiv.style.display = 'block';
        });
});

function addFriend(userId, button) {
    button.disabled = true;
    button.textContent = 'Adding...';
    
    fetch('/add-friend', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ friend_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.textContent = 'Added';
            button.classList.add('added');
        } else {
            button.textContent = 'Add Friend';
            button.disabled = false;
        }
    })
    .catch(() => {
        button.textContent = 'Add Friend';
        button.disabled = false;
    });
}
</script>

<style>
.user-info {
    display: flex;
    flex-direction: column;
}

.user-email {
    font-size: 0.8em;
    color: #666;
}

.search-result-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.add-friend-btn {
    padding: 8px 16px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-friend-btn.added {
    background-color: #2ecc71;
    cursor: default;
}

.add-friend-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
@endsection
