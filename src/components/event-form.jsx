import { useState } from 'react';
import { useDispatch } from '@wordpress/data';
import { Button, DatePicker, Form, Input, Switch } from 'antd';
import DynamicFormFields from './dynamic-form-fields';
import apiFetch from '@wordpress/api-fetch';
import { store as noticesStore } from '@wordpress/notices';
import dayjs from 'dayjs';

const { RangePicker } = DatePicker;
const { TextArea } = Input;

const EventForm = () => {

	const { scriptData } = window;

	const postContent = ( scriptData && scriptData.postData ) ?
		JSON.parse( scriptData.postData.post_content ) :
		{};

	console.log( postContent );

	const initialValues = 
		scriptData && scriptData.postData ? 
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

	const { createErrorNotice, createSuccessNotice, createInfoNotice, removeAllNotices } = useDispatch( noticesStore );

	const [loading, setLoading] = useState(false);

	const onFinish = async (values) => {

		removeAllNotices();
		setLoading( true );

		console.log( values );

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

			console.log( response );

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

	return (
		<Form
			onFinish={onFinish}
			labelCol={{ span: 6 }}
			wrapperCol={{ span: 14 }}
			labelWrap
			layout="horizontal"
			style={{ maxWidth: 920 }}
			scrollToFirstError
			initialValues={initialValues}
		>

			<Form.Item 
				name="eventName" 
				label="Event name" 
				rules={[{ required: true, message: 'Event name cannot be empty.' }]}
			>
				<Input />
			</Form.Item>

			<Form.Item name="eventPeriod" label="Event period">
				<RangePicker format='DD/MM/YYYY' />
			</Form.Item>

			<Form.Item name="eventRegistrationPeriod" label="Registration period">
				<RangePicker format='DD/MM/YYYY' />
			</Form.Item>

			<Form.Item name="eventRequireApproval" label="Require approval" defaultChecked={true}>
				<Switch />
			</Form.Item>

			<Form.Item name="eventSaveWpUsers" label="Create WP user for each registration" defaultChecked="true">
				<Switch />
			</Form.Item>

			<Form.Item label="Registration Form">
				<DynamicFormFields />
			</Form.Item>

			<Form.Item name="eventConfirmationEmailBody" label="Confirmation e-mail body">
				<TextArea rows={4} />
			</Form.Item>

			<Form.Item label="">
				<Button type="primary" htmlType="submit" loading={loading}>Save</Button>
			</Form.Item>
		</Form>
	);
};

export default EventForm;
