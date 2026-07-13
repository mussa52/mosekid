document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#login-form');
    const emailInput = document.querySelector('#email');
    const passwordInput = document.querySelector('#password');
    const toggleButton = document.querySelector('#toggle-password');
    const errorMessage = document.querySelector('#login-error');

    if (toggleButton) {
        const icon = toggleButton.querySelector('i');
        toggleButton.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            if (icon) {
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            }
            toggleButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();
            let errors = [];

            if (!email) {
                errors.push('Email address is required.');
            } else if (!/^[\w.%+-]+@[\w.-]+\.[A-Za-z]{2,}$/.test(email)) {
                errors.push('Enter a valid email address.');
            }

            if (!password) {
                errors.push('Password is required.');
            } else if (password.length < 8) {
                errors.push('Password must be at least 8 characters.');
            }

            if (errors.length) {
                event.preventDefault();
                if (errorMessage) {
                    errorMessage.innerHTML = errors.map(function (message) {
                        return '<div>' + message + '</div>';
                    }).join('');
                    errorMessage.classList.remove('d-none');
                }
            }
        });
    }
});
