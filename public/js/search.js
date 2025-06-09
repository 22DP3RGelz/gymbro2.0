document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('friendSearch');
    const searchResults = document.getElementById('searchResults');

    if (searchInput) {
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const searchTerm = this.value.trim();
                if (searchTerm.length >= 2) {
                    fetchUsers(searchTerm);
                } else {
                    searchResults.style.display = 'none';
                }
            }, 300);
        });

        async function fetchUsers(searchTerm) {
            try {
                const response = await fetch(`/search-users?term=${searchTerm}`);
                const users = await response.json();
                
                if (users.length > 0) {
                    displayResults(users);
                } else {
                    searchResults.innerHTML = '<div class="search-result-item">No users found</div>';
                    searchResults.style.display = 'block';
                }
            } catch (error) {
                console.error('Error fetching users:', error);
            }
        }

        function displayResults(users) {
            searchResults.innerHTML = users.map(user => `
                <div class="search-result-item">
                    <span>${user.name}</span>
                    <button class="add-friend-btn" onclick="addFriend(${user.id})">Add Friend</button>
                </div>
            `).join('');
            searchResults.style.display = 'block';
        }

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }
});

async function addFriend(userId) {
    try {
        const response = await fetch('/add-friend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        const result = await response.json();
        if (result.success) {
            alert('Friend request sent!');
        }
    } catch (error) {
        console.error('Error adding friend:', error);
    }
}
