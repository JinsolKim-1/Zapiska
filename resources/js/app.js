// === Toggle password visibility ===
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

// === Password strength, checklist, and validation ===
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('password_confirmation');
const strengthBar = document.querySelector('.strength-bar');
const strengthText = document.querySelector('.strength-text');

// Checklist elements (make sure these exist in your Blade)
const checklist = {
    length: document.getElementById('length'),
    upper: document.getElementById('upper'),
    lower: document.getElementById('lower'),
    number: document.getElementById('number'),
    special: document.getElementById('special')
};

// Helper function to check password rules
function validatePassword(password) {
    const length = password.length >= 8;
    const upper = /[A-Z]/.test(password);
    const lower = /[a-z]/.test(password);
    const number = /[0-9]/.test(password);
    const special = /[@$!%*#?&^]/.test(password);
    return { length, upper, lower, number, special };
}

if (passwordInput) {
    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        const rules = validatePassword(val);

        // === Password strength bar ===
        let strength = Object.values(rules).filter(Boolean).length;
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

        // === Update checklist visually ===
        for (let key in rules) {
            if (checklist[key]) {
                if (rules[key]) {
                    checklist[key].classList.add('valid');
                    checklist[key].classList.remove('invalid');
                } else {
                    checklist[key].classList.remove('valid');
                    checklist[key].classList.add('invalid');
                }
            }
        }

        // === Check password match dynamically ===
        if (confirmInput && confirmInput.value.length > 0) {
            checkPasswordMatch();
        }
    });
}

// === Live password match check ===
if (confirmInput) {
    confirmInput.addEventListener('input', checkPasswordMatch);
}

function checkPasswordMatch() {
    const matchMessageId = 'match-message';
    let message = document.getElementById(matchMessageId);

    if (!message) {
        message = document.createElement('small');
        message.id = matchMessageId;
        confirmInput.parentElement.parentElement.appendChild(message);
    }

    if (confirmInput.value === '') {
        message.textContent = '';
        return;
    }

    if (confirmInput.value === passwordInput.value) {
        message.textContent = '✅ Passwords match';
        message.style.color = '#4CAF50';
    } else {
        message.textContent = '❌ Passwords do not match';
        message.style.color = 'red';
    }
}

