document.addEventListener("DOMContentLoaded", () => {
    const messageBox = document.getElementById('message-box');

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

    window.createCompany = () => {
        displayMessage('Redirecting to Company Registration... (not implemented)', 'info');
    };

    window.joinCompany = () => {
        displayMessage('Redirecting to Join Company... (not implemented)', 'info');
    };
});
