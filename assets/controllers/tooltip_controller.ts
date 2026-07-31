import {Controller} from '@hotwired/stimulus';

type Side = 'top' | 'right' | 'bottom' | 'left';

export default class extends Controller<HTMLElement> {
    static values = {
        delayDuration: Number,
        wrapperSelector: String,
        contentSelector: String,
        arrowSelector: String,
    };

    static targets = ['trigger', 'wrapper'];

    declare readonly triggerTarget: HTMLElement;
    declare readonly wrapperTarget: HTMLElement;

    declare readonly hasDelayDurationValue: boolean;
    declare readonly delayDurationValue: number;

    declare readonly wrapperSelectorValue: string;
    declare readonly contentSelectorValue: string;
    declare readonly arrowSelectorValue: string;

    private initialized = false;
    private wrapperElement: HTMLElement | null = null;
    private contentElement: HTMLElement | null = null;
    private arrowElement: HTMLElement | null = null;

    private side: Side = 'top';
    private sideOffset = 0;

    private showTimeout: ReturnType<typeof setTimeout> | null = null;
    private hideTimeout: ReturnType<typeof setTimeout> | null = null;

    connect(): void {
        this.initialized = false;

        this.wrapperElement = document.querySelector<HTMLElement>( this.wrapperSelectorValue);
        this.contentElement = document.querySelector<HTMLElement>(this.contentSelectorValue);
        this.arrowElement = document.querySelector<HTMLElement>(this.arrowSelectorValue);

        if (!this.wrapperElement || !this.contentElement || !this.arrowElement) {
            return;
        }

        this.side = this.wrapperElement.getAttribute('data-side') as Side || 'top';
        this.sideOffset = parseInt( this.wrapperElement.getAttribute('data-side-offset') ?? '0', 10) || 0;

        this.showTimeout = null;
        this.hideTimeout = null;

        document.body.appendChild(this.wrapperElement);
        this.initialized = true;
    }

    disconnect(): void {
        this.clearTimeouts();

        if (this.wrapperElement && this.wrapperElement.parentNode === document.body) {
            this.element.appendChild(this.wrapperElement);
        }
    }

    wrapperTargetConnected(): void {
        // This case appears when the live component rerenders.
        // Because the original wrapper is moved to body, the Smart rerender
        // algorithm creates a new wrapper.
        if (this.wrapperElement) {
            this.wrapperElement.remove();
            this.connect();
        }
    }

    show(): void {
        if (!this.initialized) {
            return;
        }

        this.clearTimeouts();

        const delay = this.hasDelayDurationValue ? this.delayDurationValue : 0;

        this.showTimeout = setTimeout(() => {
            if ( !this.wrapperElement ||!this.contentElement || !this.arrowElement) {
                return;
            }

            this.wrapperElement.setAttribute('open', '');
            this.contentElement.setAttribute('open', '');
            this.arrowElement.setAttribute('open', '');

            this.positionElements();

            this.showTimeout = null;
        }, delay);
    }

    hide(): void {
        if (!this.initialized) {
            return;
        }

        this.clearTimeouts();

        this.wrapperElement?.removeAttribute('open');
        this.contentElement?.removeAttribute('open');
        this.arrowElement?.removeAttribute('open');
    }

    private clearTimeouts(): void {
        if (this.showTimeout !== null) {
            clearTimeout(this.showTimeout);
            this.showTimeout = null;
        }

        if (this.hideTimeout !== null) {
            clearTimeout(this.hideTimeout);
            this.hideTimeout = null;
        }
    }

    private positionElements(): void {
        if (!this.wrapperElement || !this.contentElement || !this.arrowElement) {
            return;
        }

        const triggerRect = this.triggerTarget.getBoundingClientRect();
        const contentRect = this.contentElement.getBoundingClientRect();
        const arrowRect = this.arrowElement.getBoundingClientRect();

        let wrapperLeft = 0;
        let wrapperTop = 0;
        let arrowLeft: number | null = null;
        let arrowTop: number | null = null;

        switch (this.side) {
            case 'left':
                wrapperLeft = triggerRect.left - contentRect.width - arrowRect.width / 2 - this.sideOffset;
                wrapperTop = triggerRect.top - contentRect.height / 2 + triggerRect.height / 2;
                arrowTop = contentRect.height / 2 - arrowRect.height / 2;
                break;
            case 'top':
                wrapperLeft = triggerRect.left - contentRect.width / 2 + triggerRect.width / 2;
                wrapperTop = triggerRect.top - contentRect.height - arrowRect.height / 2 - this.sideOffset;
                arrowLeft = contentRect.width / 2 - arrowRect.width / 2;
                break;
            case 'right':
                wrapperLeft = triggerRect.right + arrowRect.width / 2 + this.sideOffset;
                wrapperTop = triggerRect.top - contentRect.height / 2 + triggerRect.height / 2;
                arrowTop = contentRect.height / 2 - arrowRect.height / 2;
                break;
            case 'bottom':
                wrapperLeft = triggerRect.left - contentRect.width / 2 + triggerRect.width / 2;
                wrapperTop = triggerRect.bottom + arrowRect.height / 2 + this.sideOffset;
                arrowLeft = contentRect.width / 2 - arrowRect.width / 2;
                break;
        }

        this.wrapperElement.style.transform = `translate3d(${wrapperLeft}px, ${wrapperTop}px, 0)`;

        if (arrowLeft !== null) {
            this.arrowElement.style.left = `${arrowLeft}px`;
        }

        if (arrowTop !== null) {
            this.arrowElement.style.top = `${arrowTop}px`;
        }
    }
}
