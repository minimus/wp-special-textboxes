import React, { useEffect } from 'react'
import { Route, Routes } from 'react-router-dom'
import CssBaseline from '@mui/material/CssBaseline'
import { useDispatch } from 'react-redux'
import Header from './containers/header/HeaderContainer'
import AppHeader from './components/header/AppHeader'
import Footer from './components/footer/Footer'
import Styles from './containers/styles/StylesContainer'
import Settings from './containers/settings/SettingsContainer'
import Loader from './components/Loader'
import { AdminGlobalStyles, MainContainer, MainRoot } from './styles'
import { getPluginOptions, getPluginSettings } from './redux/modules/settings/actions'
import type { TDispatch } from '../types/admin'
import Editor from './containers/editor/EditorContainer'
import { getLocalizationStrings } from './redux/modules/locales/actions'
import Themes from './containers/themes/ThemesContainer'

const Application = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()
	getPluginOptions()(dispatch)
	void getLocalizationStrings()(dispatch).then()

	useEffect(() => {
		void getPluginSettings()(dispatch).then()
	}, [dispatch])

	return (
		<MainContainer id="stb-admin-main">
			<CssBaseline />
			<AdminGlobalStyles />
			<Loader />
			<Header />
			<MainRoot>
				<AppHeader />
				<Routes>
					<Route path="/" element={<Styles />} />
					<Route path="/settings" element={<Settings />} />
					<Route path="/editor" element={<Editor newStyle />}>
						<Route path=":slug" element={<Editor />} />
					</Route>
					<Route path="/themes" element={<Themes />} />
				</Routes>
			</MainRoot>
			<Footer />
		</MainContainer>
	)
}

export default Application
