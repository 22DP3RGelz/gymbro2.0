@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="friends-layout">
        <div class="friends-box">
            <h3>Your Friends</h3>
            <div id="friendsList" class="friends-list"></div>
        </div>

        <div class="search-box">
            <div class="search-section">
                <h3>Add Friends</h3>
                <input type="text" id="friendSearch" placeholder="Search users..." class="search-input">
                <div id="searchResults" class="search-results"></div>
            </div>

            <div class="requests-section">
                <h3>Friend Requests</h3>
                <div id="pendingRequests" class="pending-requests"></div>
            </div>
        </div>
    </div>
</div>

<style>
.friends-layout {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}

.friends-box, .search-box {
    flex: 1;
    background: var(--secondary-gray);
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.search-section {
    margin-bottom: 2rem;
}

.search-input {
    width: 100%;
    max-width: 250px;  /* Added max-width */
    padding: 0.8rem;
    border: 1px solid var(--accent-color);
    border-radius: 4px;
    background: var(--primary-gray);
    color: var(--text-light);
    margin: 1rem 0;
    font-size: 0.9rem;  /* Slightly smaller font */
}

.search-results {
    width: 100%;
    max-width: 250px;
    background: var(--primary-gray);
    border: 1px solid var(--accent-color);
    border-radius: 4px;
    margin-top: 0.5rem;
    max-height: 300px;
    overflow-y: auto;
    display: none;
}

.friend-item {
    background: var(--primary-gray);
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.friend-info {
    display: flex;
    flex-direction: column;
}

.friend-name {
    font-weight: bold;
    color: var(--text-light);
}

.friend-streak {
    font-size: 0.9rem;
    color: var(--accent-color);
}

.request-item {
    background: var(--primary-gray);
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.request-actions {
    display: flex;
    gap: 0.5rem;
}

.accept-btn {
    background: #2ecc71;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    color: white;
    cursor: pointer;
}

.decline-btn {
    background: #e74c3c;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    color: white;
    cursor: pointer;
}

.add-btn {
    background: var(--accent-color);
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-btn:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.error-message {
    color: #e74c3c;
    text-align: center;
    padding: 1rem;
}

.no-friends {
    color: var(--text-light);
    text-align: center;
    padding: 1rem;
}

.search-result-item {
    padding: 0.8rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--secondary-gray);
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item.error {
    color: #e74c3c;
    text-align: center;
    justify-content: center;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 500;
    color: var(--text-light);
}

.add-friend-btn {
    background: var(--accent-color);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-friend-btn.added {
    background: #2ecc71;
    cursor: default;
}

.add-friend-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>

<script>
function loadFriends() {
    fetch('/friends/list')
        .then(response => response.json())
        .then(result => {
            const listDiv = document.getElementById('friendsList');
            if (result.status === 'success') {
                const friends = result.data;
                if (friends.length === 0) {
                    listDiv.innerHTML = '<p class="no-friends">No friends added yet</p>';
                    return;
                }
                
                listDiv.innerHTML = '';
                friends.forEach(friend => {
                    listDiv.innerHTML += `
                        <div class="friend-item">
                            <div class="friend-info">
                                <span class="friend-name">${friend.name}</span>
                                <span class="friend-streak">🔥 ${friend.streak || 0} day streak</span>
                            </div>
                        </div>
                    `;
                });
            }
        });
}

function loadPendingRequests() {
    fetch('/friends/pending-requests')
        .then(response => response.json())
        .then(result => {
            const requestsDiv = document.getElementById('pendingRequests');
            if (result.status === 'success') {
                const requests = result.data;
                if (requests.length === 0) {
                    requestsDiv.innerHTML = '<p class="no-requests">No pending requests</p>';
                    return;
                }
                
                requestsDiv.innerHTML = '';
                requests.forEach(request => {
                    requestsDiv.innerHTML += `
                        <div class="request-item">
                            <span class="request-name">${request.name}</span>
                            <div class="request-actions">
                                <button onclick="handleRequest(${request.user_id}, 'accept')" class="accept-btn">Accept</button>
                                <button onclick="handleRequest(${request.user_id}, 'decline')" class="decline-btn">Decline</button>
                            </div>
                        </div>
                    `;
                });
            }
        });
}

// Initialize both lists when page loads
document.addEventListener('DOMContentLoaded', () => {
    loadFriends();
    loadPendingRequests();
});

// Refresh lists periodically
setInterval(() => {
    loadFriends();
    loadPendingRequests();
}, 30000);

// Add search state tracking
let foundUsers = new Map();

// Add persistent results tracking
let currentSearchResults = [];

// Update search functionality
document.getElementById('friendSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.trim();
    const resultsDiv = document.getElementById('searchResults');
    
    if (searchTerm.length < 2) {
        resultsDiv.style.display = 'none';
        return;
    }

    if (currentSearchResults.length > 0) {
        // Filter existing results first
        const filteredResults = currentSearchResults.filter(user => 
            user.name.toLowerCase().includes(searchTerm.toLowerCase())
        );
        
        if (filteredResults.length > 0) {
            displayResults(filteredResults, resultsDiv);
            return;
        }
    }

    // Only fetch if no matching results
    fetch(`/search-users?term=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(response => {
            if (response.success && response.data && response.data.length > 0) {
                currentSearchResults = response.data;
                displayResults(response.data, resultsDiv);
            } else if (currentSearchResults.length === 0) {
                resultsDiv.innerHTML = '<div class="search-result-item">No users found</div>';
            }
            resultsDiv.style.display = 'block';
        });
});

function displayResults(users, container) {
    const html = users.map(user => `
        <div class="search-result-item">
            <div class="user-info">
                <span class="user-name">${user.name}</span>
            </div>
            <button 
                onclick="sendFriendRequest(${user.id}, this)" 
                class="add-friend-btn"
                data-user-id="${user.id}">
                Add Friend
            </button>
        </div>
    `).join('');
    container.innerHTML = html;
}

// Update sendFriendRequest to handle state
function sendFriendRequest(userId, button) {
    button.disabled = true;
    button.textContent = 'Sending...';
    
    fetch('/friends/send-request/' + userId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            button.textContent = 'Add Friend';
            button.disabled = false;
        } else {
            button.textContent = 'Request Sent';
            button.classList.add('added');
            // Remove user from current results
            currentSearchResults = currentSearchResults.filter(user => user.id !== userId);
        }
    })
    .catch(() => {
        button.textContent = 'Add Friend';
        button.disabled = false;
    });
}
</script>
@endsection
