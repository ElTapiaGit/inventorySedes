document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('input');

    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            const label = input.previousElementSibling;
            console.log('Input focused:', input.id);
            if (label) {
                label.style.top = '-1.2em';
                label.style.left = '0';
                label.style.fontSize = '1.2em';
                label.style.fontWeight = '1.2em';
            }
        });
        
        //posicion dentro los input
        input.addEventListener('blur', function () {
            const label = input.previousElementSibling;
            console.log('Input blurred:', input.id);
            if (label && input.value === '') {
                label.style.top = '15px';
                label.style.left = '10px';
                label.style.fontSize = '20px';
            }
        });

        // Handle pre-filled inputs on page load
        if (input.value !== '') {
            const label = input.previousElementSibling;
            console.log('Input pre-filled:', input.id);
            if (label) {
                label.style.top = '0';
                label.style.left = '0';
                label.style.fontSize = '20px';
            }
        }
    });
});

function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}