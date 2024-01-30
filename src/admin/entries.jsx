/**
 * WordPress dependencies
 */
import { createRoot, render, StrictMode } from '@wordpress/element';

const domElement = document.getElementById('page-content');

const App = () => {

	return (
		<div>
		  <h1>Entries</h1>
		</div>
	);
  
  };


if ( createRoot ) {
	createRoot( domElement ).render( <StrictMode><App /></StrictMode> );
} else {
	render( <StrictMode><App /></StrictMode>, domElement );
}