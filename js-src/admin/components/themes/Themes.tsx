import React from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { useSnackbar } from 'notistack'
import { TDispatch, TSettings, TThemeInfo } from '../../../types/admin'
import { IReducers } from '../../../types/state'
import { Root } from './styles'
import ThemeItem from './components/ThemeItem'
import { activateTheme } from '../../redux/modules/themes/actions'

const Themes = (): JSX.Element => {
	const dispatch: TDispatch = useDispatch()
	const { enqueueSnackbar } = useSnackbar()

	const themes: TThemeInfo[] = useSelector((state: IReducers) => state.themes.themes) ?? []
	const settings: TSettings = useSelector((state: IReducers) => state.settings.settings)

	const onActivateClick = (slug: string): void => {
		activateTheme(slug)(dispatch)
			.then(() => {
				enqueueSnackbar('Theme are successfully activated!', { variant: 'success' })
			})
			.catch(() => {
				enqueueSnackbar('An error occurred during activating theme...', { variant: 'error' })
			})
	}

	return (
		<Root>
			{themes.map((item: TThemeInfo) => (
				<ThemeItem
					key={item.slug}
					name={item.name}
					slug={item.slug}
					description={item.description}
					image={item.image}
					active={item.slug === settings.themeName}
					onActivateClick={onActivateClick}
				/>
			))}
		</Root>
	)
}

export default Themes
