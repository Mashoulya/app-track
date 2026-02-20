const form = document.getElementById('regForm');
const out = document.getElementById('output');

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  out.textContent = '';

  const formData = Object.fromEntries(new FormData(form).entries());

  try {
    // 1) Request CSRF cookie from Sanctum
    await fetch('/sanctum/csrf-cookie', { credentials: 'include' });

    // helper to read cookie
    const getCookie = (name) => {
      const match = document.cookie.match(new RegExp('(^|; )' + name + '=([^;]+)'));
      return match ? decodeURIComponent(match[2]) : null;
    };
    const xsrf = getCookie('XSRF-TOKEN');

    const res = await fetch('/api/register', {
      method: 'POST',
      credentials: 'include',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': xsrf || '' },
      body: JSON.stringify({
        first_name: formData.first_name,
        last_name: formData.last_name,
        email: formData.email,
        password: formData.password,
        password_confirmation: formData.password_confirmation
      })
    });

    const data = await res.json();

    if (!res.ok) {
      out.textContent = 'Erreur :\n' + JSON.stringify(data, null, 2);
    } else {
      out.textContent = 'Inscription réussie !\n' + JSON.stringify(data, null, 2);
    }

  } catch (err) {
    console.error(err);
    out.textContent = 'Erreur réseau ou serveur.';
  }
});