
import apiFetch from '@wordpress/api-fetch';
import { useDispatch } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';
import dayjs from 'dayjs';

/**
 * useSubmitForm Hook.
 * 
 * @returns {Function} A function that submits the form data to the server.
 */
export const useSubmitForm = () => {

	const { createErrorNotice, createSuccessNotice, removeAllNotices } = useDispatch( noticesStore );
	const { scriptData } = window;

	return async (values, setLoading) => {

		removeAllNotices();
		setLoading( true );

		const apiPath = scriptData && scriptData.postData && scriptData.postData.ID
		? `/wp/v2/lm-events/${scriptData.postData.ID}`
		: '/wp/v2/lm-events';

		try {
			const response = await apiFetch({
				path: apiPath,
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				data: {
					'title' : values.eventName,
					'content' : JSON.stringify( 
						{
							eventPeriod: values.eventPeriod,
							registrationPeriod: values.eventRegistrationPeriod,
							requireApproval: values.eventRequireApproval,
							saveWPUsers: values.eventSaveWpUsers,
							fields: values.eventFormFields,
							emailBody: values.eventConfirmationEmailBody,
						}
					),
					'status': 'publish'
				}
			});

			setLoading( false );

			window.scrollTo( { top: 0, behavior: 'smooth' } );

			if ( response.id ) {
				const noticeMsg = 
					scriptData && scriptData.postData ?
					'Your event was updated successfully.' :
					'Your event was created successfully.';

				createSuccessNotice( noticeMsg );
			} else {
				createErrorNotice( 'It was not possible to create your event. Please try again later or contact support.' );
			}
		} catch (error) {
			createErrorNotice( 'It was not possible to create your event. Please try again later or contact support.' );
		}
	};
};

/*
 * getInitialValues Function.
 * 
 * @returns {Object} The initial values for the form.
 */
export const getInitialValues = () => {
	const { scriptData } = window;
	const postContent = ( scriptData && scriptData.postData ) ?
		JSON.parse( scriptData.postData.post_content ) :
		{};

	return scriptData && scriptData.postData ? 
		{
			eventName: scriptData.postData.post_title,
			eventPeriod: postContent && postContent.eventPeriod ? 
				[
					dayjs( postContent.eventPeriod[0] ),
					dayjs( postContent.eventPeriod[1] ),
				] : 
				null,
			eventRegistrationPeriod: postContent && postContent.registrationPeriod ? 
				[
					dayjs( postContent.registrationPeriod[0] ),
					dayjs( postContent.registrationPeriod[1] ),
				] :
				null,
			eventRequireApproval: postContent.requireApproval,
			eventSaveWpUsers: postContent.saveWPUsers,
			eventFormFields: postContent.fields,
			eventConfirmationEmailBody: postContent.emailBody,
		} :
		{};
}