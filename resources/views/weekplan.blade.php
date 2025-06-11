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
    max-width: 800px;
    margin: 0 auto;
}

.days-container {
    flex: 1;
    background: var(--surface);
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.days-column {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.day-box {
    background: var(--surface-light);
    padding: 1.5rem;
    border-radius: 0.75rem;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.day-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    background: var(--surface);
    border-color: rgba(255, 255, 255, 0.2);
}

.day-box.selected {
    border-color: var(--primary);
    background: rgba(99, 102, 241, 0.1);
}

.day-box.locked {
    opacity: 0.8;
    cursor: not-allowed;
}

.day-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: var(--primary);
}

.form-check-input {
    width: 25px;
    height: 25px;
    margin-right: 10px;
    cursor: pointer;
    border: 2px solid var(--primary);
    border-radius: 6px;
    background: var(--surface-light);
}

.form-check-input:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.form-check-label {
    font-size: 1.1rem;
    padding-top: 3px;
    cursor: pointer;
    color: var(--text);
}

.form-check {
    display: flex;
    align-items: center;
    justify-content: center;
}

.lock-button {
    margin-top: 2rem;
    align-self: center;
    min-width: 200px;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.lock-button:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
}

.lock-button:disabled {
    background: rgba(99, 102, 241, 0.5);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
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
