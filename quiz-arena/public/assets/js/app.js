async function postJSON(url, data){
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data) });
  return res.json();
}

async function postForm(url, form){
  const formData = new FormData(form);
  const res = await fetch(url, { method:'POST', body: formData });
  return res.json();
}

// Enhance auth forms with AJAX for smoother UX
window.addEventListener('DOMContentLoaded', () => {
  const registerForm = document.getElementById('registerForm');
  const loginForm = document.getElementById('loginForm');
  if (registerForm){
    registerForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const out = await postForm('../api/register.php', registerForm);
      if(out.ok){ window.location.href = 'dashboard.php'; }
      else { alert(out.error || 'Registration failed'); }
    })
  }
  if (loginForm){
    loginForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const out = await postForm('../api/login.php', loginForm);
      if(out.ok){ window.location.href = 'dashboard.php'; }
      else { alert(out.error || 'Login failed'); }
    })
  }
});
