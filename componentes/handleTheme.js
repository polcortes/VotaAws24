addEventListener('load', () => {
    let theme = localStorage.getItem('theme');
    if (theme && (theme === 'dark' || theme === 'light')) {
        document.documentElement.dataset.theme = theme;
    } else {
        theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        document.documentElement.dataset.theme = theme;
    }

    const toggleTheme = document.getElementById('toggle-theme');

    switch (theme) {
        case 'dark':
            toggleTheme.textContent = '🌞';
            break;
        case 'light':
            toggleTheme.textContent = '🌙';
            break;
    }

    toggleTheme.addEventListener('click', () => {
        switch (theme) {
            case 'dark':
                localStorage.setItem('theme', 'light');
                document.documentElement.dataset.theme = 'light';
                theme = 'light';
                toggleTheme.textContent = '🌙';
                break;
            case 'light':
                localStorage.setItem('theme', 'dark');
                document.documentElement.dataset.theme = 'dark';
                theme = 'dark';
                toggleTheme.textContent = '🌞';
                break;
        }

        const themeChangeEv = new Event('themechange');
        document.dispatchEvent(themeChangeEv);
    })
});