import {Controller} from '@hotwired/stimulus';
import ChartDataLabels from 'chartjs-plugin-datalabels';

const CHART1_COLOR_KEY = '--chart-1';
const CHART2_COLOR_KEY = '--chart-2';
let isChartDataLabelsRegistered = false;

export default class extends Controller<HTMLElement> {
    connect() {
        this.element.addEventListener('chartjs:init', this.onChartInit);
        this.element.addEventListener('chartjs:pre-connect', this.onPreConnect);
    }

    disconnect() {
        this.element.removeEventListener('chartjs:init', this.onChartInit);
        this.element.removeEventListener('chartjs:pre-connect', this.onPreConnect);
    }

    onChartInit = (event: EventModifierInit) => {
        const customEvent = event as CustomEvent;

        if (isChartDataLabelsRegistered) {
            return;
        }

        customEvent.detail.Chart.register(ChartDataLabels);
        isChartDataLabelsRegistered = true;
    };

    onPreConnect = (event: Event) => {
        const customEvent = event as CustomEvent;
        const chart1Color = getComputedStyle(this.element).getPropertyValue(CHART1_COLOR_KEY).trim();
        const chart2Color = getComputedStyle(this.element).getPropertyValue(CHART2_COLOR_KEY).trim();

        for (const dataset of customEvent.detail.config.data.datasets ?? []) {
            if (chart1Color && dataset.backgroundColor === CHART1_COLOR_KEY) {
                dataset.backgroundColor = chart1Color;
            }

            if (chart1Color && dataset.borderColor === CHART1_COLOR_KEY) {
                dataset.borderColor = chart1Color;
            }

            if (chart2Color && dataset.backgroundColor === CHART2_COLOR_KEY) {
                dataset.backgroundColor = chart2Color;
            }

            if (chart2Color && dataset.borderColor === CHART2_COLOR_KEY) {
                dataset.borderColor = chart2Color;
            }
        }
    };
}
