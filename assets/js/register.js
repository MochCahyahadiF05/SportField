// Toggle Password Visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Form Submission Handler
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                document.getElementById('messageAlert').innerHTML = `<div style="background: #fee2e2; color: #dc2626; padding: 0.75rem 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-size: 0.9rem;">Password tidak cocok!</div>`;
                return;
            }

            const formData = new FormData();
            formData.append('action', 'register');
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('password', password);

            try {
                const response = await fetch('process_auth.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                const messageAlert = document.getElementById('messageAlert');

                if (data.success) {
                    messageAlert.innerHTML = `<div style="background: #dcfce7; color: #15803d; padding: 0.75rem 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-size: 0.9rem;">${data.message}</div>`;
                    document.getElementById('registerForm').reset();
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    messageAlert.innerHTML = `<div style="background: #fee2e2; color: #dc2626; padding: 0.75rem 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-size: 0.9rem;">${data.message}</div>`;
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('messageAlert').innerHTML = `<div style="background: #fee2e2; color: #dc2626; padding: 0.75rem 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-size: 0.9rem;">Terjadi kesalahan. Silakan coba lagi.</div>`;
            }
        });
    }
});
