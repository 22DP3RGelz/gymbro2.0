<div class="workout-schedule">
    <form id="workoutForm">
        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
            <div class="form-check">
                <input type="checkbox" 
                       class="form-check-input" 
                       name="{{ $day }}" 
                       id="{{ $day }}"
                       {{ $schedule->$day ? 'checked' : '' }}
                       {{ $schedule->is_locked ? 'disabled' : '' }}
                       onchange="saveChanges()">
                <label class="form-check-label" for="{{ $day }}">
                    {{ ucfirst($day) }}
                </label>
            </div>
        @endforeach
        
        <button type="button" 
                id="lockSchedule" 
                class="btn btn-primary mt-3"
                {{ $schedule->is_locked ? 'disabled' : '' }}>
            Lock Schedule for Week
        </button>
    </form>
</div>

<script>
function saveChanges() {
    const formData = new FormData(document.getElementById('workoutForm'));
    const days = {};
    formData.forEach((value, key) => {
        days[key] = value === 'on';
    });

    fetch('/update-workout-days', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(days)
    });
}

document.getElementById('lockSchedule').addEventListener('click', () => {
    fetch('/lock-workout-schedule', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            document.querySelectorAll('#workoutForm input[type="checkbox"]').forEach(input => {
                input.disabled = true;
            });
            document.getElementById('lockSchedule').disabled = true;
        }
    });
});
</script>
