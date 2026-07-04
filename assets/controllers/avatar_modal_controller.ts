import {Controller} from '@hotwired/stimulus';

export default class extends Controller<HTMLElement> {
    static targets = ['frame'];

    static values = {
        formUrl: String,
    };

    declare readonly frameTarget: HTMLElement;
    declare readonly hasFrameTarget: boolean;
    declare readonly formUrlValue: string;

    loadForm(event: Event): void {
        const clickTarget = event.target;

        if (
            !(clickTarget instanceof Element)
            || !clickTarget.closest('[data-alert-dialog-target="trigger"]')
            || !this.hasFrameTarget
            || this.frameTarget.getAttribute('src')
        ) {
            return;
        }

        this.frameTarget.setAttribute('src', this.formUrlValue);
    }
}
