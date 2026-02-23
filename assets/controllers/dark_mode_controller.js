import { Controller } from '@hotwired/stimulus';

const STORAGE_KEY = 'theme';

export default class extends Controller {
    connect() {
        const storedTheme = localStorage.getItem(STORAGE_KEY);
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const isDark = storedTheme ? storedTheme === 'dark' : prefersDark;

        this.applyTheme(isDark);
        this.initButton(isDark);

    }

    toggle() {
        const isDark = !document.documentElement.classList.contains('dark');

        this.applyTheme(isDark);
        localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
    }

    applyTheme(isDark) {
        document.documentElement.classList.toggle('dark', isDark);
        document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
    }

    initButton(isDark) {
        const inputElt = this.element.querySelector('input');

        if (inputElt instanceof HTMLInputElement) {
            inputElt.checked = isDark;
        }
    }
}
