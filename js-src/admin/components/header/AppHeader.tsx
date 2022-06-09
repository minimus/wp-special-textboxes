import React from 'react'
import { useLocation } from 'react-router-dom'
import Box from '@mui/material/Box'
import AppBar from '@mui/material/AppBar'
import Toolbar from '@mui/material/Toolbar'
import Typography from '@mui/material/Typography'
import { useSelector } from 'react-redux'
import AppMenu from './AppMenu'
import type { ILocale } from '../../../types/admin'
import type { IReducers } from '../../../types/state'

const AppHeader = (): JSX.Element => {
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)
	const { appHeader: { styles = '', settings = '', editor = '', themes = '' } = {} } = localesData ?? {}

	const location = useLocation()
	const { pathname } = location

	const pageName = (path: string) => {
		if (path === '/') {
			return styles
		}
		if (path === '/settings') {
			return settings
		}
		if (/\/editor.*/i.test(path)) {
			return editor
		}
		if (/\/themes.*/i.test(path)) {
			return themes
		}
		return 'WTF'
	}

	return (
		<Box sx={{ flexGrow: 1 }}>
			<AppBar position="static">
				<Toolbar>
					<AppMenu />
					<Typography variant="h6" sx={{ flexGrow: 1 }}>
						{pageName(pathname)}
					</Typography>
				</Toolbar>
			</AppBar>
		</Box>
	)
}

export default AppHeader
