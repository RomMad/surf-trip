import {Controller} from '@hotwired/stimulus';
import TomSelect from 'tom-select';

interface LocationOption {
    id: string;
    label: string;
    longitude: number | null;
    latitude: number | null;
    placeId: string | null;
}

interface MapboxFeature {
    id: string;
    text?: string;
    place_name?: string;
    center?: [number, number];
}

interface MapboxResponse {
    features?: MapboxFeature[];
}

export default class extends Controller<HTMLElement> {
    static targets = ['label', 'latitude', 'longitude', 'placeId'];

    static values = {
        apiKey: String,
        noResultsText: String,
    };

    private readonly url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/';

    declare readonly labelTarget: HTMLInputElement;
    declare readonly latitudeTarget: HTMLInputElement;
    declare readonly longitudeTarget: HTMLInputElement;
    declare readonly placeIdTarget: HTMLInputElement;

    declare readonly apiKeyValue: string;
    declare readonly noResultsTextValue: string;

    private tomSelect?: TomSelect;

    connect(): void {
        this.tomSelect = new TomSelect(this.labelTarget, {
            valueField: 'label',
            labelField: 'label',
            searchField: ['label'],
            maxOptions: 8,
            closeAfterSelect: true,
            maxItems: 1,
            delimiter: ';',

            render: {
                no_results: (
                    data: {input: string},
                    escape: (value: string) => string,
                ): string =>
                    `<div class="no-results">${this.noResultsTextValue} '${escape(data.input)}'</div>`,
            },
            shouldLoad: (query: string): boolean => query.length >= 3,
            load: (
                query: string,
                callback: (options?: LocationOption[]) => void,
            ): void => void this.load(query, callback),
            onItemAdd: (value: string): void => this.onItemAdd(value),
        });
    }

    disconnect(): void {
        this.tomSelect?.destroy();
    }

    private async load(
        query: string,
        callback: (options?: LocationOption[]) => void,
    ): Promise<void> {
        const params = new URLSearchParams({
            access_token: this.apiKeyValue,
            autocomplete: 'true',
            limit: '6',
            language: document.documentElement.lang || 'en',
            types: 'address,place,locality,region,country',
        });

        const endpoint = `${this.url}${encodeURIComponent(query)}.json?${params.toString()}`;

        try {
            const response = await fetch(endpoint, {
                headers: {
                    Accept: 'application/json',
                },
            });

            const payload: MapboxResponse = await response.json();

            callback(
                (payload.features ?? []).map((feature) => ({
                    id: feature.id,
                    label: feature.place_name ?? feature.text ?? '',
                    longitude: feature.center?.[0] ?? null,
                    latitude: feature.center?.[1] ?? null,
                    placeId: feature.id ?? null,
                })),
            );
        } catch (error) {
            console.error(error);
            callback();
        }
    }

    private onItemAdd(value: string): void {
        const result = this.tomSelect?.options[value] as
            | LocationOption
            | undefined;

        if (!result) {
            return;
        }

        this.latitudeTarget.value = String(result.latitude ?? '');
        this.longitudeTarget.value = String(result.longitude ?? '');
        this.placeIdTarget.value = result.placeId ?? '';
    }
}
