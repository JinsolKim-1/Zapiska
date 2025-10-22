document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = btn.parentElement.querySelector('input');
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = "<i class='bx bx-hide'></i>";
        } else {
            input.type = 'password';
            btn.innerHTML = "<i class='bx bx-show'></i>";
        }
    });
});

const passwordInput = document.getElementById('password');
const strengthBar = document.querySelector('.strength-bar');
const strengthText = document.querySelector('.strength-text');

if (passwordInput) {
    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        let strength = 0;

        if (val.length >= 8) strength += 1;
        if (/[A-Z]/.test(val)) strength += 1;
        if (/[a-z]/.test(val)) strength += 1;
        if (/[0-9]/.test(val)) strength += 1;
        if (/[@$!%*#?&]/.test(val)) strength += 1;

        const percent = (strength / 5) * 100;
        strengthBar.style.width = percent + '%';

        if (strength <= 2) {
            strengthBar.style.background = 'red';
            strengthText.textContent = 'Weak';
        } else if (strength === 3 || strength === 4) {
            strengthBar.style.background = 'orange';
            strengthText.textContent = 'Medium';
        } else if (strength === 5) {
            strengthBar.style.background = '#4CAF88';
            strengthText.textContent = 'Strong';
        }
    });
}
