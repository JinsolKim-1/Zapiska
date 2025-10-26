<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Companies | Zapiska</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/companies.css'])
</head>
<body>
    @include('superadmin.includes.sidebar')
    
    <div class="main-content">
        <div class="top-header">
            <h1>Pending Company Verifications</h1>
            <span id="datetime"></span>
        </div>

        {{-- Success message --}}
        @if(session('success'))
            <div class="success-message">
                <i class='bx bx-check'></i> {{ session('success') }}
            </div>
        @endif

        {{-- No companies --}}
        @if($companies->isEmpty())
            <p style="color:#555; margin-top:20px;">No companies pending verification.</p>
        @else
            <div class="verification-panel">
                <table class="companies-table">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Website</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <td>{{ $company->company_name }}</td>
                                <td>{{ $company->company_email }}</td>
                                <td>{{ $company->company_number ?? '-' }}</td>
                                <td>
                                    @if($company->company_website)
                                        <a href="{{ $company->company_website }}" target="_blank" rel="noopener noreferrer">{{ $company->company_website }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="status-label {{ $company->verification_status }}">
                                        {{ ucfirst($company->verification_status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($company->verification_status === 'pending')
                                        <form method="POST" action="{{ route('superadmin.companies.approve', $company->company_id) }}" class="approve-form" style="display:inline-block;">
                                            @csrf
                                            <button type="button" class="approve-btn" data-action="approve">
                                                <i class='bx bx-check'></i> Approve
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('superadmin.companies.reject', $company->company_id) }}" class="reject-form" style="display:inline-block;">
                                            @csrf
                                            <button type="button" class="reject-btn" data-action="reject">
                                                <i class='bx bx-x'></i> Reject
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Custom Confirmation Modal -->
    <div id="confirmModal" class="confirm-modal">
        <div class="confirm-content">
            <h3 id="confirmTitle">Confirm Action</h3>
            <p id="confirmMessage">Are you sure?</p>
            <div class="confirm-actions">
                <button id="confirmYes" class="confirm-yes">Yes</button>
                <button id="confirmNo" class="confirm-no">Cancel</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const modal = document.getElementById("confirmModal");
        const confirmYes = document.getElementById("confirmYes");
        const confirmNo = document.getElementById("confirmNo");
        const confirmTitle = document.getElementById("confirmTitle");
        const confirmMessage = document.getElementById("confirmMessage");
        let targetForm = null;

        // Handle Approve/Reject clicks
        document.querySelectorAll(".approve-btn, .reject-btn").forEach(button => {
            button.addEventListener("click", () => {
                const action = button.dataset.action;
                targetForm = button.closest("form");

                if (action === "approve") {
                    confirmTitle.textContent = "Approve Company?";
                    confirmMessage.textContent = "This company will be verified and activated.";
                    confirmYes.style.background = "var(--accent-green)";
                } else {
                    confirmTitle.textContent = "Reject Company?";
                    confirmMessage.textContent = "This will permanently remove the company from the system.";
                    confirmYes.style.background = "var(--accent-red)";
                }

                modal.classList.add("active");
            });
        });

        // Confirm
        confirmYes.addEventListener("click", () => {
            if (targetForm) targetForm.submit();
            modal.classList.remove("active");
        });

        // Cancel
        confirmNo.addEventListener("click", () => {
            modal.classList.remove("active");
        });
    });
    </script>
</body>
</html>
