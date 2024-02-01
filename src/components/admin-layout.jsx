import { Layout, theme, Typography, DatePicker, Input } from 'antd';
  
const { Header, Content, Footer } = Layout;
const { Title } = Typography;

const AdminLayout = ({ title, content }) => {

	const {
		token: { colorBgContainer, borderRadiusLG },
	} = theme.useToken();

	return (
		<>
			<Title level={1} className="wp-heading-inline">{title}</Title>
			<Layout
				style={{
					background: '#f0f0f1',
					marginTop: '10px',
				}}
			>
				<Content>
					<div
					style={{
						background: colorBgContainer,
						minHeight: 360,
						padding: 24,
						borderRadius: borderRadiusLG
					}}
					>
						{content}
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

export default AdminLayout;
