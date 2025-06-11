<template>
    <div class="relative">
        <div class="flex gap-2">
            <input 
                type="text" 
                v-model="searchQuery"
                class="form-input rounded-md shadow-sm mt-1 block w-full"
                placeholder="Search for friends..."
            />
            <button 
                @click="handleSearch"
                class="mt-1 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
            >
                Search
            </button>
        </div>
        
        <div v-if="showResults && (loading || results.length > 0)" 
             class="absolute z-50 w-full mt-1 bg-white rounded-md shadow-lg border">
            <div v-if="loading" class="p-2 text-gray-500">
                Searching...
            </div>
            <ul v-else class="max-h-60 overflow-auto">
                <li v-for="user in results" 
                    :key="user.id" 
                    class="p-2 hover:bg-gray-100 flex justify-between items-center">
                    <span>{{ user.name }}</span>
                    <button 
                        @click.prevent="addFriend(user)"
                        class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm"
                    >
                        Add Friend
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue'

export default {
    emits: ['user-selected', 'friend-added'],
    
    setup(props, { emit }) {
        const searchQuery = ref('')
        const results = ref([])
        const showResults = ref(false)
        const loading = ref(false)

        const handleSearch = async () => {
            if (!searchQuery.value.trim()) {
                results.value = []
                return
            }

            loading.value = true
            showResults.value = true
            
            try {
                const response = await axios.get(route('friends.search'), {
                    params: { query: searchQuery.value },
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                
                if (response.data && response.data.status === 'success' && Array.isArray(response.data.data)) {
                    results.value = response.data.data
                } else {
                    console.error('Invalid response format:', response.data)
                    results.value = []
                }
            } catch (error) {
                console.error('Search failed:', error)
                if (error.response) {
                    console.error('Error status:', error.response.status)
                    console.error('Error data:', error.response.data)
                }
                results.value = []
            } finally {
                loading.value = false
            }
        }

        const addFriend = async (user) => {
            try {
                const response = await axios.post(route('friends.add'), {
                    friend_id: user.id
                });
                
                if (response.data.success) {
                    // Remove the added user from results
                    results.value = results.value.filter(r => r.id !== user.id);
                    emit('friend-added', user);
                    alert('Friend added successfully!');
                }
            } catch (error) {
                console.error('Failed to add friend:', error);
                alert(error.response?.data?.message || 'Failed to add friend');
            }
        }

        return {
            searchQuery,
            results,
            showResults,
            loading,
            handleSearch,
            addFriend
        }
    }
}
</script>
