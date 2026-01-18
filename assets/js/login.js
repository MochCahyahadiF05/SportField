// Toggle Password Visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

// Form Submission Handler
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('loginPassword');
            
            if (!emailInput || !passwordInput) {
                console.error('Form elements not found!', { emailInput, passwordInput });
                alert('Error: Form elements not found');
                return;
            }

            const email = emailInput.value;
            const password = passwordInput.value;

            const formData = new FormData();
            formData.append('action', 'login');
            formData.append('email', email);
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
                    setTimeout(() => {
                        // Redirect berdasarkan role
                        if (data.role === 'admin') {
                            window.location.href = '../../page/admin/dashboard.php';
                        } else {
                            window.location.href = '../../index.php';
                        }
                    }, 1500);
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