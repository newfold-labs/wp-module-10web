import posthog from 'posthog-js';

/**
 * Initialize PostHog for session replays.
 */
posthog.init(
	'phc_6wQThygzyWKFpi6f5MItEjm4qVqcigezljk7orxhpUi', // public facing api key has write only access - this is not a sensitive key
	{
		api_host: 'https://us.i.posthog.com',
		defaults: '2026-05-30',
		session_recording: {
			maskAllInputs: false,
		},
		enable_recording_console_log: true,
		capture_exceptions: true,
		// debug: process.env.NODE_ENV === 'development'
	}
);
