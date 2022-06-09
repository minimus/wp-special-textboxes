import React from 'react'
import { useSelector } from 'react-redux'
import { Backdrop } from '@mui/material'
import Settings from '../../components/settings/Settings'
import type { IReducers } from '../../../types/state'

const SettingsContainer = (): JSX.Element => {
	const saving: boolean = useSelector((state: IReducers) => state.settings.saving)

	return (
		<>
			<Settings />
			<Backdrop open={saving} invisible sx={{ color: '#fff', zIndex: 1000 }} />
		</>
	)
}

export default SettingsContainer
