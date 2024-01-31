/**
 * WordPress dependencies
 */
import { createRoot, render, StrictMode } from '@wordpress/element';
import {
	Layout,
	theme,
	Typography,
	Button,
	Cascader,
	Checkbox,
	ColorPicker,
	DatePicker,
	Form,
	Input,
	InputNumber,
	Radio,
	Select,
	Slider,
	Switch,
	TreeSelect,
	Upload,
  } from 'antd';
  
const { RangePicker } = DatePicker;
const { TextArea } = Input;

const { Header, Content, Footer } = Layout;
const { Title } = Typography;

const domElement = document.getElementById('page-content');

const App = () => {

	const {
		token: { colorBgContainer, borderRadiusLG },
	} = theme.useToken();

	return (
		<>
			<Title level={3}>New Event</Title>
			<Layout
				style={{
				background: '#f0f0f1',
				}}
			>
				<Content>
					<div
					style={{
						background: colorBgContainer,
						minHeight: 360,
						padding: 24,
						borderRadius: borderRadiusLG,
					}}
					>
<Form
        labelCol={{ span: 4 }}
        wrapperCol={{ span: 14 }}
        layout="horizontal"
        style={{ maxWidth: 600 }}
      >
        <Form.Item label="Checkbox" name="disabled" valuePropName="checked">
          <Checkbox>Checkbox</Checkbox>
        </Form.Item>
        <Form.Item label="Radio">
          <Radio.Group>
            <Radio value="apple"> Apple </Radio>
            <Radio value="pear"> Pear </Radio>
          </Radio.Group>
        </Form.Item>
        <Form.Item label="Input">
          <Input />
        </Form.Item>
        <Form.Item label="Select">
          <Select>
            <Select.Option value="demo">Demo</Select.Option>
          </Select>
        </Form.Item>
        <Form.Item label="TreeSelect">
          <TreeSelect
            treeData={[
              { title: 'Light', value: 'light', children: [{ title: 'Bamboo', value: 'bamboo' }] },
            ]}
          />
        </Form.Item>
        <Form.Item label="Cascader">
          <Cascader
            options={[
              {
                value: 'zhejiang',
                label: 'Zhejiang',
                children: [
                  {
                    value: 'hangzhou',
                    label: 'Hangzhou',
                  },
                ],
              },
            ]}
          />
        </Form.Item>
        <Form.Item label="DatePicker">
          <DatePicker />
        </Form.Item>
        <Form.Item label="RangePicker">
          <RangePicker />
        </Form.Item>
        <Form.Item label="InputNumber">
          <InputNumber />
        </Form.Item>
        <Form.Item label="TextArea">
          <TextArea rows={4} />
        </Form.Item>
        <Form.Item label="Switch" valuePropName="checked">
          <Switch />
        </Form.Item>
        <Form.Item label="Button">
          <Button>Button</Button>
        </Form.Item>
        <Form.Item label="Slider">
          <Slider />
        </Form.Item>
        <Form.Item label="ColorPicker">
          <ColorPicker />
        </Form.Item>
      </Form>
					</div>
				</Content>
				<Footer
					style={{
					textAlign: 'center',
					background: '#f0f0f1',
					}}
				>
					Event Registration ©{new Date().getFullYear()} Created by Leo Muniz
				</Footer>
			</Layout>
		</>
	);
  
  };


if ( createRoot ) {
	createRoot( domElement ).render( <StrictMode><App /></StrictMode> );
} else {
	render( <StrictMode><App /></StrictMode>, domElement );
}