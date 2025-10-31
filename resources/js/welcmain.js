document.addEventListener("DOMContentLoaded", () => {
    const messageBox = document.getElementById('message-box');
    const joinModal = document.getElementById('joinModal');

    function displayMessage(message, type = 'info') {
        messageBox.textContent = message;
        messageBox.className = 'mt-8 p-3 rounded-xl shadow-lg';
        let bgColor = 'bg-gray-700/50';
        if (type === 'success') bgColor = 'bg-green-600/70';
        if (type === 'error') bgColor = 'bg-red-600/70';
        messageBox.classList.add(bgColor, 'text-sm', 'text-white', 'block');
        setTimeout(() => {
            messageBox.classList.add('hidden');
        }, 3000);
    }

    window.openJoinModal = () => joinModal.classList.remove('hidden');
    window.closeJoinModal = () => joinModal.classList.add('hidden');

    const joinForm = document.getElementById('joinCompanyForm');
    if (joinForm) {
        joinForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const code = joinForm.invite_code.value.trim();
            if (!code) {
                displayMessage('Please enter a valid invite code.', 'error');
                return;
            }

            try {
                const token = document.querySelector('input[name="_token"]').value;

                const response = await fetch(joinForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ invite_code: code }),
                });

                const data = await response.json();

                if (response.ok) {
                    displayMessage(data.message || 'Joined company successfully!', 'success');

                    setTimeout(() => {
                        closeJoinModal(); // hide modal
                        window.location.href = data.redirect || '/users/dashboard';
                    }, 1000);
                } else {
                    displayMessage(data.message || 'Failed to join company.', 'error');
                }
            } catch (err) {
                console.error(err);
                displayMessage('Something went wrong. Try again.', 'error');
            }
        });
    }
});
