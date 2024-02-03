import { useState } from 'react';
import { Button, DatePicker, Form, Input, Switch } from 'antd';
import DynamicFormFields from './dynamic-form-fields';
import { getInitialValues, useSubmitForm } from './event-form-actions';

const { RangePicker } = DatePicker;
const { TextArea } = Input;

const EventForm = () => {

	const [isLoading, setLoading] = useState(false);

	const initialValues = getInitialValues();

	const submitForm = useSubmitForm();
	const onFinish = (values) => {
		submitForm( values, setLoading ); // form processing data located at event-form-actions.jsx
	}

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
				<Button type="primary" htmlType="submit" loading={isLoading}>Save</Button>
			</Form.Item>
		</Form>
	);
};

export default EventForm;
