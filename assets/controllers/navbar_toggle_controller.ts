import {Controller} from '@hotwired/stimulus';

export default class extends Controller<HTMLElement> {
    static targets = ['menu', 'menuIcon', 'closeIcon', 'toggleButton'];

    declare menuTarget: HTMLElement;
    declare menuIconTarget: HTMLElement;
    declare closeIconTarget: HTMLElement;
    declare toggleButtonTarget: HTMLButtonElement;

    connect() {
        this.updateMenuState();
    }

    toggle() {
        this.menuTarget.classList.toggle('app-header-menu--open');
        this.updateMenuState();
    }

    private updateMenuState() {
        const isOpen = this.isMenuOpen();

        this.updateIconDisplay(isOpen);
        this.toggleButtonTarget.setAttribute('aria-expanded', String(isOpen));

        if (isOpen) {
            this.menuTarget.removeAttribute('aria-hidden');
            this.menuTarget.removeAttribute('inert');
        } else {
            this.menuTarget.setAttribute('aria-hidden', 'true');
            this.menuTarget.setAttribute('inert', '');
        }
    }

    private updateIconDisplay(isOpen: boolean) {
        this.menuIconTarget.classList.toggle('hidden', isOpen);
        this.closeIconTarget.classList.toggle('hidden', !isOpen);
    }

    private isMenuOpen(): boolean {
        return this.menuTarget.classList.contains('app-header-menu--open');
    }
}
