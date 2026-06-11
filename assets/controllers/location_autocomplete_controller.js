import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

export default class extends Controller {
    static targets = ['label', 'latitude', 'longitude', 'placeId'];

    static values = {
        apiKey: String,
        noResultsText: String,
    };

    connect() {
        this.tomSelect = new TomSelect(this.labelTarget, {
            valueField: 'label',
            labelField: 'label',
            searchField: ['label'],
            maxOptions: 8,
            closeAfterSelect: true,
            maxItems: 1,
            delimiter: ';',
            render: {
                no_results: (data, escape) =>
                    `<div class='no-results'>${this.noResultsTextValue} '${escape(data.input)}'</div>`,
            },
            load: (query, callback) => this._load(query, callback),
            onItemAdd: (value) => this._onItemAdd(value),
            shouldLoad: (query) => query.length >= 3,
        });
    }

    disconnect() {
        this.tomSelect?.destroy();
    }

    _load(query, callback) {
        const params = new URLSearchParams({
            access_token: this.apiKeyValue,
            autocomplete: 'true',
            limit: '6',
            language: document.documentElement.lang || 'en',
            types: 'address,place,locality,region,country',
        });
        const endpoint = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?${params.toString()}`;

        fetch(endpoint, {
            headers: {
                Accept: 'application/json',
            },
        })
            .then((response) => response.json())
            .then((payload) => {
                const features = Array.isArray(payload.features)
                    ? payload.features
                    : [];

                callback(features.map((feature) => ({
                    id: feature.id,
                    label: feature.place_name ?? feature.text ?? '',
                    longitude: feature.center?.[0] ?? null,
                    latitude: feature.center?.[1] ?? null,
                    placeId: feature.id ?? null,
                })));
            })
            .catch((error) => {
                console.error(error);
                callback();
            });
    }

    _onItemAdd(value) {
        const result = this.tomSelect.options[value];

        if (!result) {
            return;
        }

        this.latitudeTarget.value = result.latitude ?? '';
        this.longitudeTarget.value = result.longitude ?? '';
        this.placeIdTarget.value = result.placeId ?? '';
    }
}
