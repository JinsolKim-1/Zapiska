<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Manager | {{ $sector->department_name }} | Zapiska</title>
    @vite(['resources/css/sectorUsers.css'])
</head>
<body>
    @include('users.includes.mainsidebar')

    <main class="main-content">
        <header class="dashboard-header">
            <h1>Edit Manager for "{{ $sector->department_name }}"</h1>
        </header>

        <section class="form-section">
            <form action="{{ route('users.editManager', $sector->sector_id) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="manager_id">Select Manager</label>
                    <select name="manager_id" id="manager_id">
                        <option value="">-- No Manager --</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->user_id }}" 
                                {{ $sector->manager_id == $manager->user_id ? 'selected' : '' }}>
                                {{ $manager->username }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="submit-btn">Update Manager</button>
            </form>
        </section>
    </main>
</body>
</html>
