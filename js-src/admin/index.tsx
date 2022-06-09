import React from 'react'
import { createRoot } from 'react-dom/client'
import { SnackbarProvider } from 'notistack'
import { Provider } from 'react-redux'
import { HashRouter as Router } from 'react-router-dom'
import Application from './Application'
import createStore from './redux'

const store = createStore({})

const container = document.getElementById('stb-admin-container')
const root = createRoot(container)

root.render(
	<SnackbarProvider maxSnack={3}>
		<Provider store={store}>
			<Router>
				<Application />
			</Router>
		</Provider>
	</SnackbarProvider>,
)
