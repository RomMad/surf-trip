import {Controller} from '@hotwired/stimulus';

export default class extends Controller<HTMLElement> {
    static values = {
        inputSelector: String,
    };

    static targets = ['overlay'];

    declare readonly inputSelectorValue: string;
    declare readonly overlayTarget: HTMLElement;

    private input!: HTMLInputElement;
    private dragCounter: number = 0;

    connect(): void {
        const input = this.element.querySelector<HTMLInputElement>(this.inputSelectorValue);

        if (!(input instanceof HTMLInputElement)) {
            throw new Error(`Input "${this.inputSelectorValue}" not found in dropzone controller`);
        }

        this.input = input;

        this.element.addEventListener('dragenter', this.onDragEnter);
        this.element.addEventListener('dragover', this.onDragOver);
        this.element.addEventListener('dragleave', this.onDragLeave);
        this.element.addEventListener('drop', this.onDrop);
    }

    disconnect(): void {
        this.dragCounter = 0;
        this.hideDropzone();

        this.element.removeEventListener('dragenter', this.onDragEnter);
        this.element.removeEventListener('dragover', this.onDragOver);
        this.element.removeEventListener('dragleave', this.onDragLeave);
        this.element.removeEventListener('drop', this.onDrop);
    }

    private readonly onDragEnter = (event: DragEvent): void => {
        if (!this.isFileDragEvent(event)) return;

        event.preventDefault();

        this.dragCounter++;

        if (this.dragCounter === 1) {
            this.displayDropzone();
        }
    };

    private readonly onDragOver = (event: DragEvent): void => {
        if (!this.isFileDragEvent(event)) return;

        event.preventDefault();

        event.dataTransfer!.dropEffect = 'copy';
    };

    private readonly onDragLeave = (event: DragEvent): void => {
        if (!this.isFileDragEvent(event)) return;

        event.preventDefault();

        this.dragCounter--;

        if (this.dragCounter <= 0) {
            this.dragCounter = 0;
            this.hideDropzone();
        }
    };

    private readonly onDrop = (event: DragEvent): void => {
        event.preventDefault();

        this.dragCounter = 0;
        this.hideDropzone();

        const files = event.dataTransfer?.files;

        if (!files?.length) return;

        const file = files[0];

        if (!file.type.startsWith('image/')) return;

        const dataTransfer = new DataTransfer();

        dataTransfer.items.add(file);

        this.input.files = dataTransfer.files;
        this.input.dispatchEvent(new Event('change', {bubbles: true}));
    };

    private isFileDragEvent(event: DragEvent): boolean {
        return event.dataTransfer?.types.includes('Files') ?? false;
    }

    private displayDropzone(): void {
        this.element.classList.add('dropzone--drag-over');
        this.overlayTarget.classList.remove('hidden');
        this.overlayTarget.classList.add('flex');
    }

    private hideDropzone(): void {
        this.element.classList.remove('dropzone--drag-over');
        this.overlayTarget.classList.add('hidden');
        this.overlayTarget.classList.remove('flex');
    }
}
