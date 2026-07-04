import {Controller} from '@hotwired/stimulus';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.min.css';

export default class extends Controller<HTMLElement> {
    static values = {
        fileInputSelector: String,
        size: Number,
    };

    static targets = ['image', 'preview'];

    declare readonly fileInputSelectorValue: string;
    declare readonly sizeValue: number;
    declare readonly imageTarget: HTMLImageElement;
    declare readonly previewTarget: HTMLElement;

    private fileInput?: HTMLInputElement;
    private cropper?: Cropper;
    private objectUrl?: string;
    private isResubmitting = false;

    connect(): void {
        this.fileInput = this.element.querySelector(this.fileInputSelectorValue) ?? undefined;

        this.fileInput?.addEventListener('change', this.onFileChange);

        this.displayPreview();
    }

    async onSubmit(event: SubmitEvent): Promise<void> {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        if (this.isResubmitting) {
            this.isResubmitting = false;

            return;
        }

        const originalFile = this.fileInput?.files?.item(0);

        if (!originalFile || !this.cropper || !this.fileInput) {
            return;
        }

        event.preventDefault();

        const croppedFile = await this.createCroppedFile(originalFile);

        if (!croppedFile) {
            form.requestSubmit();

            return;
        }

        const dataTransfer = new DataTransfer();

        dataTransfer.items.add(croppedFile);
        this.fileInput.files = dataTransfer.files;

        this.isResubmitting = true;
        form.requestSubmit();
    }

    disconnect(): void {
        this.fileInput?.removeEventListener('change', this.onFileChange);

        this.cropper?.destroy();
        this.revokeObjectUrl();
    }

    private onFileChange = (): void => {
        const file = this.fileInput?.files?.item(0);

        if (!file) {
            this.cropper?.destroy();
            this.cropper = undefined;
            this.imageTarget.removeAttribute('src');
            this.displayPreview();

            return;
        }

        this.revokeObjectUrl();

        this.objectUrl = URL.createObjectURL(file);
        this.initCropper(this.objectUrl);
    };

    private initCropper(imageUrl: string): void {
        this.imageTarget.src = imageUrl;
        this.cropper?.destroy();

        this.cropper = new Cropper(this.imageTarget, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
        });

        this.displayPreview();
    }

    private async createCroppedFile(originalFile: File): Promise<File | null> {
        const canvas = this.cropper?.getCroppedCanvas({
            width: this.sizeValue,
            height: this.sizeValue,
        });

        if (!canvas) {
            return null;
        }

        const blob = await new Promise<Blob | null>((resolve) => {
            canvas.toBlob((value: Blob | null) => resolve(value), originalFile.type || 'image/jpeg');
        });

        if (!blob) {
            return null;
        }

        return new File([blob], originalFile.name, {type: blob.type || originalFile.type});
    }

    private displayPreview(): void {
        this.previewTarget.classList.toggle('hidden', this.imageTarget.src === '');
    }

    private revokeObjectUrl(): void {
        if (!this.objectUrl) {
            return;
        }

        URL.revokeObjectURL(this.objectUrl);
        this.objectUrl = undefined;
    }
}
