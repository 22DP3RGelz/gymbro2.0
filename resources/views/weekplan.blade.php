@extends('layouts.app')

@section('content')
<link href="{{ asset('css/weekplan.css') }}" rel="stylesheet">

<div class="container py-4">
    <div class="weekplan-layout">
        <div class="days-container">
            <div class="days-column">
                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                    <div class="day-box {{ optional($schedule)->$day ? 'selected' : '' }} 
                                {{ optional($schedule)->is_locked ? 'locked' : '' }}">
                        <div class="day-title">{{ ucfirst($day) }}</div>
                        <div class="form-check">
                            <input type="checkbox" 
                                class="form-check-input" 
                                id="{{ $day }}" 
                                name="{{ $day }}" 
                                {{ optional($schedule)->$day ? 'checked' : '' }}
                                {{ optional($schedule)->is_locked ? 'disabled' : '' }}
                                onchange="saveDay(this)">
                            <label class="form-check-label" for="{{ $day }}">
                                Select for workout
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            <button id="lockWeek" class="lock-button" {{ optional($schedule)->is_locked ? 'disabled' : '' }}>
                Lock Week Plan
            </button>
        </div>
    </div>
</div>

<style>
.weekplan-layout {
    display: flex;
    gap: 2rem;
    align-items: stretch;
    max-width: 800px;  /* Adjusted width since we removed friends box */
    margin: 0 auto;
}

.days-container {
    flex: 1;
    background: var(--secondary-gray);
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.days-column {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.day-box {
    background: var(--primary-gray);
    padding: 1.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.day-box:hover {
    transform: translateX(10px);
    box-shadow: -4px 4px rgba(0,0,0,0.1);
}

.lock-button {
    margin-top: 2rem;
    align-self: center;
    min-width: 200px;
}
</style>

<script>
function saveDay(checkbox) {
    const formData = new FormData(document.getElementById('weekPlanForm'));
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value === 'on';
    });

    fetch('/weekplan/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    }).then(response => {
        if (!response.ok) {
            // Revert checkbox if update failed
            checkbox.checked = !checkbox.checked;
            const dayBox = checkbox.closest('.day-box');
            if (dayBox) {
                dayBox.classList.toggle('selected');
            }
        }
    });
}

document.getElementById('lockWeek').addEventListener('click', function() {
    if (!confirm('Are you sure you want to lock your week plan? This cannot be undone!')) {
        return;
    }

    fetch('/weekplan/lock', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(response => {
        if (response.ok) {
            // Disable all checkboxes and lock button
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.disabled = true;
                cb.closest('.day-box').classList.add('locked');
            });
            this.disabled = true;
            
            // Show locked message
            alert('Week plan has been locked successfully!');
        }
    });
});

document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const dayBox = this.closest('.day-box');
        if (this.checked) {
            dayBox.classList.add('selected');
        } else {
            dayBox.classList.remove('selected');
        }
    });
});
</script>
@endsection
