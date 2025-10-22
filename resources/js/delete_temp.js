document.addEventListener('DOMContentLoaded', function () {
    const backBtn = document.getElementById('back-home');
    const deleteUrl = window.deleteTempUserUrl || null;

    if (!deleteUrl) {
        console.error("deleteTempUserUrl is not set!");
        return;
    }

    backBtn.addEventListener('click', function (e) {
        e.preventDefault();
        deleteTempUser().then(() => {
            window.location.href = backBtn.href || '/';
        });
    });

    async function deleteTempUser() {
        return fetch(deleteUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": window.csrfToken,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({})
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const resendBtn = document.getElementById('resend-btn');
    const resendMessage = document.getElementById('resend-message');

    if (!resendBtn) return; // safety check

    resendBtn.addEventListener('click', function() {
        resendBtn.disabled = true;
        resendBtn.textContent = 'Sending...';
        resendMessage.textContent = '';

        fetch("/resend-verification", { // route URL
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                "Accept": "application/json",
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resendMessage.textContent = data.success;
                resendMessage.style.color = '#32e875';

                let countdown = 60;
                const interval = setInterval(() => {
                    resendBtn.textContent = `Resend in ${countdown}s`;
                    countdown--;
                    if (countdown < 0) {
                        clearInterval(interval);
                        resendBtn.textContent = 'Resend Verification Code';
                        resendBtn.disabled = false;
                    }
                }, 1000);

            } else if (data.error) {
                resendMessage.textContent = data.error;
                resendMessage.style.color = 'red';
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend Verification Code';
            }
        })
        .catch(() => {
            resendMessage.textContent = 'Something went wrong.';
            resendMessage.style.color = 'red';
            resendBtn.disabled = false;
            resendBtn.textContent = 'Resend Verification Code';
        });
    });
});

