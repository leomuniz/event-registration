import { Button, DatePicker, Form, Input, Switch } from 'antd';
import DynamicFormFields from './dynamic-form-fields';
import apiFetch from '@wordpress/api-fetch';

const { RangePicker } = DatePicker;
const { TextArea } = Input;

const EventForm = () => {

	const onFinish = async (values) => {

		console.log( values );
		try {
			const response = await apiFetch({
				path: '/wp/v2/lm-events',
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				data: {
					'title' : values.eventName,
					'content' : JSON.stringify( values.eventFormFields ),
					'status': 'publish'
				}
			});

			if (response.ok) { // response.ok não existe hahah chatgpt filha da puta
				console.log('Event created successfully!');
				// Handle success, e.g., show a success message or redirect to a success page
			} else {
				console.error('Failed to create event:', response.statusText);
				// Handle error, e.g., show an error message
			}
		} catch (error) {
			console.error('An error occurred:', error);
			// Handle error, e.g., show an error message
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
		>

			<Form.Item 
				name="eventName" 
				label="Event name" 
				rules={[{ required: true, message: 'Event name cannot be empty.' }]}
			>
				<Input />
			</Form.Item>

			<Form.Item name="eventStartDate" label="Start date">
				<DatePicker format='DD/MM/YYYY' />
			</Form.Item>

			<Form.Item name="eventEndDate" label="End date">
				<DatePicker format='DD/MM/YYYY' />
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
				<Button type="primary" htmlType="submit">Save</Button>
			</Form.Item>
		</Form>
	);
};

export default EventForm;
