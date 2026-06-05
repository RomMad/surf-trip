import { startStimulusApp } from '@symfony/stimulus-bridge';
import AutoSubmit from '@stimulus-components/auto-submit';
import ReadMore from '@stimulus-components/read-more'


// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));
// register any custom, 3rd party controllers here
app.register('auto-submit', AutoSubmit);
app.register('read-more', ReadMore);
