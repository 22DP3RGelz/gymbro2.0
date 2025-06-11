@extends('layouts.app')

@section('content')
<div class="container max-w-6xl mx-auto py-8 px-4">
    <div class="friends-layout">
        <div class="friends-box">
            <div class="section-header mb-6">
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Your Friends</h3>
                <div class="h-1 w-20 bg-blue-500 rounded-full mt-2"></div>
            </div>
            <div id="friendsList" class="friends-list space-y-4"></div>
        </div>

        <div class="search-box">
            <div class="search-section">
                <div class="section-header mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Add Friends</h3>
                    <div class="h-1 w-20 bg-blue-500 rounded-full mt-2"></div>
                </div>                <form class="search-form" onsubmit="event.preventDefault(); performSearch();">
                    <div class="relative flex flex-col gap-2">
                        <div class="relative">
                            <input type="text" 
                                   id="friendSearch" 
                                   placeholder="Enter username..." 
                                   class="search-input pl-10">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button type="submit" class="search-button">
                            Search
                        </button>
                    </div>
                </form>
                <div id="searchResults" class="search-results"></div>
            </div>

            <div class="requests-section mt-8">
                <div class="section-header mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Friend Requests</h3>
                    <div class="h-1 w-20 bg-blue-500 rounded-full mt-2"></div>
                </div>
                <div id="pendingRequests" class="pending-requests space-y-4"></div>
            </div>
        </div>
    </div>
</div>

<style>
.friends-layout {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    align-items: start;
}

.friends-box, .search-box {
    background: var(--surface);
    padding: 2rem;
    border-radius: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s, box-shadow 0.2s;
}

.friends-box:hover, .search-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 255, 255, 0.2);
}

.search-form {
    padding: 1rem;
}

.search-input {
    width: calc(100% - 2rem);
    margin: 0 1rem;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.75rem;
    font-size: 1rem;
    transition: all 0.3s;
    background: var(--surface-light);
    color: var(--text);
}

.search-button {
    width: 100%;
    padding: 0.75rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 0.75rem;
    font-weight: 500;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
}

.search-button:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
}

.search-results {
    margin-top: 1rem;
    border-radius: 0.75rem;
    overflow: hidden;
    display: none;
}

.search-result-item {
    padding: 0.75rem;
    background: var(--surface-light);
    margin-bottom: 0.5rem;
    border-radius: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.search-result-item:hover {
    background: var(--surface);
    transform: translateX(2px);
    border-color: rgba(255, 255, 255, 0.2);
}

.friend-item {
    background: var(--surface-light);
    padding: 0.75rem;
    border-radius: 0.75rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s;
    border: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 0.5rem;
}

.friend-item:hover {
    background: var(--surface);
    transform: translateX(2px);
    border-color: rgba(255, 255, 255, 0.2);
}

.friend-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.friend-name {
    font-weight: 500;
    color: var(--text);
}

.friend-streak {
    font-size: 0.875rem;
    color: var(--primary);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.add-friend-btn {
    background: var(--primary);
    color: white;
    border: none;
    padding: 0.4rem 0.75rem;
    border-radius: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.add-friend-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
}

.add-friend-btn.added {
    background: var(--success);
}

.remove-friend-btn {
    background: var(--error);
    color: white;
    border: none;
    padding: 0.4rem 0.75rem;
    border-radius: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.remove-friend-btn:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.accept-btn {
    background: var(--success);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.accept-btn:hover {
    background: #16a34a;
    transform: translateY(-1px);
}

.decline-btn {
    background: var(--error);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.decline-btn:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.status-text {
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
}

.status-text.added {
    color: var(--success);
    background: rgba(16, 185, 129, 0.1);
}

.status-text.pending {
    color: var(--warning);
    background: rgba(245, 158, 11, 0.1);
}

@media (max-width: 640px) {
    .friends-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function performSearch() {
    const searchTerm = document.getElementById('friendSearch').value.trim();
    const resultsDiv = document.getElementById('searchResults');

    // Show results container
    resultsDiv.style.display = 'block';
    
    if (searchTerm.length < 2) {
        resultsDiv.innerHTML = `
            <div class="search-result-item bg-yellow-50 border border-yellow-100">
                <div class="flex items-center text-yellow-800">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    Please enter at least 2 characters
                </div>
            </div>`;
        return;
    }

    resultsDiv.innerHTML = `
        <div class="search-result-item">
            <div class="flex items-center">
                <svg class="animate-spin h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Searching...
            </div>
        </div>`;

    fetch('/friends/search?query=' + encodeURIComponent(searchTerm), {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (!data.data || !Array.isArray(data.data) || data.data.length === 0) {
            resultsDiv.innerHTML = `
                <div class="search-result-item bg-gray-50 border border-gray-100">
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        No users found
                    </div>
                </div>`;
            return;
        }

        resultsDiv.innerHTML = data.data.map(user => `
            <div class="search-result-item">
                <div class="font-medium text-gray-200">${user.name}</div>
                <button onclick="sendFriendRequest(${user.id})" class="add-friend-btn flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Friend
                </button>
            </div>
        `).join('');
    })
    .catch(error => {
        console.error('Search error:', error);
        resultsDiv.innerHTML = `
            <div class="search-result-item bg-red-50 border border-red-100">
                <div class="flex items-center text-red-800">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Error performing search
                </div>
            </div>`;
    });
}

function sendFriendRequest(userId) {
    const button = event.target;
    button.disabled = true;
    button.textContent = 'Sending...';

    fetch('/friends/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ friend_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.parentElement.innerHTML = '<span class="status-text added">Added</span>';
            loadFriends();
        } else {
            throw new Error(data.message || 'Failed to add friend');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.textContent = 'Add Friend';
        alert(error.message || 'Failed to add friend');
    });
}

function loadFriends() {
    fetch('/get-friends')
        .then(response => response.json())
        .then(result => {
            const friendsListDiv = document.getElementById('friendsList');
            const friends = result.data || [];
            
            if (friends.length === 0) {
                friendsListDiv.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="mt-4 text-gray-500 text-lg">No friends added yet</p>
                        <p class="text-sm text-gray-400">Search for friends to get started!</p>
                    </div>`;
                return;
            }
            
            friendsListDiv.innerHTML = friends.map(friend => `
                <div class="friend-item">
                    <div class="friend-info">
                        <span class="friend-name">${friend.name}</span>
                        ${friend.streak ? `
                            <span class="friend-streak">
                                <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"/>
                                </svg>
                                ${friend.streak} day streak
                            </span>` : ''}
                    </div>
                    <button onclick="removeFriend(${friend.id})" class="remove-friend-btn flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Remove
                    </button>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading friends:', error);
            document.getElementById('friendsList').innerHTML = `
                <div class="bg-red-50 border border-red-100 rounded-lg p-4">
                    <div class="flex items-center text-red-800">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Error loading friends
                    </div>
                </div>`;
        });
}

function removeFriend(friendId) {
    if (!confirm('Are you sure you want to remove this friend?')) {
        return;
    }

    const button = event.target.closest('.remove-friend-btn');
    button.disabled = true;
      fetch(`/friends/${friendId}/remove`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to remove friend');
        }
        // Refresh the friends list
        loadFriends();
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        alert('Failed to remove friend. Please try again.');
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadFriends();
});
</script>

@endsection

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
