@extends('layouts.app')

@section('content')
<div class="layout-container">
    <div class="main-content">
        <h2>My Weekly Plan</h2>
        <div class="week-grid">
            @php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            @endphp

            @foreach($days as $index => $day)
                <div class="day-box {{ isset($plan->week[$index]) && $plan->week[$index] ? 'checked' : '' }}">
                    <h3>{{ $day }}</h3>
                    <div class="checkbox-container">
                        <input type="checkbox" 
                               id="day-{{ $index }}" 
                               {{ isset($plan->week[$index]) && $plan->week[$index] ? 'checked' : '' }}
                               onchange="updateDay({{ $index }})">
                        <label for="day-{{ $index }}"></label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="friends-sidebar">
        <h2>Friends</h2>
        <div class="friend-search">
            <input type="text" id="friendSearch" placeholder="Search users..." class="search-input">
            <div id="searchResults" class="search-results"></div>
        </div>
        <div class="friends-list">
            @forelse($friends as $friend)
                <div class="friend-card">
                    <div class="friend-info">
                        <h3>{{ $friend->name }}</h3>
                        <p class="streak-count">Streak: {{ $friend->streak ?? 0 }} days</p>
                    </div>
                    <button onclick="confirmRemoveFriend({{ $friend->id }}, '{{ $friend->name }}')" class="remove-btn">&times;</button>
                    <form id="remove-friend-{{ $friend->id }}" action="{{ route('friends.remove', $friend->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            @empty
                <p>No friends added yet</p>
            @endforelse
        </div>
    </div>
</div>

<style>
.layout-container {
    display: flex;
    min-height: calc(100vh - 130px); /* Adjust based on header/footer height */
    margin-top: -2rem; /* Remove default margin */
}

.main-content {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
}

.week-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.day-box {
    background: var(--secondary-gray);
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    transition: transform 0.3s ease;
}

.day-box:hover {
    transform: translateY(-5px);
}

.day-box.checked {
    border: 2px solid var(--accent-color);
}

.checkbox-container {
    margin-top: 1rem;
}

.checkbox-container input[type="checkbox"] {
    display: none;
}

.checkbox-container label {
    display: inline-block;
    width: 30px;
    height: 30px;
    border: 2px solid var(--accent-color);
    border-radius: 4px;
    cursor: pointer;
    position: relative;
}

.checkbox-container input[type="checkbox"]:checked + label::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--accent-color);
    font-size: 1.2rem;
}

.friends-sidebar {
    width: 300px;
    background: var(--secondary-gray);
    padding: 1.5rem;
    height: 100%;
    position: sticky;
    top: 0;
    right: 0;
    overflow-y: auto;
    box-shadow: -2px 0 5px rgba(0,0,0,0.1);
}

.friend-search {
    margin: 1rem 0;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid var(--accent-color);
    border-radius: 4px;
    background: var(--primary-gray);
    color: var(--text-light);
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--secondary-gray);
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    z-index: 1000;
    display: none;
}

.search-result-item {
    padding: 0.5rem;
    border-bottom: 1px solid var(--primary-gray);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.friend-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.8rem;
    margin-bottom: 0.5rem;
    background: var(--primary-gray);
    border-radius: 6px;
}

.friend-info {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.friend-info h3 {
    margin: 0;
    font-size: 1rem;
    color: var(--accent-color);
}

.streak-count {
    margin: 0;
    font-size: 0.8rem;
    color: var(--text-light);
    opacity: 0.8;
}

.remove-btn {
    background: none;
    border: none;
    color: #e74c3c;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.2rem 0.5rem;
    transition: all 0.2s ease;
}

.remove-btn:hover {
    transform: scale(1.2);
    color: #c0392b;
}

@media (max-width: 768px) {
    .layout-container {
        flex-direction: column;
    }
    
    .friends-sidebar {
        width: 100%;
        height: auto;
        position: static;
    }
}
</style>

<script>
function updateDay(dayIndex) {
    if (!window.confirm('Mark this day as completed?')) {
        return;
    }

    fetch(`/plans/check-day`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ day_index: dayIndex })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`#day-${dayIndex}`).parentElement.parentElement.classList.add('checked');
        }
    });
}

const searchInput = document.getElementById('friendSearch');
const searchResults = document.getElementById('searchResults');
let searchTimeout;

searchInput.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const query = searchInput.value;
        if (query.length >= 2) {
            fetch(`/friends/search?query=${encodeURIComponent(query)}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(users => {
                searchResults.innerHTML = '';
                searchResults.style.display = users.length ? 'block' : 'none';
                
                users.forEach(user => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.innerHTML = `
                        ${user.name}
                        <form action="/friends/${user.id}/add" method="POST" style="display: inline">
                            @csrf
                            <button type="submit">Add Friend</button>
                        </form>
                    `;
                    searchResults.appendChild(div);
                });
            });
        } else {
            searchResults.style.display = 'none';
        }
    }, 300);
});

document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.style.display = 'none';
    }
});

function confirmRemoveFriend(id, name) {
    if (confirm(`Are you sure you want to remove ${name} from your friends?`)) {
        document.getElementById(`remove-friend-${id}`).submit();
    }
}
</script>
@endsection
