// ======================= LOGIN FORM =======================
const loginForm = document.querySelector('.login-form');
if (loginForm) {
  loginForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const email = loginForm.querySelector('input[name="email"]').value.trim();
    const password = loginForm.querySelector('input[name="password"]').value;

    if (!email || !password) {
      alert('Please enter both email and password.');
      return;
    }

    const data = new FormData();
    data.append('email', email);
    data.append('password', password);

    fetch('login_server.php', {
      method: 'POST',
      body: data
    })
    .then(response => response.text())
    .then(responseText => {
      if (responseText.startsWith('redirect:')) {
        const target = responseText.replace('redirect:', '');
        window.location.href = target;
      } else if (responseText.startsWith('error:')) {
        const errorMsg = responseText.replace('error:', '');
        alert(errorMsg);
      } else {
        alert('Unexpected response: ' + responseText);
      }
    })
    .catch(error => {
      console.error('Login Error:', error);
      alert('Something went wrong. Please try again.');
    });
  });
}

// ======================= SIGNUP FORM =======================
const signupForm = document.querySelector('.signup-form');
if (signupForm) {
  signupForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const name = signupForm.querySelector('input[name="name"]').value.trim();
    const email = signupForm.querySelector('input[name="email"]').value.trim();
    const password = signupForm.querySelector('input[name="password"]').value;
    const role = signupForm.querySelector('select[name="role"]').value;

    if (!name || !email || !password || role === 'invalid') {
      alert('Please fill out all fields and select a valid role.');
      return;
    }

    const data = new FormData();
    data.append('name', name);
    data.append('email', email);
    data.append('password', password);
    data.append('role', role);

    fetch('signup_server.php', {
      method: 'POST',
      body: data
    })
      .then(response => response.text())
      .then(responseText => {
        if (responseText.startsWith('redirect:')) {
          window.location.href = responseText.replace('redirect:', '');
        } else {
          alert(responseText);
        }
      })
      .catch(error => console.error('Signup Error:', error));
  });
}

// ======================= TOGGLE FORMS =======================
const formContainer = document.getElementById('form-container');
const showSignupBtn = document.getElementById('show-signup');
const showLoginBtn = document.getElementById('show-login');

if (showSignupBtn && showLoginBtn && formContainer) {
  showSignupBtn.addEventListener('click', () => {
    loginForm.reset();
    formContainer.style.transform = 'translateX(-50%)';
  });

  showLoginBtn.addEventListener('click', () => {
    signupForm.reset();
    formContainer.style.transform = 'translateX(0)';
  });
}

// ======================= ANIMATION ON LOAD =======================
window.addEventListener('DOMContentLoaded', () => {
  anime({
    targets: '#login-wrapper',
    opacity: [0, 1],
    translateX: ['-100px', '0px'],
    duration: 1000,
    easing: 'easeOutExpo'
  });
});
