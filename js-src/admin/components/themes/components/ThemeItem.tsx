import React from 'react'
import { Typography } from '@mui/material'
import { Bolt as BoltIcon } from '@mui/icons-material'
import { useDispatch, useSelector } from 'react-redux'
import { ItemInfo, ItemRoot } from '../styles'
import { ProgressFab } from '@minimus/simplelib-ui-kit'
import { IReducers } from '../../../../types/state'
import { TDispatch } from '../../../../types/admin'
import { THEMES_ACTIVATE_THEME, STOP } from '../../../redux/constants'

type TProps = {
	name: string
	slug: string
	description: string
	image: string
	active: boolean
	onActivateClick: (string) => void
}

const ThemeItem = (props: TProps): JSX.Element => {
	const dispatch: TDispatch = useDispatch()

	const { name, slug, description, image, active, onActivateClick } = props

	const activationSlug: string = useSelector((state: IReducers) => state.themes.activatingSlug)
	const loading: boolean = useSelector((state: IReducers) => state.themes.loading)
	const activationError: boolean = useSelector((state: IReducers) => state.themes.activationError)
	const loaded: boolean = useSelector((state: IReducers) => state.themes.loaded)

	const onClick = () => {
		if (onActivateClick) {
			onActivateClick(slug)
		}
	}

	const onDelay = () => {
		dispatch({ type: THEMES_ACTIVATE_THEME + STOP })
	}

	return (
		<ItemRoot image={image} isActive={active}>
			<ItemInfo>
				<Typography variant="body1">{name}</Typography>
				<Typography variant="body2">{description}</Typography>
				{!active && (
					<ProgressFab
						processing={slug === activationSlug && loading}
						success={slug === activationSlug && loaded}
						error={slug === activationSlug && activationError}
						color="primary"
						displayPosition={{ position: 'absolute', right: 15, bottom: 15 }}
						delay={3000}
						onClick={onClick}
						onDelay={onDelay}
					>
						<BoltIcon />
					</ProgressFab>
				)}
			</ItemInfo>
		</ItemRoot>
	)
}

export default ThemeItem
