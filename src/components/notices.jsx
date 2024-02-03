/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';

import { Alert } from 'antd';

/**
 * Renders the notices fetching them from the noticeStore from @wordpress/notices.
 *
 * @return {string} HTML Notices.
 */
const Notices = () => {
	const { notices } = useSelect( ( select ) => ( {
		notices: select( noticesStore ).getNotices(),
	} ), [] );

	return (
		<>
			{ notices.map( ( notice ) => (
				<Alert key={ notice.id } message="${ notice.content }" type="${ notice.status }" />

			) ) }
		</>
	);
};

export default Notices;