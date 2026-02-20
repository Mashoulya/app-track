<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Test Register</title>
    <style>
        body{font-family:system-ui,Segoe UI,Roboto,Arial;margin:40px}
        form{max-width:420px;margin:auto;display:grid;gap:8px}
        input{padding:8px;font-size:16px}
        button{padding:10px;font-size:16px}
        .error{color:#b00020}
        .success{color:green}
    </style>
</head>
<body>
    <h1>Inscription (test)</h1>
    <form id="regForm">
        <input name="first_name" placeholder="Prénom" required />
        <input name="last_name" placeholder="Nom" required />
        <input name="email" type="email" placeholder="Email" required />
        <input name="password" type="password" placeholder="Mot de passe" required />
        <input name="password_confirmation" type="password" placeholder="Confirmer le mot de passe" required />
        <button type="submit">S'inscrire</button>
    </form>
    <div id="output"></div>

    <script>
    const form = document.getElementById('regForm');
    const out = document.getElementById('output');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        out.textContent = '';

        const data = Object.fromEntries(new FormData(form).entries());

        try {
            const res = await fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const json = await res.json();

            if (!res.ok) {
                const msgs = json.errors ? Object.values(json.errors).flat().join('\n') : (json.message || JSON.stringify(json));
                out.innerHTML = '<pre class="error">' + msgs + '</pre>';
                return;
            }

            out.innerHTML = '<div class="success">Inscription réussie — token: ' + (json.token?.slice(0,40) || '') + '...</div>';
            console.log('response', json);
        } catch (err) {
            out.innerHTML = '<pre class="error">' + err.message + '</pre>';
        }
    });
    </script>
</body>
</html>
