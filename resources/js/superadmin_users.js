
document.addEventListener("DOMContentLoaded", () => {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // Modals
    const addModal = document.getElementById("superadminModal");
    const viewModal = document.getElementById("viewUserModal");
    const confirmModal = document.getElementById("confirmModal");
    const openAddBtn = document.getElementById("openModal");
    const closeAddBtn = document.getElementById("closeModal");
    const closeViewBtn = document.getElementById("closeViewModal");
    const closeConfirmBtn = document.getElementById("closeConfirmModal");
    const confirmBtn = document.getElementById("confirmBtn");
    const confirmMessage = document.getElementById("confirmMessage");

    const addErrors = document.getElementById("addErrors");
    const editErrors = document.getElementById("editErrors");

    let confirmAction = null; // function to run after password is confirmed

    // Notification helper
    function showNotification(message, type = "success") {
        let notif = document.createElement("div");
        notif.className = `notification ${type}`;
        notif.textContent = message;
        document.body.appendChild(notif);
        setTimeout(() => notif.remove(), 3000);
    }

    // Open/Close Modals
    function openModal(modal) {
        modal.style.display = "flex";
        setTimeout(() => modal.classList.add("visible"), 10);
    }
    function closeModal(modal) {
        modal.classList.remove("visible");
        setTimeout(() => modal.style.display = "none", 300);
    }

    openAddBtn.addEventListener("click", () => openModal(addModal));
    closeAddBtn.addEventListener("click", () => closeModal(addModal));
    closeViewBtn.addEventListener("click", () => closeModal(viewModal));
    closeConfirmBtn.addEventListener("click", () => closeModal(confirmModal));

    function openConfirmModal(message, action) {
        confirmMessage.textContent = message;
        document.getElementById("confirmPassword").value = "";
        confirmAction = action;
        openModal(confirmModal);
    }

    confirmBtn.addEventListener("click", async () => {
        const password = document.getElementById("confirmPassword").value;
        if (!password) {
            showNotification("Please enter your password to confirm.", "error");
            return;
        }
        if (confirmAction) await confirmAction(password);
        closeModal(confirmModal);
    });

    // Add SuperAdmin
    document.getElementById("addSuperadminForm").addEventListener("submit", async (e) => {
        e.preventDefault();
        addErrors.innerHTML = "";
        const formData = new FormData(e.target);
        try {
            const res = await fetch("/superadmin/users/store", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrf },
                body: formData
            });
            const data = await res.json();
            if (!res.ok) {
                if (data.errors) addErrors.innerHTML = Object.values(data.errors).flat().join("<br>");
                else addErrors.textContent = data.message || "Failed to create.";
                return;
            }
            showNotification("SuperAdmin added!");
            closeModal(addModal);
            location.reload();
        } catch (err) {
            addErrors.textContent = "Unexpected error: " + err.message;
        }
    });

    // View/Edit User
    const editForm = document.getElementById("editUserForm");
    const deleteBtn = document.getElementById("deleteUserBtn");

    document.querySelectorAll(".user-row").forEach((row) => {
        row.addEventListener("click", (e) => {
            if (e.target.closest(".edit-btn") || e.target.closest(".delete-btn")) return;

            // Fill modal data
            document.getElementById("viewProfile").src = row.dataset.profile;
            document.getElementById("viewUsername").textContent = row.dataset.username;
            document.getElementById("viewEmail").textContent = row.dataset.email;
            document.getElementById("viewFirstName").textContent = row.dataset.firstName || "-";
            document.getElementById("viewLastName").textContent = row.dataset.lastName || "-";
            document.getElementById("viewContact").textContent = row.dataset.contact || "-";
            document.getElementById("viewStatus").textContent = row.dataset.status;
            document.getElementById("viewCreated").textContent = row.dataset.created;

            // Fill edit form
            document.getElementById("editUserId").value = row.dataset.id;
            document.getElementById("editUsername").value = row.dataset.username;
            document.getElementById("editEmail").value = row.dataset.email;
            document.getElementById("editFirstName").value = row.dataset.firstName || "";
            document.getElementById("editLastName").value = row.dataset.lastName || "";
            document.getElementById("editContact").value = row.dataset.contact || "";
            document.getElementById("editStatus").value = row.dataset.status.toLowerCase();

            editErrors.innerHTML = "";
            openModal(viewModal);
        });
    });

    // Edit User with password confirmation
    editForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        editErrors.innerHTML = "";

        const id = document.getElementById("editUserId").value;
        const currentId = document.querySelector('meta[name="current-superadmin-id"]').content;
        const payload = {
            super_username: document.getElementById("editUsername").value,
            super_email: document.getElementById("editEmail").value,
            first_name: document.getElementById("editFirstName").value,
            last_name: document.getElementById("editLastName").value,
            contact: document.getElementById("editContact").value,
            status: document.getElementById("editStatus").value
        };

        try {
            const endpoint = (id === currentId)
            ? `/superadmin/users/update/${id}`
            : `/superadmin/request-edit/${id}`;

            const res = await fetch(endpoint, {
                method: "POST",
                headers: { 
                    "X-CSRF-TOKEN": csrf, 
                    "Content-Type": "application/json" 
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (!res.ok) {
                editErrors.innerHTML = data.message || "Failed to send edit request.";
                return;
            }
            closeModal(viewModal);

        } catch (err) {
            editErrors.textContent = "Unexpected error: " + err.message;
        }
    });

    // Delete User with password confirmation
    deleteBtn.addEventListener("click", () => {
        const id = document.getElementById("editUserId").value;
        openConfirmModal("Enter your password to confirm deleting this user.", async (password) => {
            try {
                const res = await fetch(`/superadmin/users/${id}`, {
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": csrf, "Content-Type": "application/json" },
                    body: JSON.stringify({ password_confirmation: password })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || "Failed to delete.");

                const row = document.querySelector(`.user-row[data-id="${id}"]`);
                row.remove();
                showNotification("User deleted!");
                closeModal(viewModal);
            } catch (err) {
                showNotification("Error: " + err.message, "error");
            }
        });
    });
});
