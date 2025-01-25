// Toggle Password Visibility
document.getElementById('toggle-password').addEventListener('change', function () {
    const passwordField = document.getElementById('new_password');
    passwordField.type = this.checked ? 'text' : 'password';
});