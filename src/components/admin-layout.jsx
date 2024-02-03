import { Layout, theme, Typography, Alert } from 'antd';
import { useSelect } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';

const { Content, Footer } = Layout;
const { Title } = Typography;

const AdminLayout = ({ title, content }) => {

	const { notices } = useSelect( ( select ) => ( {
		notices: select( noticesStore ).getNotices(),
	} ), [] );

	const {
		token: { colorBgContainer, borderRadiusLG },
	} = theme.useToken();

	return (
		<>
			<Title level={1} className="wp-heading-inline">{title}</Title>

			{ notices.map( ( notice ) => (
				<Alert key={ notice.ID }  message={ notice.content } type={ notice.status } />
			) ) }

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
