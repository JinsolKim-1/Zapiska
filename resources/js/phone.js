const phoneInput = document.getElementById('company_number');

phoneInput.addEventListener('input', () => {
    phoneInput.value = phoneInput.value.replace(/[^\d+()\s-]/g, '');
});
