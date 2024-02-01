import { MinusCircleOutlined, PlusOutlined, UpCircleOutlined, DownCircleOutlined } from '@ant-design/icons';
import { Button, Checkbox, Form, Input, Select, Space } from 'antd';

const DynamicFormFields = () => {
	return (
		<>
			<Form.List name="event-form-fields">
				{(fields, { add, remove, move }) => (
					<>
					{fields.map(({ key, name, ...restField }, index) => (
						<Space key={key}
							style={{ display: 'flex' }}
							align="baseline"
						>

							<Form.Item
								{...restField}
								name={[name, 'event-form-field-names']}
								rules={[{required: true, message: 'Missing field name'}]}
							>
								<Input placeholder="Field Name" />
							</Form.Item>

							<Form.Item
								{...restField}
								name={[name, 'event-form-field-types']}
								rules={[{required: true, message: 'Missing field type'}]}
							>
								<Select
									placeholder="Field Type"
									defaultActiveFirstOption="true"
									style={{
										width: 120,
									}}
									options={[
										{ value: 'text', label: 'Text' },
										{ value: 'textArea', label: 'Text Area' },
										{ value: 'email', label: 'Email' },
										{ value: 'number', label: 'Number' },
										{ value: 'date', label: 'Date' },
										{ value: 'checkbox', label: 'Checkbox'},
									]}

								/>
							</Form.Item>

							<Form.Item
								{...restField}
								name={[name, 'event-form-fields-required']}
								valuePropName="checked" // Checkbox has no value prop, so we need to use valuePropName to set it to checked
							>
								<Checkbox>Required</Checkbox>
							</Form.Item>

							<Form.Item
								{...restField}
								name={[name, 'event-form-fields-unique']}
								valuePropName="checked" // Checkbox has no value prop, so we need to use valuePropName to set it to checked
							>
								<Checkbox>Unique</Checkbox>
							</Form.Item>

							<UpCircleOutlined
								onClick={() => move(index, index - 1)}
								style={{ color: index === 0 ? '#ccc' : undefined }}
							/>

							<DownCircleOutlined
								onClick={() => move(index, index + 1)} 
								style={{ color: index === ( fields.length - 1 ) ? '#ccc' : undefined }}
							/>

							<MinusCircleOutlined onClick={() => remove(name)} />
						</Space>
					))}
					<Form.Item>
						<Button type="dashed" onClick={() => add()} block icon={<PlusOutlined />}>
							Add field
						</Button>
					</Form.Item>
					</>
				)}
			</Form.List>
		</>
	);
};

export default DynamicFormFields;
