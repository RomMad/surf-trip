import {Controller} from '@hotwired/stimulus';

export default class extends Controller<HTMLElement> {
    static targets = ['trigger', 'dialog'];

    static values = {
        open: Boolean,
    };

    declare readonly triggerTarget: HTMLElement;
    declare readonly hasTriggerTarget: boolean;
    declare readonly dialogTarget: HTMLDialogElement;
    declare readonly openValue: boolean;

    connect(): void {
        if (this.openValue) {
            this.open();
        }
    }

    open(): void {
        this.dialogTarget.showModal();

        this.updateTrigger(true);
    }

    close(): void {
        this.dialogTarget.close();

        this.updateTrigger(false);
    }

    closeOnSubmitEnd(event: CustomEvent<{success: boolean; fetchResponse?: {response?: Response}}>): void {
        const formElement = event.target;

        if (!(formElement instanceof HTMLFormElement) || !this.element.contains(formElement)) {
            return;
        }

        if (!event.detail.success) {
            return;
        }

        const contentType = event.detail.fetchResponse?.response?.headers.get('content-type') ?? '';

        if (!contentType.includes('turbo-stream')) {
            return;
        }

        this.close();
    }

    private updateTrigger(expanded: boolean): void {
        if (!this.hasTriggerTarget) {
            return;
        }

        const callback = (): void => {
            this.triggerTarget.setAttribute('aria-expanded', String(expanded));
        };

        if (this.dialogTarget.getAnimations().length === 0) {
            return callback();
        }

        this.dialogTarget.addEventListener('transitionend', callback, {once: true});
    }
}
