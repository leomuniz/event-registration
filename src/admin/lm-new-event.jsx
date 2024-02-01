/**
 * WordPress dependencies
 */
import { createRoot, render, StrictMode } from '@wordpress/element';

/**
 * Custom components
 */
import AdminLayout from '../components/admin-layout';
import EventForm from '../components/event-form';

const domElement = document.getElementById('page-content');

const PageContent = () => {

	return (
		<AdminLayout
			title="New Event"
			content={
				<EventForm />
			} 
		/>
	);
  
  };

if ( createRoot ) {
	createRoot( domElement ).render( <StrictMode><PageContent /></StrictMode> );
} else {
	render( <StrictMode><PageContent /></StrictMode>, domElement );
}