<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="current-superadmin-id" content="{{ Auth::guard('superadmin')->user()->super_id }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control panel | Manage Users</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/super_users.css', 'resources/js/superadmin_users.js'])
</head>
<body>
    @include('superadmin.includes.sidebar')

    <div class="main-content">
        {{-- Header --}}
        <header class="top-header">
            <div>
                <h1>Management</h1>
                <p>Manage and authorize other Associates</p>
            </div>
            <button class="add-btn" id="openModal">
                <i class="bx bx-plus-circle"></i> Add New Associates
            </button>
        </header>

        {{-- Summary Cards --}}
        <section class="summary-cards">
            <div class="card">
                <i class="bx bx-user-circle"></i>
                <div class="card-info">
                    <h3>Total Associates</h3>
                    <p>{{ $superadmins->count() }}</p>
                </div>
            </div>
            <div class="card">
                <i class="bx bx-shield-quarter"></i>
                <div class="card-info">
                    <h3>Active Accounts</h3>
                    <p>{{ $superadmins->where('status','active')->count() }}</p>
                </div>
            </div>
        </section>

        {{-- Users Table --}}
        <section class="users-table">
            <h2>All Associates</h2>
            <table>
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Date Created</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="superadminTableBody">
                    @forelse ($superadmins as $sa)
                        <tr 
                            class="user-row"
                            data-id="{{ $sa->super_id }}"
                            data-username="{{ $sa->super_username }}"
                            data-email="{{ $sa->super_email }}"
                            data-first-name="{{ $sa->first_name ?? '' }}"
                            data-last-name="{{ $sa->last_name ?? '' }}"
                            data-contact="{{ $sa->contact ?? '' }}"
                            data-status="{{ ucfirst($sa->status) }}"
                            data-created="{{ \Carbon\Carbon::parse($sa->su_created_at)->format('Y-m-d') }}"
                            data-profile="{{ $sa->profile ? asset('storage/' . $sa->profile) : asset('images/cat.png') }}"
                        >
                            <td>
                                <img src="{{ $sa->profile ? asset('storage/' . $sa->profile) : asset('images/cat.png') }}" class="user-avatar">
                            </td>
                            <td>{{ $sa->super_username }}</td>
                            <td>{{ $sa->super_email }}</td>
                            <td>{{ \Carbon\Carbon::parse($sa->su_created_at)->format('Y-m-d') }}</td>
                            <td>
                                <span class="status {{ strtolower($sa->status) }}">
                                    {{ ucfirst($sa->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;">No Associates found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>

    {{-- Add SuperAdmin Modal --}}
    <div class="modal" id="superadminModal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2><i class="bx bx-user-plus"></i> Add New Associate</h2>
            <div id="addErrors" style="color:red; margin-bottom:10px;"></div>
            <form id="addSuperadminForm" enctype="multipart/form-data">
                <div class="form-grid">
                    <input type="text" name="super_username" id="superadminUsername" placeholder="Username" required>
                    <input type="email" name="super_email" id="superadminEmail" placeholder="Email" required>
                    <input type="password" name="super_password" id="superadminPassword" placeholder="Password" required>
                    <input type="password" name="super_password_confirmation" id="superadminPasswordConfirm" placeholder="Confirm Password" required>
                    <input type="text" name="first_name" id="firstName" placeholder="First Name">
                    <input type="text" name="last_name" id="lastName" placeholder="Last Name">
                    <input type="text" name="contact" id="contact" placeholder="Contact">
                    <input type="file" name="profile" id="profile" accept="image/*">
                </div>
                <button class="create" type="submit">Create Associate</button>
            </form>
        </div>
    </div>

    {{-- View / Edit User Modal --}}
    <div class="modal" id="viewUserModal">
        <div class="modal-content">
            <span class="close" id="closeViewModal">&times;</span>
            <h2>User Information</h2>
        <div class="user-info-header">
            <img id="viewProfile" src="{{ asset('images/cat.png') }}" class="user-avatar-large">
        </div>

        <div class="user-info-grid">
            <div class="info-item">
                <strong>Username:</strong>
                <span id="viewUsername"></span>
            </div>
            <div class="info-item">
                <strong>Email:</strong>
                <span id="viewEmail"></span>
            </div>
            <div class="info-item">
                <strong>First Name:</strong>
                <span id="viewFirstName"></span>
            </div>
            <div class="info-item">
                <strong>Last Name:</strong>
                <span id="viewLastName"></span>
            </div>
            <div class="info-item">
                <strong>Contact:</strong>
                <span id="viewContact"></span>
            </div>
            <div class="info-item">
                <strong>Status:</strong>
                <span id="viewStatus"></span>
            </div>
            <div class="info-item full-width">
                <strong>Date Created:</strong>
                <span id="viewCreated"></span>
            </div>
        </div>

            <div id="editErrors" style="color:red; margin-bottom:10px;"></div>
            <div class="modal-content large">
                <h2>Edit User Information</h2>
                <form id="editUserForm">
                    <input type="hidden" id="editUserId">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editUsername">Username</label>
                            <input type="text" id="editUsername" name="super_username" required>
                        </div>

                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" id="editEmail" name="super_email" required>
                        </div>

                        <div class="form-group">
                            <label for="editFirstName">First Name</label>
                            <input type="text" id="editFirstName" name="first_name">
                        </div>

                        <div class="form-group">
                            <label for="editLastName">Last Name</label>
                            <input type="text" id="editLastName" name="last_name">
                        </div>

                        <div class="form-group">
                            <label for="editContact">Contact</label>
                            <input type="text" id="editContact" name="contact">
                        </div>

                        <div class="form-group">
                            <label for="editStatus">Status</label>
                            <select id="editStatus" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="editPasswordConfirm">Confirm Your Password</label>
                            <input type="password" id="editPasswordConfirm" placeholder="Enter your password to confirm">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="add-btn">Save Changes</button>
                        <button type="button" id="deleteUserBtn" class="delete-btn">Delete User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <span class="close" id="closeConfirmModal">&times;</span>
            <h2>Confirm Your Password</h2>
            <p id="confirmMessage">Enter your password to confirm this change.</p>
            <input type="password" id="confirmPassword" placeholder="Your password">
            <button id="confirmBtn" class="done-btn">Confirm</button>
        </div>
    </div>

</body>
</html>
