import React, { useState, MouseEvent } from 'react'
import IconButton from '@mui/material/IconButton'
import MenuIcon from '@mui/icons-material/Menu'
import Menu from '@mui/material/Menu'
import MenuItem from '@mui/material/MenuItem'
import Link from '@mui/material/Link'
import { Link as RouterLink, useLocation } from 'react-router-dom'
import { useSelector } from 'react-redux'
import type { ILocale } from '../../../types/admin'
import type { IReducers } from '../../../types/state'

type TMenuItem = {
	title: string
	link: string
}

const AppMenu = (): JSX.Element => {
	const localesData: ILocale = useSelector((state: IReducers) => state.locales.data)

	const [anchorEl, setAnchorEl] = useState<Element>(null)
	const { menu: { styles = '', newStyle = '', settings = '', themes = '' } = {} } = localesData ?? {}

	const location = useLocation()
	const { pathname } = location

	const menu: TMenuItem[] = [
		{ title: styles, link: '/' },
		{ title: newStyle, link: '/editor' },
		{ title: settings, link: '/settings' },
		{ title: themes, link: '/themes' },
	]

	const handleClick = (event: MouseEvent<HTMLButtonElement>): void => {
		setAnchorEl(event.currentTarget)
	}

	const handleClose = () => {
		setAnchorEl(null)
	}

	return (
		<>
			<IconButton
				size="large"
				edge="start"
				color="inherit"
				aria-label="Menu"
				sx={{ mr: 2 }}
				onClick={handleClick}
			>
				<MenuIcon />
			</IconButton>
			<Menu id="simple-menu" anchorEl={anchorEl} keepMounted open={!!anchorEl} onClose={handleClose}>
				{menu.map((item: TMenuItem, idx: number) => (
					<MenuItem key={idx.toString()} disabled={item.link === pathname} onClick={handleClose}>
						<Link
							key={idx.toString()}
							component={RouterLink}
							to={item.link}
							underline="none"
							color="textPrimary"
						>
							{item.title}
						</Link>
					</MenuItem>
				))}
			</Menu>
		</>
	)
}

export default AppMenu
